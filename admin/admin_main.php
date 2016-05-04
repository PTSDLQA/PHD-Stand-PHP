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

class AdminMain extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("admin_main.tpl");
  }

  function createData()
  {
    $extensions=A::$DB->getOne("SELECT COUNT(*) FROM _extensions");
    if(empty($extensions))
    { require_once("admin/system_extensions.php");
	  SystemExtensions::Update();
    }

	$main=array('sections'=>array(),'structures'=>array());

    A::$DB->query("SELECT * FROM mysite_sections WHERE icon='Y' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $item=$section="mysite_ru_".$row['name'];
	  $main['sections'][]=array("id"=>$row['id'],"item"=>$item,"name"=>$row['caption'],"ico"=>"/modules/".$row['module']."/ico.gif");
	  $file="modules/{$row['module']}/admin/statistic.php";
	  if(file_exists($file))
	  { require_once($file);
	    $class="{$row['module']}_Statistic";
		$obj = new $class($section,$row['module']);
		foreach($main['sections'] as $i=>$mitem)
	    if($mitem['item']==$item)
	    $main['sections'][$i]['stat']=str_replace("\"","'",$obj->getContent());
	  }
	}
	A::$DB->free();
	$this->Assign("main",$main);

	$blocks=array();
	A::$DB->query("SELECT * FROM mysite_blocks WHERE icon='Y' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $ico="/templates/admin/images/icons/blocks.gif";
	  $link="admin.php?mode=site&item=blocks&id=".$row['id'];
	  $blocks[]=array('id'=>$row['id'],'ico'=>$ico,'link'=>$link,'caption'=>$row['caption']);
	}
	A::$DB->free();
	$this->Assign("blocks",$blocks);
  }
}

A::$MAINFRAME = new AdminMain;