/**
 * Run task by worker thread
 * $Id$
 */
#ifdef HAVE_CONFIG_H
#    include "config.h"
#endif

#include <string>
#include <set>

#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <poll.h>
#include <xapian.h>

#include "log.h"
#include "conn.h"
#include "global.h"
#include "task.h"
#include "pinyin.h"

/**
 * Reset debug log macro to contiain tid
 */
#ifdef DEBUG
#    undef	log_debug
#    undef	log_debug_conn
#    define	log_debug(fmt, ...)			log_printf("[%s:%d] [thr:%p] " fmt, \
		__FILE__, __LINE__, pthread_self(), ##__VA_ARGS__)
#    define	log_debug_conn(fmt, ...)	log_printf("[%s:%d] [sock:%d] [thr:%p] " fmt, \
		__FILE__, __LINE__, CONN_FD(), pthread_self(), ##__VA_ARGS__)
#endif	/* DEBUG */

/**
 * Extern global variables
 */
extern Xapian::Stem stemmer;
extern Xapian::SimpleStopper stopper;
using std::string;

/**
 * Local static variables
 */
#define	QUERY_OP_NUM	6
static int query_ops[] = {
	Xapian::Query::OP_AND,
	Xapian::Query::OP_OR,
	Xapian::Query::OP_AND_NOT,
	Xapian::Query::OP_XOR,
	Xapian::Query::OP_AND_MAYBE,
	Xapian::Query::OP_FILTER,
	NULL
};

/**
 * Data structure for zcmd_exec
 */
enum object_type
{
	OTYPE_DB,
	OTYPE_RANGER
};

struct object_chain
{
	enum object_type type;
	char *key;
	void *val;
	struct object_chain *next;
};

struct search_zarg
{
	Xapian::Database *db;
	Xapian::Enquire *eq;
	Xapian::Query *qq;
	Xapian::QueryParser *qp;

	unsigned int parse_flag;
	unsigned int db_total;
	unsigned char cuts[XS_DATA_VNO + 1]; // 0x80(numeric)|(cut_len/10)

	struct object_chain *objs;
};

struct result_doc
{
	unsigned int docid; // Xapian::docid
	unsigned int rank;
	unsigned int ccount;
	int percent;
	float weight;
};

#define	DELETE_PTR(p)		do { if (p != NULL) { delete p; p = NULL; } } while(0)
#define	DELETE_PTT(p, t)	do { if (p != NULL) { delete (t)p; p = NULL; } } while(0)
#define	GET_SCALE(b)		(double)(b[0]<<8|b[1])/100
#define	GET_QUERY_OP(a)		(Xapian::Query::op)query_ops[a % QUERY_OP_NUM]

#define	CACHE_NONE			0
#define	CACHE_USE			1	// cache was used
#define	CACHE_FOUND			2	// cache found
#define	CACHE_VALID			4	// cache valid
#define	CACHE_NEED			8	// is cache need (for big object)

#ifdef HAVE_MEMORY_CACHE
#    include "global.h"
#    include "mcache.h"
#    include "md5.h"

extern MC *mc;

struct cache_result
{
	unsigned int total; // document total on caching
	unsigned int count; // matched count
	struct result_doc doc[MAX_SEARCH_RESULT];
};

struct cache_count
{
	unsigned int total; // document total on caching
	unsigned int count; // matched count
};

#    define	C_LOCK_CACHE()		G_LOCK_CACHE(); conn->flag |= CONN_FLAG_CACHE_LOCKED
#    define	C_UNLOCK_CACHE()	G_UNLOCK_CACHE(); conn->flag ^= CONN_FLAG_CACHE_LOCKED

#endif	/* HAVE_MEMORY_CACHE */

/**
 * Cut longer string or convert serialise string into numeric
 * @param s string
 * @param v int (char)
 * @param m MsetIterator
 * @param z search_zarg
 */
static inline void cut_matched_string(string &s, int v, unsigned int id, struct search_zarg *z)
{
	int cut = (int) z->cuts[v];
	if (cut & CMD_VALUE_FLAG_NUMERIC)
	{
		// convert to numeric string
		char buf[64];
		buf[63] = '\0';
		snprintf(buf, sizeof(buf) - 1, "%g", Xapian::sortable_unserialise(s));
		s = string(buf);
	}
	else
	{
		cut = (cut & (CMD_VALUE_FLAG_NUMERIC - 1)) * 10;
		if (cut != 0 && s.size() > cut)
		{
			int i;
			const char *ptr;
			string tt = string("");
			Xapian::TermIterator tb = z->eq->get_matching_terms_begin(id);
			Xapian::TermIterator te = z->eq->get_matching_terms_end(id);

			// get longest matched term
			while (tb != te)
			{
				string tm = *tb;
				ptr = tm.data();
				if (prefix_to_vno((char *) ptr) == v)
				{
					for (i = 0; ptr[i] >= 'A' && ptr[i] <= 'Z'; i++);
					if (i > 0) tm = tm.substr(i);
					if (tm.size() > tt.size()) tt = tm;
				}
				tb++;
			}

			// get start pos
			i = 0;
			if (tt.size() > 0)
			{
				ptr = strcasestr(s.data(), tt.data());
				if (ptr != NULL)
				{
					i = ptr - s.data() - (cut >> 2);
					if (i < 0) i = 0;
				}
			}

			ptr = s.data() + i;
			if (i == 0) tt = string("");
			else
			{
				tt = string("...");
				while ((*ptr & 0xc0) == 0x80)
				{
					ptr++;
					i++;
				}
			}
			if ((i + cut) >= s.size())
				tt += s.substr(i);
			else
			{
				while (cut > 0 && (ptr[cut] & 0xc0) == 0x80) cut--;
				tt += s.substr(i, cut);
				tt += string("...");
			}
			s = tt;
		}
	}
}

/**
 * Get query object from CMD
 */
#define	FETCH_CMD_QUERY(q)		do {															\
	if (XS_CMD_BLEN(cmd) == 0) q = Xapian::Query(*zarg->qq);									\
	else {																						\
		string qstr = string(XS_CMD_BUF(cmd), XS_CMD_BLEN(cmd));								\
		int flag = zarg->parse_flag > 0 ? zarg->parse_flag : Xapian::QueryParser::FLAG_DEFAULT;	\
		zarg->qp->set_default_op(GET_QUERY_OP(cmd->arg2));										\
		q = zarg->qp->parse_query(qstr, flag);													\
	}																							\
} while(0)

/**
 * Send a document to client
 * @param conn (XS_CONN *)
 * @param rd (struct result_doc *)
 */
static int send_result_doc(XS_CONN *conn, struct result_doc *rd)
{
	int rc;

	// send the doc header
	log_debug_conn("send search result document (ID:%u, PERCENT:%d%%)", rd->docid, rd->percent);
	rc = conn_respond(conn, CMD_SEARCH_RESULT_DOC, 0, (char *) rd, sizeof(struct result_doc));
	if (rc == CMD_RES_CONT)
	{
		// send the data (body, check to cut)
		string data;
		int vno;
		struct search_zarg *zarg = (struct search_zarg *) conn->zarg;
		Xapian::Document d = zarg->db->get_document(rd->docid);
		Xapian::ValueIterator v = d.values_begin();

		// send other fields (value)
		while (v != d.values_end() && rc == CMD_RES_CONT)
		{
			vno = v.get_valueno();
			data = *v++;

			cut_matched_string(data, vno, rd->docid, (struct search_zarg *) conn->zarg);
			rc = conn_respond(conn, CMD_SEARCH_RESULT_FIELD, vno, data.data(), data.size());
		}

		// send data (body)
		data = d.get_data();
		vno = XS_DATA_VNO;

		cut_matched_string(data, vno, rd->docid, (struct search_zarg *) conn->zarg);
		rc = conn_respond(conn, CMD_SEARCH_RESULT_FIELD, vno, data.data(), data.size());
	}
	return rc;
}

/**
 * Add pointer into zarg
 * @param zarg
 */
static void zarg_add_object(struct search_zarg *zarg, enum object_type type, const char *key, void *val)
{
	struct object_chain *oc;

	// TODO: check return value of malloc()
	oc = (struct object_chain *) malloc(sizeof(struct object_chain));
	oc->type = type;
	oc->key = key == NULL ? NULL : strdup(key);
	oc->val = val;
	oc->next = zarg->objs;
	zarg->objs = oc;
}

/**
 * Get pointer from zarg
 * @param zarg
 */
static void *zarg_get_object(struct search_zarg *zarg, enum object_type type, const char *key)
{
	struct object_chain *oc = zarg->objs;
	while (oc != NULL)
	{
		if (oc->type == type
			&& ((oc->key == NULL && key == NULL) || !strcmp(oc->key, key)))
		{
			return oc->val;
		}
		oc = oc->next;
	}
	return NULL;
}

/**
 * Free zarg pointers
 * @param zarg
 */
static inline void zarg_cleanup(struct search_zarg *zarg)
{
	struct object_chain *oc;
	while ((oc = zarg->objs) != NULL)
	{
		zarg->objs = oc->next;
		if (oc->type == OTYPE_DB)
		{
			log_debug("delete (Xapian::Database *)%s: %p", oc->key, oc->val);
			DELETE_PTT(oc->val, Xapian::Database *);
		}
		else if (oc->type == OTYPE_RANGER)
		{
			log_debug("delete (Xapian::ValueRangeProcessor *): %p", oc->val);
			DELETE_PTT(oc->val, Xapian::ValueRangeProcessor *);
		}
		if (oc->key != NULL) free(oc->key);
		free(oc);
	}
	DELETE_PTR(zarg->eq);
	DELETE_PTR(zarg->qp);
	DELETE_PTR(zarg->qq);
	DELETE_PTR(zarg->db);
}

/**
 * task zcmd command handler
 * @param conn
 * @return CMD_RES_CONT/CMD_RES_NEXT/CMD_RES_xxx
 */
static int zcmd_task_default(XS_CONN *conn)
{
	int rc = CMD_RES_CONT;
	XS_CMD *cmd = conn->zcmd;
	struct search_zarg *zarg = (struct search_zarg *) conn->zarg;

	switch (cmd->cmd)
	{
		case CMD_USE:
			rc = CONN_RES_ERR(WRONGPLACE);
			break;
		case CMD_SEARCH_FINISH:
			if ((rc = CONN_RES_OK(FINISHED)) == CMD_RES_CONT)
				rc = CMD_RES_PAUSE;
			break;
		case CMD_SEARCH_DB_TOTAL:
			// get total doccount of DB
			if (zarg->db == NULL)
				rc = CONN_RES_ERR(NODB);
			else
				rc = CONN_RES_OK3(DB_TOTAL, (char *) &zarg->db_total, sizeof(zarg->db_total));
			break;
		case CMD_SEARCH_ADD_LOG:
			if (XS_CMD_BLEN(cmd) > MAX_QUERY_LENGTH)
				rc = CONN_RES_ERR(TOOLONG);
			else
			{
				char fpath[256];
				FILE *fp;

				sprintf(fpath, "%s/" SEARCH_LOG_FILE, conn->user->home);
				if ((fp = fopen(fpath, "a")) == NULL)
					log_conn("failed to open search log (PATH:%s, ERROR:%s)", fpath, strerror(errno));
				else
				{
					fprintf(fp, "%.*s\n", XS_CMD_BLEN(cmd), XS_CMD_BUF(cmd));
					fclose(fp);
				}
				rc = CONN_RES_OK(LOGGED);
			}
			break;
		case CMD_SEARCH_GET_DB:
			if (zarg->db == NULL)
				rc = CONN_RES_ERR(NODB);
			else
			{
				const string &desc = zarg->db->get_description();
				rc = CONN_RES_OK3(DB_INFO, desc.data(), desc.size());
			}
			break;
			// Silent commands
			// NOTE: the following commands without any respond
		case CMD_SEARCH_SET_SORT:
			if (zarg->eq != NULL)
			{
				int type = cmd->arg1 & CMD_SORT_TYPE_MASK;
				bool reverse = (cmd->arg1 & CMD_SORT_FLAG_ASCENDING) ? false : true;

				if (type == CMD_SORT_TYPE_DOCID)
				{
					zarg->eq->set_docid_order(reverse ? Xapian::Enquire::DESCENDING : Xapian::Enquire::ASCENDING);
					conn->flag |= CONN_FLAG_CH_SORT;
				}
				else if (type == CMD_SORT_TYPE_VALUE)
				{
					zarg->eq->set_sort_by_value_then_relevance(cmd->arg2, reverse);
					conn->flag |= CONN_FLAG_CH_SORT;
				}
				else if (type == CMD_SORT_TYPE_RELEVANCE)
				{
					zarg->eq->set_sort_by_relevance();
					conn->flag &= ~CONN_FLAG_CH_SORT;
				}
			}
			break;
		case CMD_SEARCH_SET_CUT:
			zarg->cuts[cmd->arg2] &= CMD_VALUE_FLAG_NUMERIC;
			zarg->cuts[cmd->arg2] |= (cmd->arg1 & (CMD_VALUE_FLAG_NUMERIC - 1));
			break;
		case CMD_SEARCH_SET_NUMERIC:
			zarg->cuts[cmd->arg2] |= CMD_VALUE_FLAG_NUMERIC;
			break;
		case CMD_SEARCH_SET_COLLAPSE:
			if (zarg->eq != NULL)
			{
				int vno = cmd->arg2 == XS_DATA_VNO ? Xapian::BAD_VALUENO : cmd->arg2;
				int max = cmd->arg1 == 0 ? 1 : cmd->arg1;
				zarg->eq->set_collapse_key(vno, max);
				if (cmd->arg2 == XS_DATA_VNO)
					conn->flag &= ~CONN_FLAG_CH_COLLAPSE;
				else
					conn->flag |= CONN_FLAG_CH_COLLAPSE;
			}
			break;
		case CMD_QUERY_INIT:
			if (!zarg->qq->empty())
			{
				delete zarg->qq;
				zarg->qq = new Xapian::Query();
			}
			if (cmd->arg1 == 1)
			{
				delete zarg->qp;
				zarg->qp = new Xapian::QueryParser();
				zarg->qp->set_stemmer(stemmer);
				zarg->qp->set_stopper(&stopper);
				zarg->qp->set_stemming_strategy(Xapian::QueryParser::STEM_SOME);
				zarg->qp->set_database(*zarg->db);
				zarg->parse_flag = 0;
				memset(&zarg->cuts, 0, sizeof(zarg->cuts));
			}
			break;
		case CMD_QUERY_PREFIX:
			if (cmd->arg2 != XS_DATA_VNO)
			{
				string field = string(XS_CMD_BUF(cmd), XS_CMD_BLEN(cmd));
				char prefix[3];

				vno_to_prefix(cmd->arg2, prefix);
				if (cmd->arg1 == CMD_PREFIX_BOOLEAN)
					zarg->qp->add_boolean_prefix(field, prefix);
				else
					zarg->qp->add_prefix(field, prefix);
			}
			break;
		case CMD_QUERY_PARSEFLAG:
			zarg->parse_flag = XS_CMD_ARG(cmd);
			break;
		case CMD_QUERY_RANGEPROC:
		{
			Xapian::ValueRangeProcessor *vrp;

			if (cmd->arg1 == CMD_RANGE_PROC_DATE)
				vrp = new Xapian::DateValueRangeProcessor(cmd->arg2);
			else if (cmd->arg1 == CMD_RANGE_PROC_NUMBER)
				vrp = new Xapian::NumberValueRangeProcessor(cmd->arg2);
			else
				vrp = new Xapian::StringValueRangeProcessor(cmd->arg2);

			zarg_add_object(zarg, OTYPE_RANGER, NULL, vrp);
			log_debug("new (Xapian::ValueRangeProcessor *): %p", vrp);
		}
			break;
		default: rc = CMD_RES_NEXT; // passed to next
	}
	return rc;
}

/**
 * fetch database for conn by name
 */
static inline Xapian::Database *fetch_conn_database(XS_CONN *conn, const char *name)
{
	struct search_zarg *zarg = (struct search_zarg *) conn->zarg;
	Xapian::Database *db = (Xapian::Database *) zarg_get_object(zarg, OTYPE_DB, name);

	if (db == NULL)
	{
		db = new Xapian::Database(string(conn->user->home) + "/" + string(name));
		db->keep_alive();
		zarg_add_object(zarg, OTYPE_DB, name, db);
		log_debug("new (Xapian::Database *)%s: %p", name, db);
	}
	return db;
}

/**
 * Set the active db to search
 * @param conn
 * @return CMD_RES_CONT
 */
static int zcmd_task_set_db(XS_CONN *conn)
{
	XS_CMD *cmd = conn->zcmd;
	struct search_zarg *zarg = (struct search_zarg *) conn->zarg;
	string name = string(XS_CMD_BUF(cmd), XS_CMD_BLEN(cmd));
	int rc;

	if (name.size() == 0) name = DEFAULT_DB_NAME;
	rc = xs_user_check_name(name.data(), name.size());
	if (rc == CMD_ERR_TOOLONG)
		rc = CONN_RES_ERR(TOOLONG);
	else if (rc == CMD_ERR_INVALIDCHAR)
		rc = CONN_RES_ERR(INVALIDCHAR);
	else
	{
		Xapian::Database *db = fetch_conn_database(conn, name.data());

		conn->flag |= CONN_FLAG_CH_DB;
		if (zarg->db == NULL || cmd->cmd == CMD_SEARCH_SET_DB)
		{
			DELETE_PTR(zarg->db);
			zarg->db = new Xapian::Database();
			if (name == DEFAULT_DB_NAME)
				conn->flag ^= CONN_FLAG_CH_DB;
		}

		zarg->db->add_database(*db);
		zarg->qp->set_database(*zarg->db);
		DELETE_PTR(zarg->eq);
		zarg->eq = new Xapian::Enquire(*zarg->db);
		conn->flag &= ~CONN_FLAG_CH_SORT;

		zarg->db_total = zarg->db->get_doccount();
		rc = CONN_RES_OK(DB_CHANGED);
	}
	return rc;
}

/**
 * Get total matched count
 * @param conn
 * @return CMD_RES_CONT
 */
static int zcmd_task_get_total(XS_CONN *conn)
{
	unsigned int count, total;
	XS_CMD *cmd = conn->zcmd;
	struct search_zarg *zarg = (struct search_zarg *) conn->zarg;
	Xapian::Query qq;

	// check db & data length
	if (zarg->db == NULL)
		return CONN_RES_ERR(NODB);
	if (XS_CMD_BLEN(cmd) > MAX_QUERY_LENGTH)
		return CONN_RES_ERR(TOOLONG);

	// load the query
	FETCH_CMD_QUERY(qq);
	log_debug_conn("search count (USER:%s, QUERY:%s)", conn->user->name, qq.get_description().data() + 13);

	// get total & count
	total = zarg->db->get_doccount();
	if (qq.empty())
		count = total;
	else
	{
		int cache_flag = CACHE_NONE;
#ifdef HAVE_MEMORY_CACHE
		char md5[33]; // KEY: MD5("Matchec for " +  user + ": " + query");

		if (!(conn->flag & CONN_FLAG_CH_DB))
		{
			struct cache_count *cc;
			string key = "Total for " + string(conn->user->name) + ": " + qq.get_description();

			md5_r(key.data(), md5);
			cache_flag |= CACHE_USE;

			// Extremely low probability of deadlock for adding CONN_FLAG_CACHE_LOCKED
			C_LOCK_CACHE();
			cc = (struct cache_count *) mc_get(mc, md5);
			C_UNLOCK_CACHE();

			if (cc != NULL)
			{
				cache_flag |= CACHE_FOUND;
				if (cc->total != total)
				{
					log_debug_conn("search count cache expired (COUNT:%d, TOTAL:%u<>%u)",
						count, cc->total, total);
				}
				else
				{
					cache_flag |= CACHE_VALID;
					count = cc->count;
					log_debug_conn("search count cache hit (COUNT:%d)", count, total);
				}
			}
			else
			{
				log_debug_conn("search count cache miss (KEY:%s)", md5);
			}
		}
#endif
		// get count by searching directly
		if (!(cache_flag & CACHE_VALID))
		{
			conn->flag &= ~CONN_FLAG_CH_SORT;
			zarg->eq->set_sort_by_relevance(); // sort reset
			zarg->eq->set_query(qq);

			Xapian::MSet mset = zarg->eq->get_mset(0, MAX_SEARCH_RESULT);
			count = mset.get_matches_estimated();
			log_debug_conn("search count estimated (COUNT:%d)", count);

#ifdef HAVE_MEMORY_CACHE
			if (cache_flag & CACHE_USE)
			{
				if (count > MAX_SEARCH_RESULT) cache_flag |= CACHE_NEED;
				if (cache_flag & CACHE_NEED)
				{
					struct cache_count cs;

					cs.total = total;
					cs.count = count;
					C_LOCK_CACHE();
					mc_put(mc, md5, &cs, sizeof(cs));
					C_UNLOCK_CACHE();
					log_debug_conn("search count cache created (KEY:%s, COUNT:%d)", md5, count);
				}
				else if (cache_flag & CACHE_FOUND)
				{
					C_LOCK_CACHE();
					mc_del(mc, md5);
					C_UNLOCK_CACHE();
					log_debug_conn("search count cache dropped (KEY:%s)", md5);
				}
			}
#endif
		}
	}

	return CONN_RES_OK3(SEARCH_TOTAL, (char *) &count, sizeof(count));
}

/**
 * Get total matched result
 */
static int zcmd_task_get_result(XS_CONN *conn)
{
	XS_CMD *cmd = conn->zcmd;
	struct search_zarg *zarg = (struct search_zarg *) conn->zarg;
	unsigned int off, limit, count;
	int rc = CMD_RES_CONT, cache_flag = CACHE_NONE;
#ifdef HAVE_MEMORY_CACHE
	struct cache_result cs, *cr = NULL;
	char md5[33];
#endif
	Xapian::Query qq;

	// check db & data length
	if (zarg->db == NULL)
		return CONN_RES_ERR(NODB);
	if (XS_CMD_BLEN(cmd) > MAX_QUERY_LENGTH)
		return CONN_RES_ERR(TOOLONG);

	// fetch query
	FETCH_CMD_QUERY(qq);

	// check input (off+limit) in buf1
	if (XS_CMD_BLEN1(cmd) != (sizeof(int) + sizeof(int)))
	{
		if (XS_CMD_BLEN1(cmd) != 0)
			return CONN_RES_ERR(WRONGFORMAT);
		off = 0;
		limit = (MAX_SEARCH_RESULT >> 4) + 1;
	}
	else
	{
		off = *((unsigned int *) XS_CMD_BUF1(cmd));
		limit = *((unsigned int *) (XS_CMD_BUF1(cmd) + sizeof(int)));
		if (limit > MAX_SEARCH_RESULT) limit = MAX_SEARCH_RESULT;
	}
	log_debug_conn("search result (USER:%s, OFF:%d, LIMIT:%d, QUERY:%s)",
		conn->user->name, off, limit, qq.get_description().data() + 13);

	// check to skip empty query
	if (qq.empty() || limit == 0)
		return CONN_RES_ERR(EMPTYQUERY);

#ifdef HAVE_MEMORY_CACHE
	// Only cache for default db with default sorter, and only top MAX_SEARCH_RESUT items
	// KEY: MD5("Result for " +  user + ": " + query");
	cs.total = zarg->db->get_doccount();
	if ((off + limit) <= MAX_SEARCH_RESULT
		&& !(conn->flag & (CONN_FLAG_CH_SORT | CONN_FLAG_CH_DB | CONN_FLAG_CH_COLLAPSE)))
	{
		string key = "Result for " + string(conn->user->name) + ": " + qq.get_description();

		cache_flag |= CACHE_USE;
		md5_r(key.data(), md5);

		C_LOCK_CACHE();
		cr = (cache_result *) mc_get(mc, md5);
		C_UNLOCK_CACHE();

		if (cr != NULL)
		{
			cache_flag |= CACHE_FOUND;
			if (cr->total != cs.total)
			{
				log_debug_conn("search result cache expired (COUNT:%d, TOTAL:%u<>%u)",
					cr->count, cr->total, cs.total);
			}
			else
			{
				cache_flag |= CACHE_VALID;
				log_debug_conn("search result cache hit (COUNT:%d)", cr->count);
			}
		}
		else
		{
			log_debug_conn("search result cache miss (KEY:%s)", md5);
		}
	}
#endif

	// set parameters to search or load data for cache
	zarg->eq->set_query(qq);

	// check cache flag
	if (!(cache_flag & CACHE_VALID))
	{
		unsigned int off2, limit2;

		// search directly
		off2 = (cache_flag & CACHE_USE) ? 0 : off;
		limit2 = (cache_flag & CACHE_USE) ? MAX_SEARCH_RESULT : limit;

		Xapian::MSet mset = zarg->eq->get_mset(off2, limit2);
		count = mset.get_matches_estimated();
		log_debug_conn("search result estimated (COUNT:%d, OFF2:%d, LIMIT2:%d)", count, off2, limit2);

#ifdef HAVE_MEMORY_CACHE
		if (count > MAX_SEARCH_RESULT && (cache_flag & CACHE_USE))
		{
			cache_flag |= CACHE_NEED;
			memset(&cs.doc, 0, sizeof(cs.doc));
		}
#endif
		// first to send the total header
		if ((rc = CONN_RES_OK3(RESULT_BEGIN, (char *) &count, sizeof(count))) != CMD_RES_CONT)
			return rc;

		// send every document
		limit2 = 0;
		limit += off;
		for (Xapian::MSetIterator m = mset.begin(); m != mset.end(); m++)
		{
			struct result_doc rd;

			rd.docid = *m;
			rd.rank = m.get_rank() + 1;
			rd.ccount = m.get_collapse_count();
			rd.percent = m.get_percent();
			rd.weight = (float) m.get_weight();
#ifdef HAVE_MEMORY_CACHE
			if (cache_flag & CACHE_NEED) cs.doc[limit2++] = rd;
#endif
			if (++off2 <= off) continue;
			if (off2 > limit) continue;
			if (rd.docid == 0) continue;

			// send the doc
			if ((rc = send_result_doc(conn, &rd)) != CMD_RES_CONT)
				return rc;
		}

#ifdef HAVE_MEMORY_CACHE
		// check to save or delete cache
		if (cache_flag & CACHE_NEED)
		{
			cs.count = count;
			C_LOCK_CACHE();
			mc_put(mc, md5, &cs, sizeof(cs));
			C_UNLOCK_CACHE();
			log_debug_conn("search result cache created (KEY:%s, COUNT:%d)", md5, count);
		}
		else if (cache_flag & CACHE_FOUND)
		{
			C_LOCK_CACHE();
			mc_del(mc, md5);
			C_UNLOCK_CACHE();
			log_debug_conn("search result cache dropped (KEY:%s)", md5);
		}
#endif
	}
#ifdef HAVE_MEMORY_CACHE
	else
	{
		// send the total header (break to switch)
		if ((rc = CONN_RES_OK3(RESULT_BEGIN, (char *) &cr->count, sizeof(cr->count))) != CMD_RES_CONT)
			return rc;

		// send documents
		limit += off;
		do
		{
			if ((rc = send_result_doc(conn, &cr->doc[off])) != CMD_RES_CONT)
				return rc;
		}
		while (++off < limit);
	}
#endif
	// send end declare
	return CONN_RES_OK(RESULT_END);
}

/**
 * Add subquery to current query
 * Query types: term, string, range, valcmp
 * @param conn
 * @return CMD_RES_CONT
 */
static int zcmd_task_add_query(XS_CONN *conn)
{
	XS_CMD *cmd = conn->zcmd;
	struct search_zarg *zarg = (struct search_zarg *) conn->zarg;
	Xapian::Query q2;

	// check data length
	if (XS_CMD_BLEN(cmd) > MAX_QUERY_LENGTH)
		return CONN_RES_ERR(TOOLONG);

	// generate new query
	string qstr = string(XS_CMD_BUF(cmd), XS_CMD_BLEN(cmd));
	if (cmd->cmd == CMD_QUERY_TERM)
	{
		if (cmd->arg2 != XS_DATA_VNO)
		{
			char prefix[3];
			vno_to_prefix(cmd->arg2, prefix);
			qstr = string(prefix) + qstr;
		}
		q2 = Xapian::Query(qstr);
		log_debug_conn("add query term (TERM:%s, ADD_OP:%d, VNO:%d)",
			qstr.data(), cmd->arg1, cmd->arg2);
	}
	else if (cmd->cmd == CMD_QUERY_RANGE)
	{
		string qstr1 = string(XS_CMD_BUF1(cmd), XS_CMD_BLEN1(cmd));
		log_debug_conn("add query range (VNO:%d, FROM:%s, TO:%s, ADD_OP:%d)",
			cmd->arg2, qstr.data(), qstr1.data(), cmd->arg1);

		// check to serialise
		if (zarg->cuts[cmd->arg2] & CMD_VALUE_FLAG_NUMERIC)
		{
			qstr = Xapian::sortable_serialise(strtod(qstr.data(), NULL));
			qstr1 = Xapian::sortable_serialise(strtod(qstr1.data(), NULL));
		}
		q2 = Xapian::Query(Xapian::Query::OP_VALUE_RANGE, cmd->arg2, qstr, qstr1);
	}
	else if (cmd->cmd == CMD_QUERY_VALCMP)
	{
		bool less = (XS_CMD_BLEN1(cmd) == 1 && *(XS_CMD_BUF1(cmd)) == CMD_VALCMP_GE) ? false : true;
		log_debug_conn("add query valcmp (TYPE:%c, VNO:%d, VALUE:%s, ADD_OP:%d)",
			less ? '<' : '>', cmd->arg2, qstr.data(), cmd->arg1);

		// check to serialise
		if (zarg->cuts[cmd->arg2] & CMD_VALUE_FLAG_NUMERIC)
			qstr = Xapian::sortable_serialise(strtod(qstr.data(), NULL));
		q2 = Xapian::Query(less ? Xapian::Query::OP_VALUE_LE : Xapian::Query::OP_VALUE_GE, cmd->arg2, qstr);
	}
	else
	{
		int flag = zarg->parse_flag > 0 ? zarg->parse_flag : Xapian::QueryParser::FLAG_DEFAULT;
		zarg->qp->set_default_op(GET_QUERY_OP(cmd->arg2));
		q2 = zarg->qp->parse_query(qstr, flag);
		log_debug_conn("add parse query (QUERY:%s, FLAG:0x%04x, ADD_OP:%d, DEF_OP:%d)",
			qstr.data(), flag, cmd->arg1, cmd->arg2);
	}

	// check to do OP_SCALE_WEIGHT
	if (XS_CMD_BLEN1(cmd) == 2
		&& (cmd->cmd == CMD_QUERY_TERM || cmd->cmd == CMD_QUERY_PARSE))
	{
		unsigned char *buf1 = (unsigned char *) XS_CMD_BUF1(cmd);
		double scale = GET_SCALE(buf1);
		q2 = Xapian::Query(Xapian::Query::OP_SCALE_WEIGHT, q2, scale);
	}

	// combine with old query
	if (zarg->qq->empty())
	{
		delete zarg->qq;
		zarg->qq = new Xapian::Query(q2);
	}
	else
	{
		Xapian::Query *qq = new Xapian::Query(GET_QUERY_OP(cmd->arg1), *zarg->qq, q2);
		delete zarg->qq;
		zarg->qq = qq;
	}
	return CMD_RES_CONT;
}

/**
 * String case-sensitive compare
 */
struct string_casecmp
{

	bool operator() (const string &a, const string & b) const
	{
		return strcasecmp(a.data(), b.data()) < 0;
	}
};

/**
 * Get parsed query string
 */
static int zcmd_task_get_query(XS_CONN *conn)
{
	XS_CMD *cmd = conn->zcmd;
	struct search_zarg *zarg = (struct search_zarg *) conn->zarg;
	string str;
	Xapian::Query qq;

	if (XS_CMD_BLEN(cmd) > 0)
	{
		string qstr = string(XS_CMD_BUF(cmd), XS_CMD_BLEN(cmd));
		int flag = zarg->parse_flag > 0 ? zarg->parse_flag : Xapian::QueryParser::FLAG_DEFAULT;

		zarg->qp->set_default_op(GET_QUERY_OP(cmd->arg2));
		qq = zarg->qp->parse_query(qstr, flag);

		if (cmd->cmd == CMD_QUERY_GET_STRING && XS_CMD_BLEN1(cmd) == 2)
		{
			unsigned char *buf1 = (unsigned char *) XS_CMD_BUF1(cmd);
			double scale = GET_SCALE(buf1);
			qq = Xapian::Query(Xapian::Query::OP_SCALE_WEIGHT, qq, scale);
		}
	}

	if (cmd->cmd == CMD_QUERY_GET_TERMS)
	{
		std::set<string, string_casecmp> terms;
		std::pair < std::set<string, string_casecmp>::iterator, bool> ins;
		string str2, tt;
		Xapian::TermIterator tb = (XS_CMD_BLEN(cmd) > 0) ? qq.get_terms_begin() : zarg->qq->get_terms_begin();
		Xapian::TermIterator te = (XS_CMD_BLEN(cmd) > 0) ? qq.get_terms_end() : zarg->qq->get_terms_end();

		while (tb != te)
		{
			tt = *tb++;
			if (cmd->arg1 == 1)
			{
				ins = terms.insert(tt);
				if (ins.second == true)
					str += tt + " ";
			}
			else
			{
				Xapian::TermIterator ub = zarg->qp->unstem_begin(tt);
				Xapian::TermIterator ue = zarg->qp->unstem_end(tt);

				if (ub == ue)
				{
					ins = terms.insert(tt);
					if (ins.second == true)
						str += tt + " ";
				}
				else
				{
					bool first = true;
					while (ub != ue)
					{
						tt = *ub++;
						ins = terms.insert(tt);
						if (ins.second == true)
						{
							if (first)
								str += tt + " ";
							else
								str2 += tt + " ";
						}
						first = false;
					}
				}
			}
		}
		str += str2;
		return CONN_RES_OK3(QUERY_TERMS, str.data(), str.size() > 0 ? str.size() - 1 : 0);
	}
	else
	{
		str = (XS_CMD_BLEN(cmd) > 0) ? qq.get_description() : zarg->qq->get_description();

		return CONN_RES_OK3(QUERY_STRING, str.data(), str.size());
	}
}

/**
 * query fixed
 */
struct fixed_query
{
	char *raw; // raw-query (fixed)
	char *nsp; // non-space
	char *py; // pinyin buffer
	int flag; // flag
	int len; // len of raw
};

#define	IS_8BIT(x)			((unsigned char)(x) & 0x80)
#define	IS_NCHAR(x)			(x >= 0 && x <= ' ')
#define	FQ_8BIT_ONLY()		((fq->flag & 0x03) == 0x01)
#define	FQ_7BIT_ONLY()		((fq->flag & 0x03) == 0x02)
#define	FQ_7BIT_8BIT()		((fq->flag & 0x03) == 0x03)
#define	FQ_END_SPACE()		(fq->flag & 0x04)
#define	FQ_HAVE_SPACE()		(fq->flag & 0x08)

static struct fixed_query *get_fixed_query(char *str, int len)
{
	struct fixed_query *fq;
	char *raw, *nsp, *end = str + len - 1;
	;

	// sizeof(struct) + <raw> \0 <non-space> \0 <py_buffer> \0
	fq = (struct fixed_query *) malloc(sizeof(struct fixed_query) + (len << 2) + 2);
	if (fq == NULL)
		return NULL;
	raw = fq->raw = (char *) fq + sizeof(struct fixed_query);
	nsp = fq->nsp = fq->raw + len + 1;
	fq->py = fq->nsp + len + 1;
	fq->flag = 0;

	// loop to fixed
	do
	{
		if (IS_8BIT(*str))
		{
			*raw = *str;
			*nsp++ = *raw++;
			fq->flag |= 0x01;
		}
		else if (IS_NCHAR(*str))
		{
			if (raw == fq->raw || raw[-1] == ' ')
				continue;
			if (str == end)
			{
				fq->flag |= 0x04; // end with space char
				break;
			}
			if (IS_NCHAR(str[1]) || (IS_8BIT(raw[-1]) ^ IS_8BIT(str[1])))
				continue;
			*raw++ = ' ';
			fq->flag |= 0x08;
		}
		else
		{
			*raw = (*str >= 'A' && *str <= 'Z') ? (*str | 0x20) : *str;
			*nsp++ = *raw++;
			fq->flag |= 0x02;
		}
	}
	while (++str <= end);

	*raw = *nsp = '\0';
	fq->len = raw - fq->raw;
	return fq;
}

/**
 * Get corrected query string
 */
static int zcmd_task_get_corrected(XS_CONN *conn)
{
	string result, tt;
	XS_CMD *cmd = conn->zcmd;
	py_list *pl, *cur;
	struct fixed_query *fq;
	char *str, *ptr;
	Xapian::Database *db = fetch_conn_database(conn, SEARCH_LOG_DB);
	Xapian::MSet ms;
	Xapian::Enquire eq(*db);

	// fixed query, clean white characters
	if (XS_CMD_BLEN(cmd) == 0)
		return CONN_RES_ERR(EMPTYQUERY);
	if (XS_CMD_BLEN(cmd) > MAX_QUERY_LENGTH)
		return CONN_RES_ERR(TOOLONG);

	if ((fq = get_fixed_query(XS_CMD_BUF(cmd), XS_CMD_BLEN(cmd))) == NULL)
		return CONN_RES_ERR(NOMEM);
	log_debug_conn("corrected query (USER:%s, QUERY:%s)", conn->user->name, fq->raw);

	// 1.check full union with non-space string
	if (FQ_7BIT_ONLY())
		tt = "B" + string(fq->nsp);
	else
	{
		pl = py_convert(fq->raw, fq->len);
		for (ptr = fq->py, cur = pl; cur != NULL; cur = cur->next)
		{
			strcpy(ptr, cur->py);
			ptr += strlen(cur->py);
		}
		py_list_free(pl);
		tt = "B" + string(fq->py, ptr - fq->py);
	}
	log_debug_conn("checking full non-space py (TERM:%s)", tt.data());
	if (db->term_exists(tt))
	{
		eq.set_query(Xapian::Query(tt));
		ms = eq.get_mset(0, 3);
		for (Xapian::MSetIterator m = ms.begin(); m != ms.end(); m++)
		{
			tt = m.get_document().get_data();
			if (!FQ_7BIT_ONLY() && !memcmp(tt.data(), fq->raw, tt.size()))
				break;
			result += tt + "\n";
		}
		if (result.size() > 0)
		{
			result.resize(result.size() - 1);
			goto end_fixed;
		}
	}

	// 2.parse every partial (concat single py & char)
	for (ptr = str = fq->raw; *ptr != '\0'; str = ptr)
	{
		string tt;

		if (*ptr == ' ')
		{
			result += " ";
			str++;
		}
		if (IS_8BIT(*str))
		{
			// 8bit chars
			for (ptr = str + 1; IS_8BIT(*ptr); ptr++);

			// do not support single word
			if ((ptr - str) < 6)
			{
				result.resize(0);
				goto end_fixed;
			}
			else
			{
				char *py;

				// check full-pinyin
				log_debug_conn("get raw 8bit chars (TERM:%.*s)", ptr - str, str);
				pl = py_convert(str, ptr - str);
				for (py = fq->py, cur = pl; cur != NULL; cur = cur->next)
				{
					strcpy(py, cur->py);
					py += strlen(cur->py);
				}
				tt = string(fq->py, py - fq->py);
				log_debug_conn("get raw py (TERM:%s)", tt.data());

				// check fuzzy-pinyin
				if (tt.size() > 0 && !db->term_exists("B" + tt))
				{
					if (py_fuzzy_fix(pl) == NULL)
						tt.resize(0);
					else
					{
						for (py = fq->py, cur = pl; cur != NULL; cur = cur->next)
						{
							strcpy(py, cur->py);
							py += strlen(cur->py);
						}
						tt = string(fq->py, py - fq->py);
						log_debug_conn("get fuzzy py (TERM:%s)", tt.data());

						if (!db->term_exists("B" + tt))
							tt.resize(0);
					}
				}
				py_list_free(pl);

				// failed
				if (tt.empty())
				{
					result.resize(0);
					goto end_fixed;
				}
			}
		}
		else
		{
			// 7bit chars
			for (ptr = str + 1; *ptr != '\0' && *ptr != ' ' && !IS_8BIT(*ptr); ptr++);

			// check raw pinyin/abbr
			tt = string(str, ptr - str);
			if (!db->term_exists("B" + tt))
			{
				// check fuzzy pinyin
				pl = py_segment(str, ptr - str);
				if (py_fuzzy_fix(pl) == NULL)
					tt.resize(0);
				else
				{
					char *py;
					for (py = fq->py, cur = pl; cur != NULL; cur = cur->next)
					{
						strcpy(py, cur->py);
						py += strlen(cur->py);
					}
					tt = string(fq->py, py - fq->py);
				}
				py_list_free(pl);
				log_debug_conn("get as fuzzy py (TERM:%s)", tt.data());

				// check spelling correction
				if (tt.empty() || !db->term_exists("B" + tt))
				{
					tt = db->get_spelling_suggestion(string(str, ptr - str));
					log_debug_conn("get spelling suggestion (TERM:%s)", tt.data());

					if (!tt.empty())
						result += tt;
					else
					{
						tt = string(str, ptr - str);
						if (!db->term_exists(tt))
						{
							result.resize(0);
							goto end_fixed;
						}
						result += tt;
					}
					continue;
				}
			}
		}

		// read pinyin result
		log_debug_conn("checking full partial py (TERM:B%s)", tt.data());
		bool multi = (str == fq->raw && *ptr == '\0');
		eq.set_query(Xapian::Query("B" + tt));
		ms = eq.get_mset(0, multi ? 3 : 1);
		for (Xapian::MSetIterator m = ms.begin(); m != ms.end(); m++)
		{
			tt = m.get_document().get_data();
			if (multi && !memcmp(tt.data(), str, ptr - str))
				break;
			result += tt;
			if (multi)
				result += "\n";
		}
		if (multi && result.size() > 0)
			result.resize(result.size() - 1);
	}

end_fixed:
	// free memory
	if (!memcmp(result.data(), fq->raw, result.size()))
		result.resize(0);
	free(fq);
	return CONN_RES_OK3(QUERY_CORRECTED, result.data(), result.size());
}

/**
 * Get expanded query string
 */
static int zcmd_task_get_expanded(XS_CONN *conn)
{
	Xapian::Query qq;
	int limit, rc;
	struct fixed_query *fq;
	XS_CMD *cmd = conn->zcmd;
	Xapian::Database *db = fetch_conn_database(conn, SEARCH_LOG_DB);

	// fixed query, clean white characters
	if (XS_CMD_BLEN(cmd) == 0)
		return CONN_RES_ERR(EMPTYQUERY);
	if (XS_CMD_BLEN(cmd) > MAX_QUERY_LENGTH)
		return CONN_RES_ERR(TOOLONG);

	if ((fq = get_fixed_query(XS_CMD_BUF(cmd), XS_CMD_BLEN(cmd))) == NULL)
		return CONN_RES_ERR(NOMEM);
	log_debug_conn("expanded query (USER:%s, QUERY:%s)", conn->user->name, fq->raw);

	// first to send the total header
	limit = cmd->arg1 > 0 ? cmd->arg1 : 10;
	if ((rc = CONN_RES_OK3(RESULT_BEGIN, fq->raw, fq->len)) != CMD_RES_CONT)
		return rc;
	// check size
	if (fq->len > MAX_EXPAND_LEN)
	{
		// expand from primary key as wildcard directly		
		string root = "A" + string(fq->raw, fq->len);
		Xapian::TermIterator ti = db->allterms_begin(root);

		while (ti != db->allterms_end(root))
		{
			string tt = *ti;
			if (root != tt)
			{
				rc = conn_respond(conn, CMD_SEARCH_RESULT_FIELD, 0, tt.data() + 1, tt.size() - 1);
				if (rc != CMD_RES_CONT || --limit == 0) break;
			}
			ti++;
		}
		// full pinyin
		if (rc == CMD_RES_CONT && FQ_7BIT_ONLY() && limit > 0)
		{
			int max = 3;

			root = "B" + string(fq->raw, fq->len);
			ti = db->allterms_begin(root);
			while (ti != db->allterms_end(root))
			{
				if (qq.empty())
					qq = Xapian::Query(*ti);
				else
				{
					qq = Xapian::Query(Xapian::Query::OP_OR, qq, Xapian::Query(*ti));
				}
				if (--max == 0)
					break;
				ti++;
			}
		}
	}
	else
	{
		// 0.raw query partial
		Xapian::Query q2;
		string pp = "C" + string(fq->raw, fq->len);

		qq = Xapian::Query(pp);
		// 1.expand from raw query
		if (FQ_END_SPACE())
		{
			q2 = Xapian::Query(Xapian::Query::OP_SCALE_WEIGHT, Xapian::Query(pp + " "), 0.5);
			qq = Xapian::Query(Xapian::Query::OP_AND_MAYBE, qq, q2);
		}
		// 2.pure 7bit
		if (FQ_7BIT_ONLY())
		{
			// use non-space pinyin to get fuzzy partial
			if (!FQ_HAVE_SPACE())
			{
				char *ptr;
				py_list *cur, *pl = py_segment(fq->raw, fq->len);

				if (py_fuzzy_fix(pl) != NULL)
				{
					for (ptr = fq->py, cur = pl; cur != NULL; cur = cur->next)
					{
						strcpy(ptr, cur->py);
						ptr += strlen(cur->py);
					}
					if ((ptr - fq->py) <= MAX_EXPAND_LEN)
					{
						q2 = Xapian::Query("C" + string(fq->py, ptr - fq->py));
						q2 = Xapian::Query(Xapian::Query::OP_SCALE_WEIGHT, q2, 0.5);
						qq = Xapian::Query(Xapian::Query::OP_OR, qq, q2);
					}
				}
				py_list_free(pl);
			}
			// full pinyin check (TODO: filter non-pinyin querys...)
			q2 = Xapian::Query("B" + string(fq->raw, fq->len));
			q2 = Xapian::Query(Xapian::Query::OP_SCALE_WEIGHT, q2, 0.5);
			qq = Xapian::Query(Xapian::Query::OP_AND_MAYBE, qq, q2);
		}
		// 3. 7bit+8bit, convert to pinyin
		if (FQ_7BIT_8BIT())
		{
			char *ptr;
			py_list *cur, *pl = py_convert(fq->raw, fq->len);

			for (ptr = fq->py, cur = pl; cur != NULL; cur = cur->next)
			{
				strcpy(ptr, cur->py);
				ptr += strlen(cur->py);
				if (cur->next != NULL && PY_ILLEGAL(cur) && PY_ILLEGAL(cur->next))
					*ptr++ = ' ';
			}
			py_list_free(pl);

			if ((ptr - fq->py) <= MAX_EXPAND_LEN)
			{
				q2 = Xapian::Query("C" + string(fq->py, ptr - fq->py));
				q2 = Xapian::Query(Xapian::Query::OP_SCALE_WEIGHT, q2, 0.5);
				qq = Xapian::Query(Xapian::Query::OP_OR, qq, q2);
			}
		}
		else if (FQ_HAVE_SPACE())
		{
			// try to query without spacce
			qq = Xapian::Query(Xapian::Query::OP_OR, qq, Xapian::Query("C" + string(fq->nsp)));
		}
	}
	// free memory
	free(fq);

	// process search
	if (!qq.empty())
	{
		Xapian::MSet ms;
		Xapian::Enquire eq(*db);

		log_debug_conn("expanded query (QUERY:%s)", qq.get_description().data());
		eq.set_query(qq);
		ms = eq.get_mset(0, limit);
		for (Xapian::MSetIterator m = ms.begin(); m != ms.end(); m++)
		{
			const string &tt = m.get_document().get_data();
			if ((rc = conn_respond(conn, CMD_SEARCH_RESULT_FIELD, 0, tt.data(), tt.size())) != CMD_RES_CONT)
				break;
		}
	}

	// send end declare
	return CONN_RES_OK2(RESULT_END, qq.get_description().data());
}
/**
 * Task command table
 */
static zcmd_exec_tab zcmd_task_tab[] = {
	{CMD_SEARCH_SET_DB, zcmd_task_set_db},
	{CMD_SEARCH_ADD_DB, zcmd_task_set_db},
	{CMD_SEARCH_GET_TOTAL, zcmd_task_get_total},
	{CMD_SEARCH_GET_RESULT, zcmd_task_get_result},
	{CMD_QUERY_TERM, zcmd_task_add_query},
	{CMD_QUERY_RANGE, zcmd_task_add_query},
	{CMD_QUERY_VALCMP, zcmd_task_add_query},
	{CMD_QUERY_PARSE, zcmd_task_add_query},
	{CMD_QUERY_GET_STRING, zcmd_task_get_query},
	{CMD_QUERY_GET_TERMS, zcmd_task_get_query},
	{CMD_QUERY_GET_CORRECTED, zcmd_task_get_corrected},
	{CMD_QUERY_GET_EXPANDED, zcmd_task_get_expanded},
	{CMD_DEFAULT, zcmd_task_default}
};

/**
 * Execute zcmd during task execution
 * @param conn current connection
 * @return RES_CMD_CONT/RES_CMD_PAUSE/CMD_RES_xxx
 */
static int zcmd_exec_task(XS_CONN * conn)
{
	try
	{
		// exec the commands accord to task tables
		return conn_zcmd_exec_table(conn, zcmd_task_tab);
	}
	catch (const Xapian::Error &e)
	{
		XS_CMD *cmd = conn->zcmd;
		string msg = e.get_msg();

		log_conn("xapian ERROR: %s", msg.data());
		return XS_CMD_DONT_ANS(cmd) ? CMD_RES_CONT : CONN_RES_ERR3(XAPIAN, msg.data(), msg.size());
	}
	catch (...)
	{
		XS_CMD *cmd = conn->zcmd;

		log_conn("unknown ERROR during executing task (CMD:%d)", cmd->cmd);

		return XS_CMD_DONT_ANS(cmd) ? CMD_RES_CONT : CONN_RES_ERR(UNKNOWN);
	}
}

/**
 * Check left io/rcv buffer for next cmds
 * @param conn
 * @return CMD_RES_PAUSE/CMD_RES_xxx
 */
static int task_exec_other(XS_CONN * conn)
{
	int rc;
	struct pollfd fdarr[1];

	// check to read new incoming data via poll()
	fdarr[0].fd = CONN_FD();
	fdarr[0].events = POLLIN;
	fdarr[0].revents = 0;

	// loop to parse cmd
	log_debug_conn("check to run left cmds in task");
	while (1)
	{
		// try to parse left command in rcv_buf
		if ((rc = conn_cmds_parse(conn, zcmd_exec_task)) != CMD_RES_CONT)
			break;
		// try to poll data (only 1 second)
		rc = conn->tv.tv_sec > 0 ? conn->tv.tv_sec * 1000 : -1;
		if ((rc = poll(fdarr, 1, rc)) > 0)
		{
			rc = CONN_RECV();
			log_debug_conn("data received in task (SIZE:%d)", rc);
			if (rc <= 0)
			{
				rc = rc < 0 ? CMD_RES_IOERR : CMD_RES_CLOSED;
				break;
			}
		}
		else
		{
			log_debug_conn("broken poll(...) = %d", rc);
			rc = (rc < 0 && errno != EINTR) ? CMD_RES_IOERR : CMD_RES_TIMEOUT;

			break;
		}
	}
	return rc;
}

/**
 * Cleanup function called when task forced to canceld on timeoud
 * We can free all related resource HERE (NOTE: close/push back the conn)
 * @param arg connection
 */
void task_cancel(void *arg)
{
	XS_CONN *conn = (XS_CONN *) arg;

	log_conn("task canceld, run the cleanup function (ZARG:%p)", conn->zarg);
	// free zargs!!
	if (conn->zarg != NULL)
	{

		zarg_cleanup((struct search_zarg *) conn->zarg);
		conn->zarg = NULL;
	}
	// free cache locking
	if (conn->flag & CONN_FLAG_CACHE_LOCKED)
		G_UNLOCK_CACHE();

	// close the connection
	CONN_RES_ERR(TASK_CANCELED);
	CONN_QUIT(ERROR);
}

/**
 * Task start pointer in thread pool
 * @param arg connection
 */
void task_exec(void *arg)
{
	int rc;
	struct search_zarg zarg;
	XS_CMDS *cmds;
	XS_CONN *conn = (XS_CONN *) arg;

	// init the zarg
	log_debug_conn("init the zarg of search");
	memset(&zarg, 0, sizeof(zarg));
	conn->zarg = &zarg;
	try
	{
		Xapian::Database *db;

		zarg.qq = new Xapian::Query();
		zarg.qp = new Xapian::QueryParser();
		zarg.qp->set_stemmer(stemmer);
		zarg.qp->set_stopper(&stopper);
		zarg.qp->set_stemming_strategy(Xapian::QueryParser::STEM_SOME);

		// load default database, try to init queryparser, enquire
		try
		{
			db = fetch_conn_database(conn, DEFAULT_DB_NAME);
			zarg.db = new Xapian::Database();
			zarg.db->add_database(*db);
			zarg.qp->set_database(*zarg.db);
			zarg.eq = new Xapian::Enquire(*zarg.db);
			zarg.db_total = db->get_doccount();
			conn->flag &= ~(CONN_FLAG_CH_DB | CONN_FLAG_CH_SORT);
		}
		catch (const Xapian::Error &e)
		{
			log_conn("failed to open default db (ERROR:%s)", e.get_msg().data());
		}
	}
	catch (const Xapian::Error &e)
	{
		string msg = e.get_msg();
		log_conn("xapian ERROR: %s", msg.data());
		CONN_RES_ERR3(XAPIAN, msg.data(), msg.size());
		rc = CMD_RES_ERROR;
		goto task_end;
	}
	catch (...)
	{
		CONN_RES_ERR(UNKNOWN);
		rc = CMD_RES_ERROR;
		goto task_end;
	}

	// begin the task, parse & execute cmds list
	// TODO: is need to check conn->zhead, conn->ztail should not be NULL
	log_debug_conn("task begin (HEAD:%d, TAIL:%d)", conn->zhead->cmd->cmd, conn->ztail->cmd->cmd);
	while ((cmds = conn->zhead) != NULL)
	{
		// run as zcmd
		conn->zcmd = cmds->cmd;
		conn->zhead = cmds->next;
		conn->flag |= CONN_FLAG_ZMALLOC; // force the zcmd to be free after execution

		// free the cmds, cmds->cmd/zcmd will be free in conn_zcmd_exec()
		log_debug_conn("free(%d), addr: %p", sizeof(XS_CMDS), cmds);
		free(cmds);

		// execute the zcmd (CMD_RES_CONT accepted only)
		if ((rc = conn_zcmd_exec(conn, zcmd_exec_task)) != CMD_RES_CONT)
			goto task_end;
	}
	// flush output cache
	conn->ztail = NULL;
	if (CONN_FLUSH() != 0)
	{
		rc = CMD_RES_IOERR;
		goto task_end;
	}

	// try to check other command in rcv_buf/io_buf
	rc = task_exec_other(conn);

	// end the task normal
task_end:
	log_conn("end the task from thread pool (RC:%d, CONN:%p)", rc, conn);
	// BUG: if thread cancled HERE, may cause some unspecified problems
	// free objects of zarg
	zarg_cleanup(&zarg);

	// push back or force to quit the connection
	if (rc != CMD_RES_PAUSE)
		conn_quit(conn, rc);
	else
	{
		conn->zarg = NULL;
		conn_server_push_back(conn);
	}
}
