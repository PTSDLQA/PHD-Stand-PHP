<?php
/** \file request.php
 * Инициализация окружения и начальная маршрутизация серверной стороны AJAX.
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

$_GET['mode']=preg_replace("/[^a-zA-Z0-9_]+/i","",$_GET['mode']);
$_GET['item']=preg_replace("/[^a-zA-Z0-9_]+/i","",$_GET['item']);

if(empty($_GET['mode']))
exit();

switch($_GET['mode'])
{ case "system":
  case "site":
  case "files":
  case "sections":
  case "block":
  case "object":
	define("A_MODE",1);
	break;
  case "front":
	define("A_MODE",0);
	break;
  default: exit();
}

require_once("system/framework/ini.php");
require_once("system/framework/request.php");

if(A_MODE==1 && !A::$AUTH->isLogin())
exit();

switch($_GET['mode'])
{ case "system":
	if(A::$AUTH->isAdmin())
	$A_MAINFILE="admin/{$_GET['mode']}_{$_GET['item']}_request.php";
	break;

  case "site":
	if(A::$AUTH->isAdmin())
	$A_MAINFILE="admin/{$_GET['mode']}_{$_GET['item']}_request.php";
	break;

  case "files":
	if(A::$AUTH->isAdmin())
	$A_MAINFILE="admin/{$_GET['mode']}_{$_GET['item']}_request.php";
	break;

  case "sections":
	if(A::$AUTH->isAdmin())
	{ $section=parseSection($_GET['item']);
	  if($row=A::$DB->getRow("SELECT * FROM mysite_sections WHERE name='{$section['name']}'"))
      { $A_MAINFILE="modules/{$row['module']}/admin/request.php";
	    define("MODULE",$row['module']);
		define("SECTION",A::$SECTION=$_GET['item']);
		define("SECTION_ID",A::$SECTION_ID=$row['id']);
		define("SECTION_NAME",$row['caption']);
	    define("SNAME",$section['name']);
		A::$REGFILES=SECTION;
	  }
    }
	break;

  case "block":
    if(A::$AUTH->isAdmin())
	$A_MAINFILE="blocks/{$_GET['item']}/request.php";
	break;

  case "object":
    if(!empty($_REQUEST['section']))
	{ $_REQUEST['mode']='sections';
	  if(A::$AUTH->isAdmin())
	  { $section=parseSection($_REQUEST['section']);
	  	if($row=A::$DB->getRow("SELECT * FROM mysite_sections WHERE name='{$section['name']}'"))
        { define("MODULE",$row['module']);
		  define("SECTION",A::$SECTION=$_REQUEST['section']);
		  define("SECTION_ID",A::$SECTION_ID=$row['id']);
		  define("SECTION_NAME",$row['caption']);
	      define("SNAME",$section['name']);
		  A::$REGFILES=SECTION;
	    }
	  }
	}

	$A_MAINFILE="system/objcomp/{$_GET['item']}_request.php";
	break;

  case "front":
	$section=parseSection($_GET['item']);
	if($row=A::$DB->getRow("SELECT * FROM mysite_sections WHERE name='{$section['name']}'"))
    { $A_MAINFILE="modules/{$row['module']}/request.php";
	  define("MODULE",$row['module']);
	  define("SECTION",A::$SECTION=$_GET['item']);
	  define("SECTION_ID",A::$SECTION_ID=$row['id']);
	  define("SECTION_NAME",$row['caption_ru']);
	  define("SNAME",$section['name']);
	  A::$REGFILES=SECTION;
	}

	break;

  default: return;
}

define("MODE",!empty($_REQUEST['mode'])?$_REQUEST['mode']:$_GET['mode']);
define("ITEM",!empty($_REQUEST['item'])?$_REQUEST['item']:$_GET['item']);

foreach(A::$EXTENSIONS as $extrow)
if(is_file($ifile="{$extrow['type']}s/{$extrow['name']}/include.php"))
require_once($ifile);

if(defined('SECTION'))
{ A::$DB->query("SELECT var,value FROM mysite_options WHERE item='".SECTION."'");
  while($row=A::$DB->fetchRow())
  A::$OPTIONS[$row['var']]=$row['value'];
  A::$DB->free();
}

if(isset($A_MAINFILE) && is_file($A_MAINFILE))
{ try
  { require_once($A_MAINFILE);

    if(A::$REQUEST && !empty($_GET['action']))
    switch(A_MODE)
    { case 0:
        A::$REQUEST->Action($_GET['action']);
	    break;
      case 1:
	    if(!empty($_GET['authcode']) && $_GET['authcode']==A::$AUTH->authcode)
        { A::$OBSERVER->Event('requestAction',$_GET['action']);
	      A::$REQUEST->Action($_GET['action']);
	    }
        break;
    }
  }
  catch(exception $exception)
  { if(A_MODE==1 || !empty(A::$OPTIONS['debugmode']))
    print $exception->__toString().'<br>';
  }
}