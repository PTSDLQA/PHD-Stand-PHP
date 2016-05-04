<?php
/** \file admin.php
 * Инициализация окружения и начальная маршрутизация панели управления.
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

define('A_MODE',1);

require_once("system/framework/ini.php");

$REQUEST_URI=getenv('REQUEST_URI');

if(!A::$AUTH->isLogin())
$_REQUEST['mode']="auth";
elseif(empty($_REQUEST['mode']))
$_REQUEST['mode']="main";

$A_MENU=array();

$A_MENU['system'][]=array("item"=>"domains","name"=>"Сайты","ico"=>"/templates/admin/images/icons/domains.gif","close"=>true);
$A_MENU['system'][]=array("item"=>"extensions","name"=>"Расширения","ico"=>"/templates/admin/images/icons/extensions.gif");
$A_MENU['system'][]=array("item"=>"updates","name"=>"Обновлениe","ico"=>"/templates/admin/images/icons/updates.gif");
$A_MENU['system'][]=array("item"=>"bases","name"=>"Базы данных","ico"=>"/templates/admin/images/icons/bases.gif","close"=>true);
$A_MENU['system'][]=array("item"=>"admins","name"=>"Администраторы","ico"=>"/templates/admin/images/icons/admins.gif");
$A_MENU['system'][]=array("item"=>"options","name"=>"Настройки","ico"=>"/templates/admin/images/icons/options.gif");

$A_MENU['site'][]=array("item"=>"sections","name"=>"Разделы","ico"=>"/templates/admin/images/icons/sections.gif");
$A_MENU['site'][]=array("item"=>"structures","name"=>"Дополнения","ico"=>"/templates/admin/images/icons/structures.gif","close"=>true);
$A_MENU['site'][]=array("item"=>"blocks","name"=>"Блоки","ico"=>"/templates/admin/images/icons/blocks.gif");
$A_MENU['site'][]=array("item"=>"pages","name"=>"Типы шаблонов","ico"=>"/templates/admin/images/icons/pages.gif");
$A_MENU['site'][]=array("item"=>"languages","name"=>"Языковые версии","ico"=>"/templates/admin/images/icons/languages.gif","close"=>true);
$A_MENU['site'][]=array("item"=>"import","name"=>"Импорт/Экспорт","ico"=>"/templates/admin/images/icons/import.gif");
$A_MENU['site'][]=array("item"=>"options","name"=>"Настройки","ico"=>"/templates/admin/images/icons/options.gif");

$A_MENU['files'][]=array("item"=>"manager","name"=>"Файл-менеджер","ico"=>"/templates/admin/images/icons/manager.gif");
$A_MENU['files'][]=array("item"=>"templates","name"=>"Шаблоны","ico"=>"/templates/admin/images/icons/templates.gif");
$A_MENU['files'][]=array("item"=>"images","name"=>"Изображения","ico"=>"/templates/admin/images/icons/images.gif");
$A_MENU['files'][]=array("item"=>"files","name"=>"Файлы","ico"=>"/templates/admin/images/icons/files.gif");


A::$DB->query("SELECT * FROM mysite_sections WHERE menu='Y' ORDER BY sort");
while($row=A::$DB->fetchRow())
{ $item="mysite_ru_".$row['name'];
  $A_MENU['sections'][]=array("id"=>$row['id'],"item"=>$item,"name"=>$row['caption'],"ico"=>"/modules/".$row['module']."/ico.gif");
}
A::$DB->free();

$_REQUEST['mode']=preg_replace("/[^a-zA-Z0-9_]/i","",$_REQUEST['mode']);
if(!empty($_REQUEST['item']))
$_REQUEST['item']=preg_replace("/[^a-zA-Z0-9_]/i","",$_REQUEST['item']);

switch($_REQUEST['mode'])
{ default:
  case "auth":
    $A_MAINFILE="admin/admin_auth.php";
    break;

  case "main":
    $A_MAINFILE="admin/admin_main.php";
    break;

  case "system":
	if(empty($_REQUEST['item']))
    $_REQUEST['item']="extensions";
    $A_MAINFILE="admin/system_".$_REQUEST['item'].".php";
	$A_LEFTMENU =& $A_MENU['system'];
	$A_BIGICON="templates/admin/images/icons/big_system_".$_REQUEST['item'].".gif";
	$A_BIGICON=file_exists($A_BIGICON)?"/".$A_BIGICON:"/templates/admin/images/icons/big_system.gif";
	break;

  case "site":
	if(empty($_REQUEST['item']))
    $_REQUEST['item']="sections";
    $A_MAINFILE="admin/site_".$_REQUEST['item'].".php";
	$A_LEFTMENU =& $A_MENU['site'];
	$A_BIGICON="templates/admin/images/icons/big_site_".$_REQUEST['item'].".gif";
	$A_BIGICON=file_exists($A_BIGICON)?"/".$A_BIGICON:"/templates/admin/images/icons/big_site.gif";
	break;

  case "files":
    if(empty($_REQUEST['item']))
    $_REQUEST['item']="manager";
	$A_MAINFILE="admin/files_".$_REQUEST['item'].".php";
	$A_LEFTMENU =& $A_MENU['files'];
	$A_BIGICON="templates/admin/images/icons/big_files_".$_REQUEST['item'].".gif";
	$A_BIGICON=file_exists($A_BIGICON)?"/".$A_BIGICON:"/templates/admin/images/icons/big_files.gif";
	break;

  case "sections":
	if(empty($_REQUEST['item']))
    $A_MAINFILE="admin/statistic_sections.php";
    else
	{ $section=parseSection($_REQUEST['item']);
	  if($row=A::$DB->getRow("SELECT * FROM mysite_sections WHERE name='{$section['name']}'"))
      { $A_MAINFILE="modules/{$row['module']}/admin/{$row['module']}.php";
		define("MODULE",$row['module']);
		define("SECTION",A::$SECTION=$_REQUEST['item']);
		define("SECTION_ID",A::$SECTION_ID=$row['id']);
		define("SECTION_NAME",$row['caption']);
	    define("SNAME",$section['name']);
		A::$REGFILES=SECTION;
		$A_CPAGES=A::$DB->getCount("mysite_templates","idsec=".SECTION_ID);
		$A_BIGICON="modules/".MODULE."/big.gif";
		$A_STATFILE="modules/{$row['module']}/admin/statistic.php";
		if(file_exists($A_STATFILE))
		{ require_once($A_STATFILE);
		  $A_STATCLASS=$row['module'].'_Statistic';
		  $A_STATOBJ = new $A_STATCLASS(SECTION,$row['module']);
	    }
	  }
	}
	$A_LEFTMENU=&$A_MENU['sections'];
	$A_BIGICON=isset($A_BIGICON) && file_exists($A_BIGICON)?"/".$A_BIGICON:"/templates/admin/images/icons/big_sections.gif";
	break;
}

if(isset($_REQUEST['mode']))
define("MODE",$_REQUEST['mode']);

if(isset($_REQUEST['item']))
define("ITEM",$_REQUEST['item']);

foreach(A::$EXTENSIONS as $extrow)
if(file_exists($ifile=$extrow['type']."s/".$extrow['name']."/include.php"))
require_once($ifile);

$item=defined('SECTION')?SECTION:(defined('STRUCTURE')?STRUCTURE:'');
if(!empty($item))
{ A::$DB->query("SELECT var,value,options,type FROM mysite_options WHERE item='$item'");
  while($row=A::$DB->fetchRow())
  A::$OPTIONS[$row['var']]=$row['type']=='text'?$row['options']:$row['value'];
  A::$DB->free();
}

if(!empty($A_MAINFILE) && is_file($A_MAINFILE))
{ try
  { require_once($A_MAINFILE);

    A::$OBSERVER->Event('CreateAdminFrame',defined('ITEM')?ITEM:'');

    if(A::$MAINFRAME)
    {
	  if(!empty($_REQUEST['action']))
      { if(A::$AUTH)
	    switch($_REQUEST['action'])
	    { case 'login': A::$AUTH->Login(); break;
	      case 'logout': A::$AUTH->Logout(); break;
	      case 'remember': A::$MAINFRAME->Action($_REQUEST['action']); break;
	    }
	    if(!empty($_REQUEST['authcode']) && $_REQUEST['authcode']==A::$AUTH->authcode)
	    { A::$OBSERVER->Event('Action',$_REQUEST['action']);
	      A::$MAINFRAME->Action($_REQUEST['action']);
	    }
      }
    }
	else
	A::NotFound();
  }
  catch(exception $exception)
  { print $exception->__toString().'<br>';
  }
}

if(!empty($A_STATOBJ))
A::$MAINFRAME->Assign("itemstatistic",$A_STATOBJ->getContent());

$iconeditors=array();
if(defined('SECTION'))
{ A::$DB->query("SELECT * FROM mysite_blocks WHERE itemeditor='".SECTION."' ORDER BY align,sort");
  while($row=A::$DB->fetchRow())
  { $ico="/templates/admin/images/icons/blocks.gif";
	$link="admin.php?mode=site&item=blocks&id=".$row['id'];
	$iconeditors[]=array('ico'=>$ico,'link'=>$link,'caption'=>$row['caption']);
  }
  A::$DB->free();
}
if(!empty($A_SOWNER))
{ if($srow=A::$DB->getRowById(getSectionId($A_SOWNER),"mysite_sections"))
  { $ico="/modules/{$srow['module']}/ico.gif";
	$link="admin.php?mode=sections&item={$A_SOWNER}";
	$iconeditors[]=array('ico'=>$ico,'link'=>$link,'caption'=>$srow['caption']);
  }
}
A::$MAINFRAME->Assign("iconeditors",$iconeditors);
A::$MAINFRAME->Assign("domain",HOSTNAME);
A::$MAINFRAME->Assign("sitename",!empty(A::$OPTIONS['sitename_ru'])?A::$OPTIONS['sitename_ru']:'');
A::$MAINFRAME->Assign("sitetitle",!empty(A::$OPTIONS['sitename_ru'])?A::$OPTIONS['sitetitle_ru']:'');
A::$MAINFRAME->Assign("useckeditor",!is_dir("system/fckeditor"));
A::$MAINFRAME->Assign_by_ref("menu",$A_MENU);
A::$MAINFRAME->Assign_by_ref("leftmenu",$A_LEFTMENU);
if(!empty($A_BIGICON))
A::$MAINFRAME->Assign_by_ref("bigimage",$A_BIGICON);
if(!empty($A_CPAGES))
A::$MAINFRAME->Assign("topageslink","admin.php?mode=site&item=pages&idsec=".SECTION_ID);

A::$MAINFRAME->display();