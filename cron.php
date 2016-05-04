<?php
/** \file cron.php
 * Запуск специальных операций по расписанию.
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

chdir(dirname(__FILE__));

define("A_MODE",1);
define("A_MODE_FRONT",0);
define("A_MODE_ADMIN",1);

ini_set("register_globals",0);
ini_set("magic_quotes_gpc",0);
ini_set("mbstring.func_overload",2);
ini_set("mbstring.internal_encoding","utf-8");
ini_set("include_path","system/pear/".PATH_SEPARATOR.ini_get("include_path"));
ini_set("error_reporting",E_ALL);

require_once("system/framework/db.php");
require_once("system/framework/functions.php");
require_once("system/framework/observer.php");
require_once("system/framework/cache.php");

require_once("config.php");

function __autoload($class)
{
  switch($class)
  { case 'Smarty': require_once("system/smarty/Smarty.class.php"); break;
    case 'A_DataSet': require_once("system/framework/dataset.php"); break;
    case 'A_Pager': require_once("system/framework/pager.php"); break;
    case 'A_Mail': require_once("system/framework/mail.php"); break;
	case 'A_Form': require_once("system/framework/form.php"); break;
	case 'A_Frame': require_once("system/framework/frame.php"); break;
	case 'A_Grid': require_once("system/framework/grid.php"); break;
  }
}

class A
{
  static $DB;
  static $CACHE;
  static $AUTH;
  static $OBSERVER;
  static $OPTIONS;
  static $DOMAIN;
  static $DOMAINNAME;
  static $LANG;
  static $REGFILES;
  static $SECTION;
  static $SECTION_ID;
  static $STRUCTURE;

  function ini()
  {
    A::$DB=A_DB::getInstance();
	A::$OPTIONS=A::$DB->getAssoc("SELECT var,value FROM _options");
	A::$CACHE=A_Cache::getInstance();
	A::$OBSERVER=A_Observer::getInstance();
  }

  function getSystem()
  {
    return array();
  }
}

A::ini();

A::$DOMAIN='mysite';
A::$DOMAINNAME=getenv('HTTP_HOST');
A::$LANG='ru';

$A_GLOBALOPTIONS=A::$OPTIONS;
$A_DOMAINOPTIONS=A::$DB->getAssoc("SELECT var,value FROM mysite_options WHERE item=''");

if($A_MODULES=A::$DB->getCol("SELECT name FROM _extensions WHERE type='module' AND usecron='Y'"))
{
  $A_SECTIONS=A::$DB->getAll("SELECT * FROM {$A_DOMAIN}_sections WHERE (module='".implode("' OR module='",$A_MODULES)."') AND active='Y'");
  foreach($A_SECTIONS as $A_SECTION)
  {
    A::$SECTION='mysite_ru_'.$A_SECTION['name'];
    A::$SECTION_ID=$A_SECTION['id'];
    A::$REGFILES=A::$SECTION;

    $A_SECTIONOPTIONS=getOptions(A::$SECTION);
    A::$OPTIONS=array_merge($A_GLOBALOPTIONS,$A_DOMAINOPTIONS,$A_SECTIONOPTIONS);

    if(file_exists($ifile="modules/{$A_SECTION['module']}/cron.php"))
    include($ifile);
  }
}