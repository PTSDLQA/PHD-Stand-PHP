<?php
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package APanel
 */
/**************************************************************************/

class SitePages extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("site_pages.tpl");

    $this->AddJScript("/admin/jscripts/site_pages.js");
    $this->AddJScript("/system/objcomp/jscripts/tpleditor.js");
  }

  function Action($action)
  {
    $res=false;
    switch($action)
	{ case "editpage": $res=$this->EditPage(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=site&item=pages",array('idsec'));
  }

  function EditPage()
  {
	$_REQUEST["template"]=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST["template"]);

	$dataset = new A_DataSet("mysite_templates");
	$dataset->fields=array("template");
	if($row=$dataset->Update())
	{ $module=getModuleBySection(getSectionById($row['idsec']));
	  if(!empty($_REQUEST['template']))
	  { if(!copyfile("templates/mysite/".$row['template'],"templates/mysite/".$_REQUEST['template']));
	    copyfile("modules/{$module}/templates/default/".$row['template'],"templates/mysite/".$_REQUEST['template']);
	  }
	  return true;
	}
	else
	return false;
  }

  function createData()
  {
	$this->Assign("caption","Типы шаблонов");

	$pages=array();
	$sections=array();
	A::$DB->query("SELECT * FROM mysite_templates ORDER BY idsec,id");
    while($row=A::$DB->fetchRow())
	if($srow=A::$DB->getRowById($row['idsec'],"mysite_sections"))
	{ $row['section']=$srow['caption'];
	  $row['sort']=sprintf("%02d",$srow['sort']).'_'.sprintf("%02d",$row['id']);
	  if(empty($_GET['idsec']) || $_GET['idsec']==$row['idsec'])
	  $pages[]=$row;
	  if(!isset($sections[$row['idsec']]))
	  $sections[$row['idsec']]=$row['section'];
    }
    A::$DB->free();

	$pages=array_multisort_key($pages,'sort');

	$this->Assign("pages",$pages);
	$this->Assign("sections",$sections);

	if(!empty($_GET['idsec']) && isset($sections[$_GET['idsec']]))
	{ $this->Assign("cursection",$sections[$_GET['idsec']]);
	  $this->Assign("cursectionlink","admin.php?mode=sections&item=".getSectionById($_GET['idsec']));
	}

	$this->AddJVar("cpages",count($pages));
  }
}

A::$MAINFRAME = new SitePages;