<?php
/** \file system/framework/ini.php
 * Инициализация.
 */
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AFramework
 */
/**************************************************************************/

setlocale(LC_COLLATE, 'ru_RU.UTF8');
setlocale(LC_CTYPE, 'ru_RU.UTF8');
setlocale(LC_TIME, 'ru_RU.UTF8');

/**
 * Текущее время.
 */

function timeMeasure()
{ list($msec, $sec) = explode(chr(32), microtime());
  return ($sec+$msec);
}

/**
 * Время начала работы скрипта.
 */

define('TIMESTART', timeMeasure());

ini_set("register_globals",0);
ini_set("magic_quotes_gpc",0);
ini_set("mbstring.internal_encoding","utf-8");
ini_set("mbstring.http_input","auto");
ini_set("mbstring.func_overload",2);
ini_set("include_path","system/pear/".PATH_SEPARATOR.ini_get("include_path"));

/**
 * Значение для режима на сайте.
 */

define("A_MODE_FRONT",0);

/**
 * Значение для режима в панели управления.
 */

define("A_MODE_ADMIN",1);

header('Content-type: text/html; charset=utf-8');
header("Last-Modified: ".gmdate("D, d M Y H:i:s",time())." GMT");

require_once("system/framework/db.php");
require_once("system/framework/auth.php");
require_once("system/framework/session.php");
require_once("system/framework/functions.php");
require_once("system/framework/observer.php");
require_once("system/framework/cache.php");
require_once("system/framework/navigation.php");

require_once("config.php");

A_Session::start();

function __autoload($class)
{ switch($class)
  { case 'Smarty': require_once("system/smarty/Smarty.class.php"); break;
    case 'A_MainFrame': require_once("system/framework/main.php"); break;
    case 'A_DataSet': require_once("system/framework/dataset.php"); break;
    case 'A_Pager': require_once("system/framework/pager.php"); break;
    case 'A_Mail': require_once("system/framework/mail.php"); break;
	case 'A_Form': require_once("system/framework/form.php"); break;
	case 'A_Frame': require_once("system/framework/frame.php"); break;
	case 'A_Block': require_once("system/framework/block.php"); break;
	case 'A_Grid': require_once("system/framework/grid.php"); break;
	case 'A_Statistic': require_once("system/framework/statistic.php"); break;
	case 'A_SearchEngine': require_once("system/framework/search.php"); break;
	case 'A_ExcelReader': require_once("system/framework/formats.php"); break;
	case 'A_CSVReader': require_once("system/framework/formats.php"); break;
    case 'A_CategoriesTree': require_once("system/objcomp/categoriestree.php"); break;
	case 'A_CommentsEditor': require_once("system/objcomp/commeditor.php"); break;
	case 'A_FieldsEditor': require_once("system/objcomp/fieldseditor.php"); break;
	case 'A_OptionsBox': require_once("system/objcomp/optionsbox.php"); break;
	case 'A_FileAdmin': require_once("system/objcomp/fileadmin.php"); break;
	case 'A_Files': require_once("system/objcomp/files.php"); break;
	case 'A_Images': require_once("system/objcomp/images.php"); break;
  }
}

/**
 * Инициализация и реестр окружения.
 */

class A
{

/**
 * Объект для работы с БД.
 */

  static $DB;

/**
 * Объект авторизации.
 */

  static $AUTH;

/**
 * Объект системы кэширования.
 */

  static $CACHE;

/**
 * Объект системы событий.
 */

  static $OBSERVER;

/**
 * Массив опций текущего раздела/дополнения.
 */

  static $OPTIONS;

/**
 * Массив используемых расширений на сайте (модули и плагины).
 */

  static $EXTENSIONS;

/**
 * Рабочий протокол (http или https).
 */

  static $PROTOCOL;

/**
 * Идентификатор сайта.
 */

  static $DOMAIN;

/**
 * Домен сайта.
 */

  static $DOMAINNAME;

/**
 * Языковые версии.
 */

  static $LANGUAGES;

/**
 * Языковая версия по умолчанию.
 */

  static $DEFAULTLANG;

/**
 * Текущая языковая версия.
 */

  static $LANG;

/**
 * Полный строковой идентификатор раздела для регистрируемых изображений/файлов.
 */

  static $REGFILES;

/**
 * Полный строковой идентификатор текущего раздела.
 */

  static $SECTION;

/**
 * Числовой идентификатор текущего раздела.
 */

  static $SECTION_ID;

/**
 * Полный строковой идентификатор текущего дополнения.
 */

  static $STRUCTURE;

/**
 * Объект текущего модуля.
 */

  static $MAINFRAME;

/**
 * Массив элементов в пути URL.
 */

  static $URIPARAMS;

/**
 * Объект серверной части AJAX.
 */

  static $REQUEST;

/**
 * Массив значений основных переменных окружения системы.
 */

  static $SYSTEM;

/**
 * Массив с данными скачиваемого файла.
 */

  static $FILE;

/**
 * Инициализация.
 */

  function ini()
  {
	A::$DB=A_DB::getInstance();
	A::$OPTIONS=A::$DB->getAssoc("SELECT var,value FROM _options");
	A::$CACHE=A_Cache::getInstance();
	A::$AUTH=A_AUTH::getInstance();
	A::$OBSERVER=A_Observer::getInstance();

	ini_set("error_reporting",A::$OPTIONS['debugmode']?E_ALL:0);

	define('HOSTNAME',idn_decode(getenv('HTTP_HOST')));
	define("DOMAIN",A::$DOMAIN='mysite');
	define("DOMAINNAME",A::$DOMAINNAME=HOSTNAME);

	if(A_MODE==A_MODE_FRONT)
    {
      define("SMARTY_TEMPLATES","templates/mysite");
	  define("SMARTY_COMPILE","templates_c/mysite");
	}
    else
	{
	  require_once("system/framework/html.php");

      define("SMARTY_TEMPLATES","templates/admin");
	  define("SMARTY_COMPILE","templates_c/admin");
	}

	A::$DB->query("SELECT var,value,options,type FROM mysite_options WHERE item=''");
	while($row=A::$DB->fetchRow())
	A::$OPTIONS[$row['var']]=$row['type']=='text'?$row['options']:$row['value'];
	A::$DB->free();

    A::$LANGUAGES=array('ru'=>'Русский');
	define("DEFAULTLANG",A::$LANG="ru");
	define("LANG","ru");

	A::$EXTENSIONS=array();
	$modules=A::$DB->getCol("SELECT DISTINCT module FROM mysite_sections ORDER BY sort");
	foreach($modules as $module)
	A::$EXTENSIONS[]=array('type'=>'module','name'=>$module);
  }

/**
 * Возвращает массив значений основных переменных окружения системы.
 *
 * @return array Массив значений основных переменных окружения системы.
 */

  function getSystem()
  {
    if(!self::$SYSTEM)
	{ self::$SYSTEM=array();
	  if(defined('MODE'))
      self::$SYSTEM['mode']=MODE;
	  if(defined('ITEM'))
      self::$SYSTEM['item']=ITEM;
      self::$SYSTEM['domain']='mysite';
      if(defined('MODULE'))
      self::$SYSTEM['module']=MODULE;
	  if(defined('SECTION'))
      { self::$SYSTEM['section']=SECTION;
	    self::$SYSTEM['sectionlink']=getSectionLink(SECTION);
	  }
      self::$SYSTEM['lang']='ru';
	  if(defined('SNAME'))
      { self::$SYSTEM['sname']=SNAME;
	    self::$SYSTEM['mainpage']=SNAME==A::$OPTIONS['mainsection'] && count(A::$URIPARAMS)==0;
	  }
	  if(A::$AUTH)
	  self::$SYSTEM['authcode']=A::$AUTH->authcode;
	  if(A::$MAINFRAME)
	  self::$SYSTEM['page']=A::$MAINFRAME->page;
	  self::$SYSTEM['curlink']=getenv('REQUEST_URI');
      self::$SYSTEM['prevlink']=A_Session::get("A_PREVURL",getenv('REQUEST_URI'));
      self::$SYSTEM['tpldir']='/'.SMARTY_TEMPLATES;
      self::$SYSTEM['imgdir']='/'.SMARTY_TEMPLATES.'/images';
      self::$SYSTEM['ie6']=stripos($_SERVER['HTTP_USER_AGENT'],'msie 6')!==false;
      self::$SYSTEM=A::$OBSERVER->Modifier('system_prepareValues','',self::$SYSTEM);
	}
	return self::$SYSTEM;
  }

/**
 * Страница не найдена.
 */

  function NotFound()
  {
    @header("HTTP/1.1 404 Not Found");
	if(A_MODE==A_MODE_FRONT && A::$OPTIONS['404gomain'])
	@header("Location: /");
	if(A::$MAINFRAME && is_file('templates/mysite/404.tpl'))
	{ A::$MAINFRAME->template='404.tpl';
	  A::$MAINFRAME->_display();
	}
	exit();
  }

/**
 * Перенаправление.
 *
 * @param string $url URL.
 * @param array $params Список GET параметров, которые необходимо добавить к URL с текущими значениями $_GET и $_POST.
 * @param boolean $moved=false С добавлением заголовка 301 Moved Permanently.
 */

  function goUrl($url,$params=null,$moved=false)
  {
    if(!empty($params))
	{ foreach($params as $i=>$value)
	  if(!empty($_POST[$value]))
	  { if(is_array($_POST[$value]))
	    { $params[$i]=array();
		  foreach($_POST[$value] as $val)
		  $params[$i][]="{$value}[]=$val";
		  $params[$i]=implode("&",$params[$i]);
        }
        else
		$params[$i]="$value={$_POST[$value]}";
	  }
	  elseif(!isset($_POST[$value]) && !empty($_GET[$value]))
	  { if(is_array($_GET[$value]))
	    { $params[$i]=array();
		  foreach($_GET[$value] as $val)
		  $params[$i][]="{$value}[]=$val";
		  $params[$i]=implode("&",$params[$i]);
        }
        else
	    $params[$i]="$value={$_GET[$value]}";
	  }
	  else
	  unset($params[$i]);
	  if(!empty($params))
	  { $purl=parse_url($url);
	    if(!empty($purl['query']))
	    $url.='&'.implode('&',$params);
	    else
	    $url.='?'.implode('&',$params);
	  }
	}
	if($moved)
	@header("HTTP/1.1 301 Moved Permanently");
	elseif(preg_match("/[а-яА-Я]+/iu",$url) && strpos(getenv('HTTP_USER_AGENT'),"MSIE")!==false && mb_strpos($url,'http://')!==0 && mb_strpos(urldecode(getenv('REQUEST_URI')),"//")!==0)
	$url="http://".HOSTNAME."/".$url;
    @header("Location: $url");
	exit();
  }

/**
 * Перенаправление на предыдущую страницу.
 */

  function goPrevUrl()
  {
	$prev=A_Session::get("A_PREVURL","/");
	if(A_MODE==A_MODE_FRONT && strpos($prev,'admin.php')!==false)
	return;
	elseif(A_MODE==A_MODE_ADMIN && strpos($prev,'admin.php')===false)
	return;
	@header("Location: {$prev}");
	exit();
  }
}

A::ini();