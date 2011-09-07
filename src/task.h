/**
 * Task header file
 *
 * $Id$
 */

#ifndef __XS_TASK_20110703_H__
#define	__XS_TASK_20110703_H__

/**
 * max number of results per search Query
 * also be used for cache number
 */
#define	MAX_SEARCH_RESULT		100

/**
 * max length of query string for CMD_QUERY_
 */
#define	MAX_QUERY_LENGTH		80

void task_cancel(void *arg); // called on canceling task
void task_exec(void *arg); // called on executing task

#endif	/* __XS_TASK_20110703_H__ */

