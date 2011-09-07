<?php
/**
 * Xunsearch PHP-SDK 引导文件
 *
 * 这个文件是由开发工具中的 'build lite' 指令智能合并类定义的源码文件
 * 并删除所有注释而自动生成的。
 * 
 * 当您编写搜索项目时，先通过 require 引入该文件即可使用所有的 PHP-SDK
 * 功能。合并的主要目的是便于拷贝，只要复制这个库文件即可，而不用拷贝一
 * 大堆文件。详细文档请阅读 {@link:http://www.xunsearch.com/doc/php/}
 * 
 * 切勿手动修改本文件！生成时间：2011/09/07 17:31:39 
 *
 * @author hightman
 * @link http://www.xunsearch.com/
 * @copyright Copyright &copy; 2011 HangZhou YunSheng Network Technology Co., Ltd.
 * @license http://www.xunsearch.com/license/
 * @version $Id$
 */
define('CMD_NONE',	0);
define('CMD_DEFAULT',	CMD_NONE);
define('CMD_PROTOCOL',	20110707);
define('CMD_USE',	1);
define('CMD_HELLO',	1);
define('CMD_DEBUG',	2);
define('CMD_TIMEOUT',	3);
define('CMD_QUIT',	4);
define('CMD_INDEX_SET_DB',	32);
define('CMD_INDEX_GET_DB',	33);
define('CMD_INDEX_SUBMIT',	34);
define('CMD_INDEX_REMOVE',	35);
define('CMD_INDEX_EXDATA',	36);
define('CMD_INDEX_CLEAN_DB',	37);
define('CMD_DELETE_PROJECT',	38);
define('CMD_INDEX_COMMIT',	39);
define('CMD_INDEX_REBUILD',	40);
define('CMD_FLUSH_LOGGING',	41);
define('CMD_SEARCH_DB_TOTAL',	64);
define('CMD_SEARCH_GET_TOTAL',	65);
define('CMD_SEARCH_GET_RESULT',	66);
define('CMD_SEARCH_SET_DB',	CMD_INDEX_SET_DB);
define('CMD_SEARCH_GET_DB',	CMD_INDEX_GET_DB);
define('CMD_SEARCH_ADD_DB',	68);
define('CMD_SEARCH_FINISH',	69);
define('CMD_SEARCH_DRAW_TPOOL',	70);
define('CMD_SEARCH_ADD_LOG',	71);
define('CMD_QUERY_GET_STRING',	96);
define('CMD_QUERY_GET_TERMS',	97);
define('CMD_QUERY_GET_CORRECTED',	98);
define('CMD_QUERY_GET_EXPANDED',	99);
define('CMD_OK',	128);
define('CMD_ERR',	129);
define('CMD_SEARCH_RESULT_DOC',	140);
define('CMD_SEARCH_RESULT_FIELD',	141);
define('CMD_DOC_TERM',	160);
define('CMD_DOC_VALUE',	161);
define('CMD_DOC_INDEX',	162);
define('CMD_INDEX_REQUEST',	163);
define('CMD_IMPORT_HEADER',	191);
define('CMD_SEARCH_SET_SORT',	192);
define('CMD_SEARCH_SET_CUT',	193);
define('CMD_SEARCH_SET_NUMERIC',	194);
define('CMD_SEARCH_SET_COLLAPSE',	195);
define('CMD_SEARCH_KEEPALIVE',	196);
define('CMD_QUERY_INIT',	224);
define('CMD_QUERY_PARSE',	225);
define('CMD_QUERY_TERM',	226);
define('CMD_QUERY_RANGEPROC',	227);
define('CMD_QUERY_RANGE',	228);
define('CMD_QUERY_VALCMP',	229);
define('CMD_QUERY_PREFIX',	230);
define('CMD_QUERY_PARSEFLAG',	231);
define('CMD_SORT_TYPE_RELEVANCE',	0);
define('CMD_SORT_TYPE_DOCID',	1);
define('CMD_SORT_TYPE_VALUE',	2);
define('CMD_SORT_TYPE_MASK',	0x3f);
define('CMD_SORT_FLAG_ASCENDING',	0x80);
define('CMD_QUERY_OP_AND',	0);
define('CMD_QUERY_OP_OR',	1);
define('CMD_QUERY_OP_AND_NOT',	2);
define('CMD_QUERY_OP_XOR',	3);
define('CMD_QUERY_OP_AND_MAYBE',	4);
define('CMD_QUERY_OP_FILTER',	5);
define('CMD_RANGE_PROC_STRING',	0);
define('CMD_RANGE_PROC_DATE',	1);
define('CMD_RANGE_PROC_NUMBER',	2);
define('CMD_VALCMP_LE',	0);
define('CMD_VALCMP_GE',	1);
define('CMD_PARSE_FLAG_BOOLEAN',	1);
define('CMD_PARSE_FLAG_PHRASE',	2);
define('CMD_PARSE_FLAG_LOVEHATE',	4);
define('CMD_PARSE_FLAG_BOOLEAN_ANY_CASE',	8);
define('CMD_PARSE_FLAG_WILDCARD',	16);
define('CMD_PARSE_FLAG_PURE_NOT',	32);
define('CMD_PARSE_FLAG_PARTIAL',	64);
define('CMD_PARSE_FLAG_SPELLING_CORRECTION',	128);
define('CMD_PARSE_FLAG_SYNONYM',	256);
define('CMD_PARSE_FLAG_AUTO_SYNONYMS',	512);
define('CMD_PARSE_FLAG_AUTO_MULTIWORD_SYNONYMS',	1536);
define('CMD_PREFIX_NORMAL',	0);
define('CMD_PREFIX_BOOLEAN',	1);
define('CMD_INDEX_WEIGHT_MASK',	0x3f);
define('CMD_INDEX_FLAG_WITHPOS',	0x40);
define('CMD_INDEX_FLAG_SAVEVALUE',	0x80);
define('CMD_INDEX_FLAG_CHECKSTEM',	0x80);
define('CMD_VALUE_FLAG_NUMERIC',	0x80);
define('CMD_INDEX_REQUEST_ADD',	0);
define('CMD_INDEX_REQUEST_UPDATE',	1);
define('CMD_ERR_UNKNOWN',	600);
define('CMD_ERR_NOPROJECT',	401);
define('CMD_ERR_TOOLONG',	402);
define('CMD_ERR_INVALIDCHAR',	403);
define('CMD_ERR_EMPTY',	404);
define('CMD_ERR_NOACTION',	405);
define('CMD_ERR_RUNNING',	406);
define('CMD_ERR_REBUILDING',	407);
define('CMD_ERR_WRONGPLACE',	450);
define('CMD_ERR_WRONGFORMAT',	451);
define('CMD_ERR_EMPTYQUERY',	452);
define('CMD_ERR_TIMEOUT',	501);
define('CMD_ERR_IOERR',	502);
define('CMD_ERR_NOMEM',	503);
define('CMD_ERR_BUSY',	504);
define('CMD_ERR_UNIMP',	505);
define('CMD_ERR_NODB',	506);
define('CMD_ERR_DBLOCKED',	507);
define('CMD_ERR_CREATE_HOME',	508);
define('CMD_ERR_INVALID_HOME',	509);
define('CMD_ERR_REMOVE_HOME',	510);
define('CMD_ERR_REMOVE_DB',	511);
define('CMD_ERR_STAT',	512);
define('CMD_ERR_OPEN_FILE',	513);
define('CMD_ERR_TASK_CANCELED',	514);
define('CMD_ERR_XAPIAN',	515);
define('CMD_OK_INFO',	200);
define('CMD_OK_PROJECT',	201);
define('CMD_OK_QUERY_STRING',	202);
define('CMD_OK_DB_TOTAL',	203);
define('CMD_OK_QUERY_TERMS',	204);
define('CMD_OK_QUERY_CORRECTED',	205);
define('CMD_OK_SEARCH_TOTAL',	206);
define('CMD_OK_RESULT_BEGIN',	CMD_OK_SEARCH_TOTAL);
define('CMD_OK_RESULT_END',	207);
define('CMD_OK_TIMEOUT_SET',	208);
define('CMD_OK_FINISHED',	209);
define('CMD_OK_LOGGED',	210);
define('CMD_OK_RQST_FINISHED',	250);
define('CMD_OK_DB_CHANGED',	251);
define('CMD_OK_DB_INFO',	252);
define('CMD_OK_DB_CLEAN',	253);
define('CMD_OK_PROJECT_ADD',	254);
define('CMD_OK_PROJECT_DEL',	255);
define('CMD_OK_DB_COMMITED',	256);
define('CMD_OK_DB_REBUILD',	257);
define('CMD_OK_LOG_FLUSHED',	258);
define('PACKAGE_BUGREPORT',	"http://www.xunsearch.com/bugs");
define('PACKAGE_NAME',	"xunsearch");
define('PACKAGE_TARNAME',	"xunsearch");
define('PACKAGE_URL',	"");
define('PACKAGE_VERSION',	"1.0.0a");
define('XS_LIB_ROOT', dirname(__FILE__));
class XSException extends Exception
{
	public function __toString()
	{
		$string = '[' . __CLASS__ . '] ' . $this->getRelPath($this->getFile()) . '(' . $this->getLine() . '): ';
		$string .= $this->getMessage() . ($this->getCode() > 0 ? '(S#' . $this->getCode() . ')' : '');
		return $string;
	}
	public static function getRelPath($file)
	{
		$from = getcwd();
		$pos = strrpos($file, '/');
		$to = substr($file, 0, $pos);
		for ($rel = '';; $rel .= '../')
		{
			if ($from === $to)
				break;
			if ($from === dirname($from))
			{
				$rel .= substr($to, 1);
				break;
			}
			if (!strncmp($from . '/', $to, strlen($from) + 1))
			{
				$rel .= substr($to, strlen($from) + 1);
				break;
			}
			$from = dirname($from);
		}
		if ($rel != '' && substr($rel, -1, 1) != '/')
			$rel .= '/';
		return $rel . substr($file, $pos + 1);
	}
}
class XSErrorException extends XSException
{
	private $_file, $_line;
	public function __construct($code, $message, $file, $line, $previous = null)
	{
		$this->_file = $file;
		$this->_line = $line;
		parent::__construct($message, $code, $previous);
	}
	public function __toString()
	{
		$string = '[' . __CLASS__ . '] ' . $this->getRelPath($this->_file) . '(' . $this->_line . '): ';
		$string .= $this->getMessage() . '(' . $this->getCode() . ')';
		return $string;
	}
}
class XSComponent
{
	public function __get($name)
	{
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return $this->$getter();
		$msg = method_exists($this, 'set' . $name) ? 'Write-only' : 'Undefined';
		$msg .= ' property: ' . get_class($this) . '::$' . $name;
		throw new XSException($msg);
	}
	public function __set($name, $value)
	{
		$setter = 'set' . $name;
		if (method_exists($this, $setter))
			return $this->$setter($value);
		$msg = method_exists($this, 'get' . $name) ? 'Read-only' : 'Undefined';
		$msg .= ' property: ' . get_class($this) . '::$' . $name;
		throw new XSException($msg);
	}
	public function __isset($name)
	{
		return method_exists($this, 'get' . $name);
	}
	public function __unset($name)
	{
		$this->__set($name, null);
	}
}
class XS extends XSComponent
{
	private $_index;
	private $_search;
	private $_scheme, $_bindScheme;
	private $_config;
	public function __construct($file)
	{
		if (strlen($file) < 255 && !is_file($file))
			$file = XS_LIB_ROOT . '/../app/' . $file . '.ini';
		$this->loadIniFile($file);
	}
	public function __destruct()
	{
		$this->_index = null;
		$this->_search = null;
	}
	public function getScheme()
	{
		return $this->_scheme;
	}
	public function setScheme(XSFieldScheme $fs)
	{
		$fs->checkValid(true);
		$this->_scheme = $fs;
		if ($this->_search !== null)
			$this->_search->markResetScheme(true);
	}
	public function restoreScheme()
	{
		if ($this->_scheme !== $this->_bindScheme)
		{
			$this->_scheme = $this->_bindScheme;
			if ($this->_search !== null)
				$this->_search->markResetScheme(true);
		}
	}
	public function getName()
	{
		return $this->_config['project.name'];
	}
	public function getDefaultCharset()
	{
		return isset($this->_config['project.default_charset']) ?
			strtoupper($this->_config['project.default_charset']) : 'UTF-8';
	}
	public function getIndex()
	{
		if ($this->_index === null)
		{
			$conn = isset($this->_config['server.index']) ? $this->_config['server.index'] : 8383;
			$name = $this->_config['project.name'];
			$this->_index = new XSIndex($conn, $this);
		}
		return $this->_index;
	}
	public function getSearch()
	{
		if ($this->_search === null)
		{
			$conn = isset($this->_config['server.search']) ? $this->_config['server.search'] : 8384;
			$name = $this->_config['project.name'];
			$this->_search = new XSSearch($conn, $this);
			$this->_search->setCharset($this->getDefaultCharset());
		}
		return $this->_search;
	}
	public function getFieldId()
	{
		return $this->_scheme->getFieldId();
	}
	public function getFieldTitle()
	{
		return $this->_scheme->getFieldTitle();
	}
	public function getFieldBody()
	{
		return $this->_scheme->getFieldBody();
	}
	public function getField($name, $throw = true)
	{
		return $this->_scheme->getField($name, $throw);
	}
	public function getAllFields()
	{
		return $this->_scheme->getAllFields();
	}
	public static function autoload($name)
	{
		$file = XS_LIB_ROOT . '/' . $name . '.class.php';
		if (file_exists($file))
			require_once $file;
	}
	public static function convert($data, $to, $from)
	{
		if ($to == $from)
			return $data;
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$data[$key] = self::convert($value, $to, $from);
			}
			return $data;
		}
		if (is_string($data) && preg_match('/[\x81-\xfe]/', $data))
		{
			if (function_exists('mb_convert_encoding'))
				return mb_convert_encoding($data, $to, $from);
			else if (function_exists('iconv'))
				return iconv($from, $to . '//TRANSLIT', $data);
			else
				throw new XSException('Cann\'t find the mbstring or iconv extension to convert encoding');
		}
		return $data;
	}
	private function loadIniFile($file)
	{
		$cache = false;
		if (strlen($file) < 255 && file_exists($file))
		{
			$cache_key = md5(__CLASS__ . '::ini::' . realpath($file));
			$cache_write = '';
			if (function_exists('apc_fetch'))
			{
				$cache = apc_fetch($cache_key);
				$cache_write = 'apc_store';
			}
			else if (function_exists('xcache_get'))
			{
				$cache = xcache_get($cache_key);
				$cache_write = 'xcache_set';
			}
			else if (function_exists('eaccelerator_get'))
			{
				$cache = eaccelerator_get($cache_key);
				$cache_write = 'eaccelerator_put';
			}
			if ($cache && filemtime($file) < $cache['mtime'])
			{
				$this->_scheme = $this->_bindScheme = $cache['scheme'];
				$this->_config = $cache['config'];
				return;
			}
			$this->_config = parse_ini_file($file, true, INI_SCANNER_RAW);
		}
		else
		{
			$this->_config = parse_ini_string($file, true, INI_SCANNER_RAW);
		}
		if ($this->_config === false)
			throw new XSException('Failed to parse project config file/string: `' . substr($file, 0, 10) . '...\'');
		$scheme = new XSFieldScheme;
		foreach ($this->_config as $key => $value)
		{
			if (is_array($value))
				$scheme->addField($key, $value);
		}
		$scheme->checkValid(true);
		if (!isset($this->_config['project.name']))
			$this->_config['project.name'] = basename($file, '.ini');
		$this->_scheme = $this->_bindScheme = $scheme;
		if ($cache_write != '')
		{
			$cache['mtime'] = filemtime($file);
			$cache['scheme'] = $this->_scheme;
			$cache['config'] = $this->_config;
			call_user_func($cache_write, $cache_key, $cache);
		}
	}
}
spl_autoload_register('XS::autoload', true, true);
function xs_error_handler($errno, $error, $file, $line)
{
	if ($errno & ini_get('error_reporting'))
		throw new XSErrorException($errno, $error, $file, $line);
}
set_error_handler('xs_error_handler');
class XSDocument implements ArrayAccess, IteratorAggregate
{
	private $_data;
	private $_terms, $_texts;
	private $_charset, $_meta;
	public function __construct($p = null, $d = null)
	{
		if (is_string($p))
			$this->_charset = strtoupper($p);
		else if (is_array($p))
			$this->_meta = $p;
		$this->_data = is_array($d) ? $d : array();
	}
	public function __get($name)
	{
		if (!isset($this->_data[$name]))
			return null;
		return $this->autoConvert($this->_data[$name]);
	}
	public function __set($name, $value)
	{
		if ($value === null)
			unset($this->_data[$name]);
		else
			$this->_data[$name] = $value;
	}
	public function __call($name, $args)
	{
		if ($this->_meta !== null)
		{
			$name = strtolower($name);
			if (isset($this->_meta[$name]))
				return $this->_meta[$name];
		}
		throw new XSException('Call to undefined method `' . get_class($this) . '::' . $name . '()\'');
	}
	public function getCharset()
	{
		return $this->_charset;
	}
	public function setCharset($charset)
	{
		$this->_charset = strtoupper($charset);
		if ($this->_charset == 'UTF8')
			$this->_charset = 'UTF-8';		
	}
	public function setFields($data)
	{
		if ($data === null)
			$this->_data = array();
		else
			$this->_data = array_merge($this->_data, $data);
	}
	public function setField($name, $value)
	{
		$this->__set($name, $value);
	}
	public function f($name)
	{
		return $this->__get(strval($name));
	}
	public function getAddTerms($field)
	{
		$field = strval($field);
		if ($this->_terms === null || !isset($this->_terms[$field]))
			return null;
		$terms = array();
		foreach ($this->_terms as $term => $weight)
		{
			$term = $this->autoConvert($term);
			$terms[$term] = $weight;
		}
		return $terms;
	}
	public function getAddText($field)
	{
		$field = strval($field);
		if ($this->_texts === null || !isset($this->_texts[$field]))
			return null;
		return $this->autoConvert($this->_texts[$field]);
	}
	public function addTerm($field, $term, $weight = 1)
	{
		$field = strval($field);
		if (!is_array($this->_terms))
			$this->_terms = array();
		if (!isset($this->_terms[$field]))
			$this->_terms[$field] = array(array($term => $weight));
		else if (!isset($this->_terms[$field][$term]))
			$this->_terms[$field][$term] = $weight;
		else
			$this->_terms[$field][$term] += $weight;
	}
	public function addIndex($field, $text)
	{
		$field = strval($field);
		if (!is_array($this->_texts))
			$this->_texts = array();
		if (!isset($this->_texts[$field]))
			$this->_texts[$field] = strval($text);
		else
			$this->_texts[$field] .= "\n" . strval($text);
	}
	public function getIterator()
	{
		if ($this->_charset !== null && $this->_charset !== 'UTF-8')
		{
			$from = $this->_meta === null ? $this->_charset : 'UTF-8';
			$to = $this->_meta === null ? 'UTF-8' : $this->_charset;
			return new ArrayIterator(XS::convert($this->_data, $to, $from));
		}
		return new ArrayIterator($this->_data);
	}
	public function offsetExists($name)
	{
		return isset($this->_data[$name]);
	}
	public function offsetGet($name)
	{
		return $this->__get($name);
	}
	public function offsetSet($name, $value)
	{
		if (!is_null($name))
			$this->__set(strval($name), $value);
	}
	public function offsetUnset($name)
	{
		unset($this->_data[$name]);
	}
	public function beforeSubmit(XSIndex $index)
	{
		if ($this->_charset === null)
			$this->_charset = $index->xs->getDefaultCharset();
		return true;
	}
	public function afterSubmit($index)
	{
	}
	private function autoConvert($value)
	{
		if ($this->_charset === null || $this->_charset == 'UTF-8'
			|| !is_string($value) || !preg_match('/[\x81-\xfe]/', $value))
		{
			return $value;
		}
		$from = $this->_meta === null ? $this->_charset : 'UTF-8';
		$to = $this->_meta === null ? 'UTF-8' : $this->_charset;
		return XS::convert($value, $to, $from);
	}
}
class XSFieldScheme implements IteratorAggregate
{
	const MIXED_VNO = 255;
	private $_fields = array();
	private $_typeMap = array();
	private $_vnoMap = array();
	private static $_logger;
	public function __toString()
	{
		$str = '';
		foreach ($this->_fields as $field)
		{
			$str .= $field->toConfig() . "\n";
		}
		return $str;
	}
	public function getFieldId()
	{
		if (isset($this->_typeMap[XSFieldMeta::TYPE_ID]))
		{
			$name = $this->_typeMap[XSFieldMeta::TYPE_ID];
			return $this->_fields[$name];
		}
		return false;
	}
	public function getFieldTitle()
	{
		if (isset($this->_typeMap[XSFieldMeta::TYPE_TITLE]))
		{
			$name = $this->_typeMap[XSFieldMeta::TYPE_TITLE];
			return $this->_fields[$name];
		}
		return false;
	}
	public function getFieldBody()
	{
		if (isset($this->_typeMap[XSFieldMeta::TYPE_BODY]))
		{
			$name = $this->_typeMap[XSFieldMeta::TYPE_BODY];
			return $this->_fields[$name];
		}
		return false;
	}
	public function getField($name, $throw = true)
	{
		if (is_int($name))
		{
			if (!isset($this->_vnoMap[$name]))
			{
				if ($throw === true)
					throw new XSException('Not exists field with vno: `' . $name . '\'');
				return false;
			}
			$name = $this->_vnoMap[$name];
		}
		if (!isset($this->_fields[$name]))
		{
			if ($throw === true)
				throw new XSException('Not exists field with name: `' . $name . '\'');
			return false;
		}
		return $this->_fields[$name];
	}
	public function getAllFields()
	{
		return $this->_fields;
	}
	public function getVnoMap()
	{
		return $this->_vnoMap;
	}
	public function addField($field, $config = null)
	{
		if (!$field instanceof XSFieldMeta)
			$field = new XSFieldMeta($field, $config);
		if ($field->isSpeical())
		{
			if (isset($this->_typeMap[$field->type]))
			{
				$prev = $this->_typeMap[$field->type];
				throw new XSException('Duplicated ' . strtoupper($config['type']) . ' field: `' . $field->name . '\' and `' . $prev . '\'');
			}
			$this->_typeMap[$field->type] = $field->name;
		}
		$field->vno = ($field->type == XSFieldMeta::TYPE_BODY) ? self::MIXED_VNO : count($this->_vnoMap);
		$this->_vnoMap[$field->vno] = $field->name;
		if ($field->type == XSFieldMeta::TYPE_ID)
			$this->_fields = array_merge(array($field->name => $field), $this->_fields);
		else
			$this->_fields[$field->name] = $field;
	}
	public function checkValid($throw = false)
	{
		if (!isset($this->_typeMap[XSFieldMeta::TYPE_ID]))
		{
			if ($throw)
				throw new XSException('Missing field of type ID');
			return false;
		}
		return true;
	}
	public function getIterator()
	{
		return new ArrayIterator($this->_fields);
	}
	public static function logger()
	{
		if (self::$_logger === null)
		{
			$scheme = new self;
			$scheme->addField('id', array('type' => 'id'));
			$scheme->addField('pinyin');
			$scheme->addField('partial');
			$scheme->addField('total', array('type' => 'numeric'));
			$scheme->addField('lastnum', array('type' => 'numeric'));
			$scheme->addField('currnum', array('type' => 'numeric'));
			$scheme->addField('currtag', array('type' => 'numeric'));
			$scheme->addField('body', array('type' => 'body'));
			self::$_logger = $scheme;
		}
		return self::$_logger;
	}
}
class XSFieldMeta
{
	const MAX_WDF = 0x3f;
	const TYPE_STRING = 0;
	const TYPE_NUMERIC = 1;
	const TYPE_DATE = 2;
	const TYPE_ID = 10;
	const TYPE_TITLE = 11;
	const TYPE_BODY = 12;
	const FLAG_INDEX_SELF = 0x01;
	const FLAG_INDEX_MIXED = 0x02;
	const FLAG_INDEX_BOTH = 0x03;
	const FLAG_WITH_POSITION = 0x10;
	public $name;
	public $cutlen = 0;
	public $weight = 1;
	public $type = 0;
	public $vno = 0;
	private $tokenizer = XSTokenizer::DFL;
	private $flag = 0;
	private static $_tokenizers = array();
	public function __construct($name, $config = null)
	{
		$this->name = strval($name);
		if (is_array($config))
			$this->fromConfig($config);
	}
	public function __toString()
	{
		return $this->name;
	}
	public function val($value)
	{
		if ($this->type == self::TYPE_DATE)
		{
			if (!is_numeric($value) || strlen($value) != 8)
				$value = date('Ymd', is_numeric($value) ? $value : strtotime($value));
		}
		return $value;
	}
	public function withPos()
	{
		return ($this->flag & self::FLAG_WITH_POSITION) ? true : false;
	}
	public function isBoolIndex()
	{
		return ($this->tokenizer !== XSTokenizer::DFL);
	}
	public function isNumeric()
	{
		return ($this->type == self::TYPE_NUMERIC);
	}
	public function isSpeical()
	{
		return ($this->type == self::TYPE_ID || $this->type == self::TYPE_TITLE || $this->type == self::TYPE_BODY);
	}
	public function hasIndex()
	{
		return ($this->flag & self::FLAG_INDEX_BOTH) ? true : false;
	}
	public function hasIndexMixed()
	{
		return ($this->flag & self::FLAG_INDEX_MIXED) ? true : false;
	}
	public function hasIndexSelf()
	{
		return ($this->flag & self::FLAG_INDEX_SELF) ? true : false;
	}
	public function hasCustomTokenizer()
	{
		return ($this->tokenizer !== XSTokenizer::DFL);
	}
	public function getCustomTokenizer()
	{
		if (isset(self::$_tokenizers[$this->tokenizer]))
			return self::$_tokenizers[$this->tokenizer];
		else
		{
			if (($pos1 = strpos($this->tokenizer, '(')) !== false
				&& ($pos2 = strrpos($this->tokenizer, ')', $pos1 + 1)))
			{
				$name = 'XSTokenizer' . ucfirst(trim(substr($this->tokenizer, 0, $pos1)));
				$arg = substr($this->tokenizer, $pos1 + 1, $pos2 - $pos1 - 1);
			}
			else
			{
				$name = 'XSTokenizer' . ucfirst($this->tokenizer);
				$arg = null;
			}
			if (!class_exists($name))
				throw new XSException('Undefined custom tokenizer `' . $this->tokenizer . '\' for field `' . $this->name . '\'');
			$obj = $arg === null ? new $name : new $name($arg);
			if (!$obj instanceof XSTokenizer)
				throw new XSException($name . ' for field `' . $this->name . '\' dose not implement the interface: XSTokenizer');
			self::$_tokenizers[$this->tokenizer] = $obj;
			return $obj;
		}
	}
	public function toConfig()
	{
		$str = "[" . $this->name . "]\n";
		if ($this->type === self::TYPE_NUMERIC)
			$str .= "type = numeric\n";
		else if ($this->type === self::TYPE_DATE)
			$str .= "type = date\n";
		else if ($this->type === self::TYPE_ID)
			$str .= "type = id\n";
		else if ($this->type === self::TYPE_TITLE)
			$str .= "type = title\n";
		else if ($this->type === self::TYPE_BODY)
			$str .= "type = body\n";
		if ($this->type !== self::TYPE_BODY && ($index = ($this->flag & self::FLAG_INDEX_BOTH)))
		{
			if ($index === self::FLAG_INDEX_BOTH)
			{
				if ($this->type !== self::TYPE_TITLE)
					$str .= "index = both\n";
			}
			else if ($index === self::FLAG_INDEX_MIXED)
			{
				$str .= "index = mixed\n";
			}
			else
			{
				if ($this->type != self::TYPE_ID)
					$str .= "index = self\n";
			}
		}
		if ($this->type !== self::TYPE_ID && $this->tokenizer !== XSTokenizer::DFL)
			$str .= "tokenizer = " . $this->tokenizer . "\n";
		if ($this->cutlen > 0 && !($this->cutlen === 300 && $this->type === self::TYPE_BODY))
			$str .= "cutlen = " . $this->cutlen . "\n";
		if ($this->weight !== 1 && !($this->weight === 5 && $this->type === self::TYPE_TITLE))
			$str .= "weight = " . $this->weight . "\n";
		if ($this->flag & self::FLAG_WITH_POSITION)
		{
			if ($this->type !== self::TYPE_BODY && $this->type !== self::TYPE_TITLE)
				$str .= "phrase = yes\n";
		}
		else
		{
			if ($this->type === self::TYPE_BODY || $this->type === self::TYPE_TITLE)
				$str .= "phrase = no\n";
		}
		return $str;
	}
	public function fromConfig($config)
	{
		if (isset($config['type']))
		{
			$predef = 'self::TYPE_' . strtoupper($config['type']);
			if (defined($predef))
			{
				$this->type = constant($predef);
				if ($this->type == self::TYPE_ID)
				{
					$this->flag = self::FLAG_INDEX_SELF;
					$this->tokenizer = 'full';
				}
				else if ($this->type == self::TYPE_TITLE)
				{
					$this->flag = self::FLAG_INDEX_BOTH | self::FLAG_WITH_POSITION;
					$this->weight = 5;
				}
				else if ($this->type == self::TYPE_BODY)
				{
					$this->vno = XSFieldScheme::MIXED_VNO;
					$this->flag = self::FLAG_INDEX_SELF | self::FLAG_WITH_POSITION;
					$this->cutlen = 300;
				}
			}
		}
		if (isset($config['index']) && $this->type != self::TYPE_BODY)
		{
			$predef = 'self::FLAG_INDEX_' . strtoupper($config['index']);
			if (defined($predef))
			{
				$this->flag &= ~ self::FLAG_INDEX_BOTH;
				$this->flag |= constant($predef);
			}
			if ($this->type == self::TYPE_ID)
				$this->flag |= self::FLAG_INDEX_SELF;
		}
		if (isset($config['cutlen']))
			$this->cutlen = intval($config['cutlen']);
		if (isset($config['weight']) && $this->type != self::TYPE_BODY)
			$this->weight = intval($config['weight']) & self::MAX_WDF;
		if (isset($config['phrase']))
		{
			if (!strcasecmp($config['phrase'], 'yes'))
				$this->flag |= self::FLAG_WITH_POSITION;
			else if (!strcasecmp($config['phrase'], 'no'))
				$this->flag &= ~ self::FLAG_WITH_POSITION;
		}
		if (isset($config['tokenizer']) && $this->type != self::TYPE_ID
			&& $config['tokenizer'] != 'default')
		{
			$this->tokenizer = $config['tokenizer'];
		}
	}
}
class XSIndex extends XSServer
{
	private $_buf = '';
	private $_bufSize = 0;
	public function clean()
	{
		$this->execCommand(CMD_INDEX_CLEAN_DB, CMD_OK_DB_CLEAN);
	}
	public function add(XSDocument $doc)
	{
		$this->update($doc, true);
	}
	public function update(XSDocument $doc, $add = false)
	{
		if ($doc->beforeSubmit($this) === false)
			return;
		$fid = $this->xs->getFieldId();
		$key = $doc->f($fid);
		if ($key === null || $key === '')
			throw new XSException('Missing value of primarky key (FIELD:' . $fid . ')');
		$cmd = new XSCommand(CMD_INDEX_REQUEST, CMD_INDEX_REQUEST_ADD);
		if ($add !== true)
		{
			$cmd->arg1 = CMD_INDEX_REQUEST_UPDATE;
			$cmd->arg2 = $fid->vno;
			$cmd->buf = $key;
		}
		$cmds = array($cmd);
		foreach ($this->xs->getAllFields() as $field) /* @var $field XSFieldMeta */
		{
			if (($value = $doc->f($field)) !== null)
			{
				$varg = $field->isNumeric() ? CMD_VALUE_FLAG_NUMERIC : 0;
				$value = $field->val($value);
				if (!$field->hasCustomTokenizer())
				{
					$wdf = $field->weight | ($field->withPos() ? CMD_INDEX_FLAG_WITHPOS : 0);
					if ($field->hasIndexMixed())
						$cmds[] = new XSCommand(CMD_DOC_INDEX, $wdf, XSFieldScheme::MIXED_VNO, $value);
					if ($field->hasIndexSelf())
					{
						$wdf |= $field->isNumeric() ? 0 : CMD_INDEX_FLAG_SAVEVALUE;
						$cmds[] = new XSCommand(CMD_DOC_INDEX, $wdf, $field->vno, $value);
					}
					if (!$field->hasIndexSelf() || $field->isNumeric())
						$cmds[] = new XSCommand(CMD_DOC_VALUE, $varg, $field->vno, $value);
				}
				else
				{
					if ($field->hasIndex())
					{
						$terms = $field->getCustomTokenizer()->getTokens($value, $doc);
						if ($field->hasIndexSelf())
						{
							foreach ($terms as $term)
							{
								$term = strtolower($term);
								$cmds[] = new XSCommand(CMD_DOC_TERM, 0, $field->vno, $term);
							}
						}
						if ($field->hasIndexMixed())
						{
							$mtext = implode(' ', $terms);
							$cmds[] = new XSCommand(CMD_DOC_INDEX, $field->weight, XSFieldScheme::MIXED_VNO, $mtext);
						}
					}
					$cmds[] = new XSCommand(CMD_DOC_VALUE, $varg, $field->vno, $value);
				}
			}
			if (($terms = $doc->getAddTerms($field)) !== null)
			{
				$wdf1 = $field->isBoolIndex() ? 0 : CMD_INDEX_FLAG_CHECKSTEM;
				foreach ($terms as $term => $wdf)
				{
					$term = strtolower($term);
					$wdf2 = $field->isBoolIndex() ? 0 : $wdf * $field->weight;
					while ($wdf2 > XSFieldMeta::MAX_WDF)
					{
						$cmds[] = new XSCommand(CMD_DOC_TERM, $wdf1 | XSFieldMeta::MAX_WDF, $field->vno, $term);
						$wdf2 -= XSFieldMeta::MAX_WDF;
					}
					$cmds[] = new XSCommand(CMD_DOC_TERM, $wdf1 | $wdf2, $field->vno, $term);
				}
			}
			if (($text = $doc->getAddText($field)) !== null)
			{
				if (!$field->hasCustomTokenizer())
				{
					$wdf = $field->weight | ($field->withPos() ? CMD_INDEX_FLAG_WITHPOS : 0);
					$cmds[] = new XSCommand(CMD_DOC_INDEX, $arg1, $field->vno, $text);
				}
				else
				{
					$wdf = $field->isBoolIndex() ? 0 : ($field->weight | CMD_INDEX_FLAG_CHECKSTEM);
					$terms = $field->getCustomTokenizer()->getTokens($text, $doc);
					foreach ($terms as $term)
					{
						$term = strtolower($term);
						$cmds[] = new XSCommand(CMD_DOC_TERM, $wdf, $field->vno, $term);
					}
				}
			}
		}
		$cmds[] = new XSCommand(CMD_INDEX_SUBMIT);
		if ($this->_bufSize > 0)
			$this->appendBuffer(implode('', $cmds));
		else
		{
			for ($i = 0; $i < count($cmds) - 1; $i++)
				$this->execCommand($cmds[$i]);
			$this->execCommand($cmds[$i], CMD_OK_RQST_FINISHED);
		}
		$doc->afterSubmit($this);
	}
	public function del($term, $field = null)
	{
		$field = $field === null ? $this->xs->getFieldId() : $this->xs->getField($field);
		$cmds = array();
		$terms = is_array($term) ? array_unique($term) : array($term);
		$terms = XS::convert($terms, 'UTF-8', $this->xs->getDefaultCharset());
		foreach ($terms as $term)
		{
			$cmds[] = new XSCommand(CMD_INDEX_REMOVE, 0, $field->vno, strtolower($term));
		}
		if ($this->_bufSize > 0)
			$this->appendBuffer(implode('', $cmds));
		else if (count($cmds) == 1)
			$this->execCommand($cmds[0], CMD_OK_RQST_FINISHED);
		else
		{
			$cmd = array('cmd' => CMD_INDEX_EXDATA, 'buf' => implode('', $cmds));
			$this->execCommand($cmd, CMD_OK_RQST_FINISHED);
		}
	}
	public function addExdata($data)
	{
		if (strlen($data) < 255 && file_exists($data) && ($data = file_get_contents($data) === false))
			throw new XSException('Failed to read exdata from file');
		$first = ord(substr($data, 0, 1));
		if ($first != CMD_IMPORT_HEADER && $first != CMD_INDEX_REQUEST
			&& $first != CMD_INDEX_REMOVE && $first != CMD_INDEX_EXDATA)
		{
			throw new XSException('Invalid start command of exdata (CMD:' . $first . ')');
		}
		$cmd = array('cmd' => CMD_INDEX_EXDATA, 'buf' => $data);
		$this->execCommand($cmd, CMD_OK_RQST_FINISHED);
	}
	public function openBuffer($size = 4)
	{
		if ($this->_buf !== '')
			$this->addExdata($this->_buf);
		$this->_bufSize = intval($size) << 20;
		$this->_buf = '';
	}
	public function closeBuffer()
	{
		$this->openBuffer(0);
	}
	public function beginRebuild()
	{
		$this->execCommand(array('cmd' => CMD_INDEX_REBUILD, 'arg1' => 0), CMD_OK_DB_REBUILD);
	}
	public function endRebuild()
	{
		$this->execCommand(array('cmd' => CMD_INDEX_REBUILD, 'arg1' => 1), CMD_OK_DB_REBUILD);
	}
	public function setDb($name)
	{
		$this->execCommand(array('cmd' => CMD_INDEX_SET_DB, 'buf' => $name), CMD_OK_DB_CHANGED);
	}
	public function flushLogging()
	{
		try
		{
			$this->execCommand(CMD_FLUSH_LOGGING, CMD_OK_LOG_FLUSHED);
		}
		catch (XSException $e)
		{
			if ($e->getCode() === CMD_ERR_BUSY)
				return false;
			throw $e;
		}
		return true;
	}
	public function flushIndex()
	{
		try
		{
			$this->execCommand(CMD_INDEX_COMMIT, CMD_OK_DB_COMMITED);
		}
		catch (XSException $e)
		{
			if ($e->getCode() === CMD_ERR_BUSY || $e->getCode() === CMD_ERR_RUNNING)
				return false;
			throw $e;
		}
		return true;
	}
	public function close($ioerr = false)
	{
		$this->closeBuffer();
		parent::close($ioerr);
	}
	private function appendBuffer($buf)
	{
		$this->_buf .= $buf;
		if (strlen($this->_buf) >= $this->_bufSize)
		{
			$this->addExdata($this->_buf);
			$this->_buf = '';
		}
	}
}
class XSSearch extends XSServer
{
	const PAGE_SIZE = 10;
	const LOB_DB = 'log_db';
	private $_defaultOp = CMD_QUERY_OP_AND;
	private $_prefix, $_fieldSet, $_resetScheme = false;
	private $_query, $_terms, $_count;
	private $_lastCount, $_highlight;
	private $_limit = 0, $_offset = 0;
	private $_charset = 'UTF-8';
	public function open($conn)
	{
		parent::open($conn);
		$this->_prefix = array();
		$this->_fieldSet = false;
		$this->_lastCount = false;
	}
	public function setCharset($charset)
	{
		$this->_charset = strtoupper($charset);
		if ($this->_charset == 'UTF8')
			$this->_charset = 'UTF-8';
		return $this;
	}
	public function setFuzzy($value = true)
	{
		$this->_defaultOp = $value === true ? CMD_QUERY_OP_OR : CMD_QUERY_OP_AND;
		return $this;
	}
	public function getQuery($query = null)
	{
		$query = $query === null ? '' : $this->preQueryString($query);
		$cmd = new XSCommand(CMD_QUERY_GET_STRING, 0, $this->_defaultOp, $query);
		$res = $this->execCommand($cmd, CMD_OK_QUERY_STRING);
		return XS::convert($res->buf, $this->_charset, 'UTF-8');
	}
	public function setQuery($query)
	{
		$this->clearQuery();
		if ($query !== null)
		{
			$this->_query = $query;
			$this->addQueryString($query);
		}
		return $this;
	}
	public function setSort($field, $asc = false)
	{
		if ($field === null)
			$cmd = new XSCommand(CMD_SEARCH_SET_SORT, CMD_SORT_TYPE_RELEVANCE);
		else
		{
			$type = CMD_SORT_TYPE_VALUE | ($asc ? CMD_SORT_FLAG_ASCENDING : 0);
			$vno = $this->xs->getField($field, true)->vno;
			$cmd = new XSCommand(CMD_SEARCH_SET_SORT, $type, $vno);
		}
		$this->execCommand($cmd);
	}
	public function setCollapse($field, $num = 1)
	{
		$vno = $field === null ? XSFieldScheme::MIXED_VNO : $this->xs->getField($field, true)->vno;
		$max = min(255, intval($num));
		$cmd = new XSCommand(CMD_SEARCH_SET_COLLAPSE, $max, $vno);
		$this->execCommand($cmd);
		return $this;
	}
	public function addRange($field, $from, $to)
	{
		if ($from !== null || $to !== null)
		{
			if (strlen($from) > 255 || strlen($to) > 255)
				throw new XSException('Value of range is too long');
			$vno = $this->xs->getField($field)->vno;
			$from = XS::convert($from, 'UTF-8', $this->_charset);
			$to = XS::convert($to, 'UTF-8', $this->_charset);
			if ($from === null)
				$cmd = new XSCommand(CMD_QUERY_VALCMP, CMD_QUERY_OP_FILTER, $vno, $to, chr(CMD_VALCMP_LE));
			else if ($to === null)
				$cmd = new XSCommand(CMD_QUERY_VALCMP, CMD_QUERY_OP_FILTER, $vno, $from, chr(CMD_VALCMP_GE));
			else
				$cmd = new XSCommand(CMD_QUERY_RANGE, CMD_QUERY_OP_FILTER, $vno, $from, $to);
			$this->execCommand($cmd);
		}
		return $this;
	}
	public function addWeight($field, $term, $weight = 1)
	{
		$this->addQueryTerm($field, $term, CMD_QUERY_OP_AND_MAYBE, $weight);
		return $this;
	}
	public function setLimit($limit, $offset = 0)
	{
		$this->_limit = intval($limit);
		$this->_offset = intval($offset);
		return $this;
	}
	public function setDb($name)
	{
		$this->execCommand(array('cmd' => CMD_SEARCH_SET_DB, 'buf' => strval($name)));
		return $this;
	}
	public function addDb($name)
	{
		$this->execCommand(array('cmd' => CMD_SEARCH_ADD_DB, 'buf' => strval($name)));
		return $this;
	}
	public function markResetScheme()
	{
		$this->_resetScheme = true;
	}
	public function terms($query = null, $convert = true)
	{
		$query = $query === null ? '' : $this->preQueryString($query);
		if ($query === '' && $this->_terms !== null)
			$ret = $this->_terms;
		else
		{
			$cmd = new XSCommand(CMD_QUERY_GET_TERMS, 0, $this->_defaultOp, $query);
			$res = $this->execCommand($cmd, CMD_OK_QUERY_TERMS);
			$ret = array();
			$tmps = explode(' ', $res->buf);
			for ($i = 0; $i < count($tmps); $i++)
			{
				if ($tmps[$i] === '' || strpos($tmps[$i], ':') !== false)
					continue;
				$ret[] = $tmps[$i];
			}
			if ($query === '')
				$this->_terms = $ret;
		}
		return $convert ? XS::convert($ret, $this->_charset, 'UTF-8') : $ret;
	}
	public function count($query = null)
	{
		$query = $query === null ? '' : $this->preQueryString($query);
		if ($query === '' && $this->_count !== null)
			return $this->_count;
		$cmd = new XSCommand(CMD_SEARCH_GET_TOTAL, 0, $this->_defaultOp, $query);
		$res = $this->execCommand($cmd, CMD_OK_SEARCH_TOTAL);
		$ret = unpack('Icount', $res->buf);
		if ($query === '')
			$this->_count = $ret['count'];
		return $ret['count'];
	}
	public function search($query = null)
	{
		$this->_highlight = $query;
		$query = $query === null ? '' : $this->preQueryString($query);
		$page = pack('II', $this->_offset, $this->_limit > 0 ? $this->_limit : self::PAGE_SIZE);
		$cmd = new XSCommand(CMD_SEARCH_GET_RESULT, 0, $this->_defaultOp, $query, $page);
		$res = $this->execCommand($cmd, CMD_OK_RESULT_BEGIN);
		$tmp = unpack('Icount', $res->buf);
		$this->_lastCount = $tmp['count'];
		$ret = array();
		$vnoes = $this->xs->getScheme()->getVnoMap();
		while (true)
		{
			$res = $this->getRespond();
			if ($res->cmd == CMD_SEARCH_RESULT_DOC)
			{
				$doc = new XSDocument(unpack('Idocid/Irank/Iccount/ipercent/fweight', $res->buf));
				$doc->setCharset($this->_charset);
				$ret[] = $doc;
			}
			else if ($res->cmd == CMD_SEARCH_RESULT_FIELD)
			{
				if (isset($doc))
				{
					$name = isset($vnoes[$res->arg]) ? $vnoes[$res->arg] : $res->arg;
					$doc->setField($name, $res->buf);
				}
			}
			else if ($res->cmd == CMD_OK && $res->arg == CMD_OK_RESULT_END)
			{
				break;
			}
			else
			{
				$msg = 'Unexpected respond in search {CMD:' . $res->cmd . ', ARG:' . $res->arg . '}';
				throw new XSException($msg);
			}
		}
		if ($query === '')
			$this->_count = $tmp['count'];
		$this->_limit = $this->_offset = 0;
		return $ret;
	}
	public function getLastCount()
	{
		return $this->_lastCount;
	}
	public function getDbTotal()
	{
		$cmd = new XSCommand(CMD_SEARCH_DB_TOTAL);
		$res = $this->execCommand($cmd, CMD_OK_DB_TOTAL);
		$tmp = unpack('Itotal', $res->buf);
		return $tmp['total'];
	}
	public function getHotQuery($limit = 6, $type = 'total')
	{
		$ret = array();
		$limit = max(1, min(50, intval($limit)));
		$this->xs->setScheme(XSFieldScheme::logger());
		try
		{
			$this->setDb(self::LOB_DB)->setLimit($limit);
			if ($type !== 'lastnum' && $type !== 'currnum')
				$type = 'total';
			$result = $this->search($type . ':1');
			foreach ($result as $doc) /* @var $doc XSDocument */
			{
				$body = $doc->body;
				$ret[$body] = $doc->f($type);
			}
			$this->setDb(null);
		}
		catch (XSException $e)
		{
			if ($e->getCode() != CMD_ERR_XAPIAN)
				throw $e;
		}
		$this->xs->restoreScheme();
		return $ret;
	}
	public function getRelatedQuery($query = null, $limit = 6)
	{
		$ret = array();
		$limit = max(1, min(20, intval($limit)));
		$this->logQuery();
		if ($query === null)
			$query = $this->_query;
		if (empty($query) || strpos($query, ':') !== false)
			return $ret;
		$op = $this->_defaultOp;
		$this->xs->setScheme(XSFieldScheme::logger());
		try
		{
			$result = $this->setDb(self::LOB_DB)->setFuzzy()->setLimit($limit + 1)->search($query);
			foreach ($result as $doc) /* @var $doc XSDocument */
			{
				$doc->setCharset($this->_charset);
				$body = $doc->body;
				if (!strcasecmp($body, $query))
					continue;
				$ret[] = $body;
				if (count($ret) == $limit)
					break;
			}
		}
		catch (XSException $e)
		{
			if ($e->getCode() != CMD_ERR_XAPIAN)
				throw $e;
		}
		$this->setDb(null);
		$this->xs->restoreScheme();
		$this->_defaultOp = $op;
		return $ret;
	}
	public function getExpandedQuery($query, $limit = 10)
	{
		$ret = array();
		$limit = max(1, min(20, intval($limit)));
		try
		{
			$buf = XS::convert($query, 'UTF-8', $this->_charset);
			$cmd = array('cmd' => CMD_QUERY_GET_EXPANDED, 'arg1' => $limit, 'buf' => $buf);
			$res = $this->execCommand($cmd, CMD_OK_RESULT_BEGIN);
			while (true)
			{
				$res = $this->getRespond();
				if ($res->cmd == CMD_SEARCH_RESULT_FIELD)
				{
					$ret[] = XS::convert($res->buf, $this->_charset, 'UTF-8');
				}
				else if ($res->cmd == CMD_OK && $res->arg == CMD_OK_RESULT_END)
				{
					break;
				}
				else
				{
					$msg = 'Unexpected respond in search {CMD:' . $res->cmd . ', ARG:' . $res->arg . '}';
					throw new XSException($msg);
				}
			}
		}
		catch (XSException $e)
		{
			if ($e->getCode() != CMD_ERR_XAPIAN)
				throw $e;
		}
		return $ret;
	}
	public function getCorrectedQuery($query = null)
	{
		$ret = array();
		try
		{
			if ($query === null)
			{
				$query = $this->_query;
				if ($this->_count > 0 && $this->_count > ceil($this->getDbTotal() * 0.001))
					return $ret;
			}
			if (empty($query) || strpos($query, ':') !== false)
				return $ret;
			$buf = XS::convert($query, 'UTF-8', $this->_charset);
			$cmd = array('cmd' => CMD_QUERY_GET_CORRECTED, 'buf' => $buf);
			$res = $this->execCommand($cmd, CMD_OK_QUERY_CORRECTED);
			if ($res->buf !== '')
				$ret = explode("\n", XS::convert($res->buf, $this->_charset, 'UTF-8'));
		}
		catch (XSException $e)
		{
			if ($e->getCode() != CMD_ERR_XAPIAN)
				throw $e;
		}
		return $ret;
	}
	public function highlight($value)
	{
		if (empty($value))
			return $value;
		if (!is_array($this->_highlight))
			$this->initHighlight();
		if (isset($this->_highlight['pattern']))
			$value = preg_replace($this->_highlight['pattern'], $this->_highlight['replace'], $value);
		if (isset($this->_highlight['pairs']))
			$value = strtr($value, $this->_highlight['pairs']);
		return $value;
	}
	private function logQuery($query = null)
	{
		if (!$this->_lastCount)
			return;
		if ($query !== '' && $query !== null)
			$terms = $this->terms($query, false);
		else
		{
			$query = $this->_query;
			$terms = $this->terms(null, false);
		}
		$log = '';
		$pos = $max = 0;
		foreach ($terms as $term)
		{
			$pos1 = ($pos > 3 && strlen($term) === 6) ? $pos - 3 : $pos;
			if (($pos2 = strpos($query, $term, $pos1)) === false)
				break;
			if ($pos2 === $pos)
				$log .= $term;
			else if ($pos2 < $pos)
				$log .= substr($term, 3);
			else
			{
				if (++$max > 3 || strlen($log) > 42)
					break;
				$log .= ' ' . $term;
			}
			$pos = $pos2 + strlen($term);
		}
		$cmd = array('cmd' => CMD_SEARCH_ADD_LOG, 'buf' => $log);
		$this->execCommand($cmd, CMD_OK_LOGGED);
	}
	private function clearQuery()
	{
		$cmd = new XSCommand(CMD_QUERY_INIT);
		if ($this->_resetScheme === true)
		{
			$cmd->arg1 = 1;
			$this->_prefix = array();
			$this->_fieldSet = false;
			$this->_resetScheme = false;
		}
		$this->execCommand($cmd);
		$this->_query = $this->_count = $this->_terms = null;
	}
	private function addQueryString($query, $addOp = CMD_QUERY_OP_AND, $scale = 1)
	{
		$query = $this->preQueryString($query);
		$bscale = ($scale > 0 && $scale != 1) ? pack('n', intval($scale * 100)) : '';
		$cmd = new XSCommand(CMD_QUERY_PARSE, $addOp, $this->_defaultOp, $query, $bscale);
		$this->execCommand($cmd);
		return $query;
	}
	private function addQueryTerm($field, $term, $addOp = CMD_QUERY_OP_AND, $scale = 1)
	{
		$term = strtolower($term);
		$term = XS::convert($term, 'UTF-8', $this->_charset);
		$bscale = ($scale > 0 && $scale != 1) ? pack('n', intval($scale * 100)) : '';
		$vno = $field === null ? XSFieldScheme::MIXED_VNO : $this->xs->getField($field, true)->vno;
		$cmd = new XSCommand(CMD_QUERY_TERM, $addOp, $vno, $term, $bscale);
		$this->execCommand($cmd);
	}
	private function preQueryString($query)
	{
		$query = trim($query);
		if ($query === '')
			throw new XSException('Query string cann\'t be empty');
		if ($this->_resetScheme === true)
			$this->clearQuery();
		$newQuery = '';
		$parts = preg_split('/\s+/u', $query);
		foreach ($parts as $part)
		{
			if ($newQuery != '')
				$newQuery .= ' ';
			if (($pos = strpos($part, ':', 1)) !== false)
			{
				for ($i = 0; $i < $pos; $i++)
				{
					if (strpos('+-~(', $part[$i]) === false)
						break;
				}
				$name = substr($part, $i, $pos - $i);
				if (($field = $this->xs->getField($name)) !== false
					&& $field->vno != XSFieldScheme::MIXED_VNO)
				{
					$this->regQueryPrefix($name);
					if (!$field->isBoolIndex() && substr($part, $pos + 1, 1) != '('
						&& preg_match('/[\x81-\xfe]/', $part))
					{
						$newQuery .= substr($part, 0, $pos + 1) . '(' . substr($part, $pos + 1) . ')';
					}
					else
					{
						$newQuery .= $part;
					}
					continue;
				}
			}
			if (($part[0] == '+' || $part[0] == '-') && $part[1] != '('
				&& preg_match('/[\x81-\xfe]/', $part))
			{
				$newQuery .= substr($part, 0, 1) . '(' . substr($part, 1) . ')';
				continue;
			}
			$newQuery .= $part;
		}
		if ($this->_fieldSet !== true)
		{
			foreach ($this->xs->getAllFields() as $field) /* @var $field XSFieldMeta */
			{
				if ($field->cutlen != 0)
				{
					$len = min(127, ceil($field->cutlen / 10));
					$cmd = new XSCommand(CMD_SEARCH_SET_CUT, $len, $field->vno);
					$this->execCommand($cmd);
				}
				if ($field->isNumeric())
				{
					$cmd = new XSCommand(CMD_SEARCH_SET_NUMERIC, 0, $field->vno);
					$this->execCommand($cmd);
				}
			}
			$this->_fieldSet = true;
		}
		return XS::convert($newQuery, 'UTF-8', $this->_charset);
	}
	private function regQueryPrefix($name)
	{
		if (!isset($this->_prefix[$name])
			&& ($field = $this->xs->getField($name, false))
			&& ($field->vno != XSFieldScheme::MIXED_VNO))
		{
			$type = $field->isBoolIndex() ? CMD_PREFIX_BOOLEAN : CMD_PREFIX_NORMAL;
			$cmd = new XSCommand(CMD_QUERY_PREFIX, $type, $field->vno, $name);
			$this->execCommand($cmd);
			$this->_prefix[$name] = true;
		}
	}
	private function initHighlight()
	{
		$terms = array();
		$tmps = $this->terms($this->_highlight, false);
		for ($i = 0; $i < count($tmps); $i++)
		{
			if (strlen($tmps[$i]) !== 6 || ord(substr($tmps[$i], 0, 1)) < 0xc0)
			{
				$terms[] = XS::convert($tmps[$i], $this->_charset, 'UTF-8');
				continue;
			}
			for ($j = $i + 1; $j < count($tmps); $j++)
			{
				if (strlen($tmps[$j]) !== 6 || substr($tmps[$j], 0, 3) !== substr($tmps[$j - 1], 3, 3))
					break;
			}
			if (($k = ($j - $i)) === 1)
				$terms[] = XS::convert($tmps[$i], $this->_charset, 'UTF-8');
			else
			{
				$i = $j - 1;
				while ($k--)
				{
					$j--;
					if ($k & 1)
						$terms[] = XS::convert(substr($tmps[$j - 1], 0, 3) . $tmps[$j], $this->_charset, 'UTF-8');
					$terms[] = XS::convert($tmps[$j], $this->_charset, 'UTF-8');
				}
			}
		}
		$pattern = $replace = $pairs = array();
		foreach ($terms as $term)
		{
			if (!preg_match('/[a-zA-Z]/', $term))
				$pairs[$term] = '<em>' . $term . '</em>';
			else
			{
				$pattern[] = '/' . strtr($term, array('+' => '\\+', '/' => '\\/')) . '/i';
				$replace[] = '<em>$0</em>';
			}
		}
		$this->_highlight = array();
		if (count($pairs) > 0)
			$this->_highlight['pairs'] = $pairs;
		if (count($pattern) > 0)
		{
			$this->_highlight['pattern'] = $pattern;
			$this->_highlight['replace'] = $replace;
		}
	}
}
class XSCommand extends XSComponent
{
	public $cmd = CMD_NONE;
	public $arg1 = 0;
	public $arg2 = 0;
	public $buf = '';
	public $buf1 = '';
	public function __construct($cmd, $arg1 = 0, $arg2 = 0, $buf = '', $buf1 = '')
	{
		if (is_array($cmd))
		{
			foreach ($cmd as $key => $value)
			{
				if (property_exists($this, $key))
					$this->$key = $value;
			}
		}
		else
		{
			$this->cmd = $cmd;
			$this->arg1 = $arg1;
			$this->arg2 = $arg2;
			$this->buf = $buf;
			$this->buf1 = $buf1;
		}
	}
	public function __toString()
	{
		if (strlen($this->buf1) > 0xff)
			throw new XSException('The size of buf1 is too large for {CMD:' . $this->cmd . '}');
		return pack('CCCCI', $this->cmd, $this->arg1, $this->arg2, strlen($this->buf1), strlen($this->buf)) . $this->buf . $this->buf1;
	}
	public function getArg()
	{
		return $this->arg2 | ($this->arg1 << 8);
	}
	public function setArg($arg)
	{
		$this->arg1 = ($arg >> 8) & 0xff;
		$this->arg2 = $arg & 0xff;
	}
}
class XSServer extends XSComponent
{
	const FILE = 0x01;
	const BROKEN = 0x02;
	public $xs;
	private $_sock, $_conn;
	private $_flag;
	private $_project;
	private $_sendBuffer;
	public function __construct($conn = null, $xs = null)
	{
		$this->xs = $xs;
		if ($conn !== null)
			$this->open($conn);
	}
	public function __destruct()
	{
		$this->xs = null;
		$this->close();
	}
	public function open($conn)
	{
		$this->close();
		$this->_conn = $conn;
		$this->_flag = self::BROKEN;
		$this->_sendBuffer = '';
		$this->_project = null;
		$this->connect();
		$this->_flag ^= self::BROKEN;
		if ($this->xs instanceof XS)
			$this->setProject($this->xs->getName());
	}
	public function reopen()
	{
		if ($this->_flag & self::BROKEN)
			$this->open($this->_conn);
	}
	public function close($ioerr = false)
	{
		if ($this->_sock && !($this->_flag & self::BROKEN))
		{
			if (!$ioerr && !($this->_flag & self::FILE))
			{
				$cmd = new XSCommand(CMD_QUIT);
				fwrite($this->_sock, $cmd);
			}
			fclose($this->_sock);
			$this->_flag |= self::BROKEN;
		}
	}
	public function getProject()
	{
		return $this->_project;
	}
	public function setProject($name, $home = '')
	{
		if ($name !== $this->_project)
		{
			$cmd = array('cmd' => CMD_USE, 'buf' => $name, 'buf1' => $home);
			$this->execCommand($cmd, CMD_OK_PROJECT);
			$this->_project = $name;
		}
	}
	public function setTimeout($sec)
	{
		$cmd = array('cmd' => CMD_TIMEOUT, 'arg' => $sec);
		$this->execCommand($cmd, CMD_OK_TIMEOUT_SET);
	}
	public function execCommand($cmd, $res_arg = CMD_NONE, $res_cmd = CMD_OK)
	{
		if (!$cmd instanceof XSCommand)
			$cmd = new XSCommand($cmd);
		if ($cmd->cmd & 0x80)
		{
			$this->_sendBuffer .= $cmd;
			return true;
		}
		$buf = $this->_sendBuffer . $cmd;
		$this->_sendBuffer = '';
		$this->write($buf);
		if ($this->_flag & self::FILE)
			return true;
		$res = $this->getRespond();
		if ($res->cmd === CMD_ERR && $res_cmd != CMD_ERR)
			throw new XSException($res->buf, $res->arg);
		if ($res->cmd != $res_cmd || ($res_arg != CMD_NONE && $res->arg != $res_arg))
			throw new XSException('Unexpected respond {CMD:' . $res->cmd . ', ARG:' . $res->arg . '}');
		return $res;
	}
	public function sendCommand($cmd)
	{
		if (!$cmd instanceof XSCommand)
			$cmd = new XSCommand($cmd);
		$this->write(strval($cmd));
	}
	public function getRespond()
	{
		$buf = $this->read(8);
		$hdr = unpack('Ccmd/Carg1/Carg2/Cblen1/Iblen', $buf);
		$res = new XSCommand($hdr);
		$res->buf = $this->read($hdr['blen']);
		$res->buf1 = $this->read($hdr['blen1']);
		return $res;
	}
	public function hasRespond()
	{
		if ($this->_sock === null || $this->_flag & (self::BROKEN | self::FILE))
			return false;
		$wfds = $xfds = array();
		$rfds = array($this->_sock);
		$res = stream_select($rfds, $wfds, $xfds, 0, 0);
		return $res > 0;
	}
	private function write($buf, $len = 0)
	{
		$buf = strval($buf);
		if ($len == 0 && ($len = $size = strlen($buf)) == 0)
			return true;
		$this->check();
		while (true)
		{
			$bytes = fwrite($this->_sock, $buf, $len);
			if ($bytes === false || $bytes === 0 || $bytes === $len)
				break;
			$len -= $bytes;
			$buf = substr($buf, $bytes);
		}
		if ($bytes === false || $bytes === 0)
		{
			$meta = stream_get_meta_data($this->_sock);
			$this->close(true);
			$reason = $meta['timed_out'] ? 'timeout' : ($meta['eof'] ? 'closed' : 'unknown');
			$msg = 'Failed to send the data to server completely ';
			$msg .= '(SIZE:' . ($size - $len) . '/' . $size . ', REASON:' . $reason . ')';
			throw new XSException($msg);
		}
	}
	private function read($len)
	{
		if ($len == 0)
			return '';
		$this->check();
		for ($buf = '', $size = $len;;)
		{
			$bytes = fread($this->_sock, $len);
			if ($bytes === false || strlen($bytes) == 0)
				break;
			$len -= strlen($bytes);
			$buf .= $bytes;
			if ($len === 0)
				return $buf;
		}
		$meta = stream_get_meta_data($this->_sock);
		$this->close(true);
		$reason = $meta['timed_out'] ? 'timeout' : ($meta['eof'] ? 'closed' : 'unknown');
		$msg = 'Failed to recv the data from server completely ';
		$msg .= '(SIZE:' . ($size - $len) . '/' . $size . ', REASON:' . $reason . ')';
		throw new XSException($msg);
	}
	private function check()
	{
		if ($this->_sock === null)
			throw new XSException('No server connection');
		if ($this->_flag & self::BROKEN)
			throw new XSException('Broken server connection');
	}
	private function connect()
	{
		$conn = $this->_conn;
		if (is_int($conn) || is_numeric($conn))
		{
			$host = 'localhost';
			$port = intval($conn);
		}
		else if (!strncmp($conn, 'file://', 7))
		{
			$conn = substr($conn, 7);
			if (($sock = @fopen($conn, 'wb')) === false)
				throw new XSException('Failed to open local file for writing: `' . $conn . '\'');
			$this->_flag |= self::FILE;
			$this->_sock = $sock;
			return;
		}
		else if (($pos = strpos($conn, ':')) !== false)
		{
			$host = substr($conn, 0, $pos);
			$port = intval(substr($conn, $pos + 1));
		}
		else
		{
			$host = 'unix://' . $conn;
			$port = -1;
		}
		if (($sock = @fsockopen($host, $port, $errno, $error, 5)) === false)
			throw new XSException($error . '(C#' . $errno . ')');
		$timeout = ini_get('max_execution_time');
		$timeout = $timeout > 0 ? ($timeout - 1) : 30;
		stream_set_blocking($sock, true);
		stream_set_timeout($sock, $timeout);
		$this->_sock = $sock;
	}
}
interface XSTokenizer
{
	const DFL = 0;
	public function getTokens($value, XSDocument $doc);
}
class XSTokenizerNone implements XSTokenizer
{
	public function getTokens($value, XSDocument $doc)
	{
		return array();
	}
}
class XSTokenizerFull implements XSTokenizer
{
	public function getTokens($value, XSDocument $doc)
	{
		return array($value);
	}
}
class XSTokenizerSplit implements XSTokenizer
{
	private $arg = ' ';
	public function __construct($arg = null)
	{
		if ($arg !== null && $arg !== '')
			$this->arg = $arg;
	}
	public function getTokens($value, XSDocument $doc)
	{
		if (strlen($arg) > 1 && substr($arg, 0, 1) == '/' && substr($arg, -1, 1) == '/')
			return preg_split($this->arg, $value);
		return explode($this->arg, $value);
	}
}
class XSTokenizerXlen implements XSTokenizer
{
	private $arg = 2;
	public function __construct($arg = null)
	{
		if ($arg !== null && $arg !== '')
		{
			$this->arg = intval($arg);
			if ($this->arg < 1 || $this->arg > 255)
				throw new XSException('Invalid argument for ' . __CLASS__ . ': ' . $arg);
		}
	}
	public function getTokens($value, XSDocument $doc)
	{
		$terms = array();
		for ($i = 0; $i < strlen($value); $i += $this->arg)
		{
			$terms[] = substr($value, $i, $this->arg);
		}
		return $terms;
	}
}
class XSTokenizerXstep implements XSTokenizer
{
	private $arg = 2;
	public function __construct($arg = null)
	{
		if ($arg !== null && $arg !== '')
		{
			$this->arg = intval($arg);
			if ($this->arg < 1 || $this->arg > 255)
				throw new XSException('Invalid argument for ' . __CLASS__ . ': ' . $arg);
		}
	}
	public function getTokens($value, XSDocument $doc)
	{
		$terms = array();
		for ($i = $this->arg; $i <= strlen($value); $i += $this->arg)
		{
			$terms[] = substr($value, 0, $i);
		}
		return $terms;
	}
}
