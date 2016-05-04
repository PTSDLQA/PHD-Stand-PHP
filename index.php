<?php
/** \file index.php
 * Инициализация окружения и начальная маршрутизация сайта.
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

define('A_MODE',0);
require_once("system/framework/ini.php");

$REQUEST_URI=urldecode(getenv('REQUEST_URI'));
$QUERY_STRING=getenv('QUERY_STRING');
$PURL=parse_url($REQUEST_URI);

if(preg_match("/\/[a-zA-Zа-яА-Я0-9_-]+$/iu",$PURL['path']))
A::goUrl($PURL['path'].'/'.(!empty($QUERY_STRING)?"?{$QUERY_STRING}":""),null,true);

preg_match_all("/\/([a-zA-Zа-яА-Я0-9._-]+)/iu",$PURL['path'],$matches);
A::$URIPARAMS=$matches[1];

if(A::$URIPARAMS)
{
  if(A::$URIPARAMS[0]=='getfile' && !empty(A::$URIPARAMS[1]))
  { if(preg_match("/^[0-9]+$/i",A::$URIPARAMS[1]))
	{ if($file=A::$DB->getRowById(A::$URIPARAMS[1],"mysite_files"))
      { if(!empty(A::$URIPARAMS[2]) && $file['name']==A::$URIPARAMS[2])
	    A::goUrl("http://".HOSTNAME."/download.php?id=".A::$URIPARAMS[1].(!empty($QUERY_STRING)?"&{$QUERY_STRING}":""),null,true);
	  }
	}
	exit();
  }
  elseif(A::$URIPARAMS[0]=='sitemap.xml')
  { foreach(A::$EXTENSIONS as $extrow)
    if(is_file($ifile="{$extrow['type']}s/{$extrow['name']}/include.php"))
    require_once($ifile);
	if(function_exists("sitemap_outXML"))
	sitemap_outXML();
  }

  if($A_SECTION=A::$DB->getRow("SELECT * FROM mysite_sections WHERE urlname='".reset(A::$URIPARAMS)."' AND active='Y'"))
  { array_shift(A::$URIPARAMS);
    define("SNAME",$A_SECTION['name']);
  }
}

if(!defined("SNAME"))
{
  if(empty(A::$OPTIONS['mainsection']))
  A::goUrl("/admin.php");
  else
  define("SNAME",A::$OPTIONS['mainsection']);

  $A_SECTION=A::$DB->getRow("SELECT * FROM mysite_sections WHERE name='".SNAME."' AND active='Y'");
}

if($A_SECTION)
{
  define("MODULE",$A_SECTION['module']);
  define("SECTION",A::$SECTION="mysite_ru_".$A_SECTION['name']);
  define("SECTION_ID",A::$SECTION_ID=$A_SECTION['id']);
  define("SECTION_NAME",$A_SECTION['caption_ru']);
  define("ITEM",SECTION);

  A::$REGFILES=SECTION;
}
else
A::NotFound();

foreach(A::$EXTENSIONS as $extrow)
{ if($extrow['type']=='module' && is_file($ifile="modules/{$extrow['name']}/auth.php"))
  $authfile=$ifile;
  if(is_file($ifile="{$extrow['type']}s/{$extrow['name']}/include.php"))
  require_once($ifile);
}

if(!empty($authfile))
require_once($authfile);

A::$DB->query("SELECT var,value FROM mysite_options WHERE item='".SECTION."'");
while($row=A::$DB->fetchRow())
A::$OPTIONS[$row['var']]=$row['value'];
A::$DB->free();

if(is_file($A_MAINFILE="modules/{$A_SECTION['module']}/{$A_SECTION['module']}.php"))
{
  try
  { require_once($A_MAINFILE);

    A::$OBSERVER->Event('CreateMainFrame',SECTION);

	if(A::$MAINFRAME)
    {
	  A::$MAINFRAME->Router(A::$URIPARAMS);
	  A::$MAINFRAME->loadBlocks();

      if(!A::$MAINFRAME->fad && A::$MAINFRAME->page)
      A::$MAINFRAME->template=A::$DB->getOne("SELECT template FROM mysite_templates WHERE idsec=".SECTION_ID." AND name=?",A::$MAINFRAME->page);

      if(!empty($_REQUEST['action']))
      { if(A::$AUTH)
	    switch($_REQUEST['action'])
	    { case "login": A::$AUTH->Login(); break;
	      case "logout": A::$AUTH->Logout(); break;
	    }
	    A::$OBSERVER->Event('Action',$_REQUEST['action']);
	    A::$MAINFRAME->Action($_REQUEST['action']);
	  }
    }
    else
    A::NotFound();
  }
  catch(exception $exception)
  { if(!empty(A::$OPTIONS['debugmode']))
    print $exception->__toString().'<br>';
  }
}

A::$MAINFRAME->title=!empty($A_SECTION['title_ru'])?$A_SECTION['title_ru'].(!empty(A::$OPTIONS['sitetitle_ru'])?" - ".A::$OPTIONS['sitetitle_ru']:""):A::$OPTIONS['sitetitle_ru'];
A::$MAINFRAME->display();