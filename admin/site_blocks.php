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

class SiteBlocks extends A_MainFrame
{
  function SiteBlocks()
  {
    parent::__construct();

	$this->AddJScript("/admin/jscripts/site_blocks.js");
	$this->AddJScript("/system/objcomp/jscripts/tpleditor.js");
  }

  function Action($action)
  {
    $res=false;
    switch($action)
	{ case "add": $res=$this->Add(); break;
	  case "edit": $res=$this->Edit(); break;
	  case "editblock": $res=$this->EditBlock(); break;
	  case "del": $res=$this->Del(); break;
	  case "setactive": $res=$this->SetActive(); break;
	  case "setunactive": $res=$this->SetUnActive(); break;
	  case "delete": $res=$this->Delete(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=site&item=blocks");
  }

  function Add()
  {
    $_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['icon']=isset($_REQUEST['checkicon'])?'Y':'N';
	$_REQUEST['caption']=strip_tags($_REQUEST['caption']);
	$_REQUEST['frame']=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST['frame']);
	$_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM mysite_blocks")+1;
	if(isset($_REQUEST['b_template']))
    $_REQUEST['b_template']=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST['b_template']);

	if($_REQUEST['align']=='free' || isset($_REQUEST["check_showall"]) || !isset($_REQUEST['showcheck']))
	$_REQUEST['show']="";
	else
	{ $_REQUEST['show']=array();
	  foreach($_REQUEST['showcheck'] as $showid)
	  if(preg_match("/^([0-9]+)_([a-zA-Z0-9_-]+)$/i",$showid,$mathes)>0)
      $_REQUEST['show'][$mathes[1]][]=$mathes[2];
	  else
	  $_REQUEST['show'][$showid]=array();
	  $_REQUEST['show']=serialize($_REQUEST['show']);
	}

	$_REQUEST['block']=preg_replace("/[^a-zA-Z0-9_-]+/i","",$_REQUEST['block']);
	require_once("blocks/{$_REQUEST['block']}/{$_REQUEST['block']}.php");

    if(method_exists($_REQUEST['block'].'_Block','prepareParams'))
	$_REQUEST['params']=call_user_func(array($_REQUEST['block'].'_Block','prepareParams'));
	else
	{ $_REQUEST['params']=array();
	  foreach($_REQUEST as $name=>$value)
	  if(preg_match("/^b_(.+)$/i",$name,$matches))
	  $_REQUEST['params'][$matches[1]]=$value;
	}
	$_REQUEST['params']=serialize($_REQUEST['params']);

    $dataset = new A_DataSet("mysite_blocks");
    $dataset->fields=array("block","itemeditor","name","caption","align","frame","params","show","active","icon","sort","lang");
    if(!empty($_REQUEST['b_idsec']))
	{ $_REQUEST['item']=getSectionById((integer)$_REQUEST['b_idsec']);
	  $dataset->fields[]="item";
	}

    if($dataset->Insert())
	{ if(!empty($_REQUEST['b_template']))
	  { $_REQUEST["b_template"]=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST["b_template"]);
	    copyfile("blocks/".$_REQUEST['block']."/templates/default/".$_REQUEST['block'].".tpl","templates/mysite/blocks/".$_REQUEST['b_template']);
	  }

	  return true;
	}
	else
	return false;
  }

  function Edit()
  {
    $_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['icon']=isset($_REQUEST['checkicon'])?'Y':'N';
	$_REQUEST['caption']=strip_tags($_REQUEST['caption']);
	$_REQUEST['frame']=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST['frame']);
	if(isset($_REQUEST['b_template']))
    $_REQUEST['b_template']=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST['b_template']);

	if($_REQUEST['align']=='free' || isset($_REQUEST["check_showall"]) || !isset($_REQUEST['showcheck']))
	$_REQUEST['show']="";
	else
	{ $_REQUEST['show']=array();
	  foreach($_REQUEST['showcheck'] as $showid)
	  if(preg_match("/^([0-9]+)_([a-zA-Z0-9_-]+)$/i",$showid,$mathes)>0)
      $_REQUEST['show'][$mathes[1]][]=$mathes[2];
	  else
	  $_REQUEST['show'][$showid]=array();
	  $_REQUEST['show']=serialize($_REQUEST['show']);
	}

	$_REQUEST['block']=preg_replace("/[^a-zA-Z0-9_-]+/i","",$_REQUEST['block']);
	require_once("blocks/{$_REQUEST['block']}/{$_REQUEST['block']}.php");

	if(method_exists($_REQUEST['block'].'_Block','prepareParams'))
	$_REQUEST['params']=call_user_func(array($_REQUEST['block'].'_Block','prepareParams'));
	else
	{ $_REQUEST['params']=array();
	  foreach($_REQUEST as $name=>$value)
	  if(preg_match("/^b_(.+)$/i",$name,$matches))
	  $_REQUEST['params'][$matches[1]]=$value;
	}
	$_REQUEST['params']=serialize($_REQUEST['params']);

    $dataset = new A_DataSet("mysite_blocks");
    $dataset->fields=array("block","itemeditor","name","caption","align","frame","params","show","active","icon","lang");
    if(!empty($_REQUEST['b_idsec']))
	{ $_REQUEST['item']=getSectionById((integer)$_REQUEST['b_idsec']);
	  $dataset->fields[]="item";
	}

    if($row=$dataset->Update())
	{ if(!empty($_REQUEST['b_template']))
	  { $_REQUEST["b_template"]=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST["b_template"]);
	    copyfile("blocks/".$_REQUEST['block']."/templates/default/".$_REQUEST['block'].".tpl","templates/mysite/blocks/".$_REQUEST['b_template']);
	  }

	  delFilesByDir('wizard_config/mysite',array('preview.html','preview.css'));

	  return true;
	}
	else
	return false;
  }

  function EditBlock()
  {
	$dataset = new A_DataSet("mysite_blocks");
    $dataset->fields=array("params");

	$_REQUEST['block']=preg_replace("/[^a-zA-Z0-9_-]+/i","",$_REQUEST['block']);
	require_once("blocks/{$_REQUEST['block']}/{$_REQUEST['block']}.php");

	if(method_exists($dataset->data['block'].'_Block','prepareParams'))
	$_REQUEST['params']=call_user_func(array($dataset->data['block'].'_Block','prepareParams'));
	else
	{ $_REQUEST['params']=array();
	  foreach($_REQUEST as $name=>$value)
	  if(preg_match("/^b_(.+)$/i",$name,$matches))
	  $_REQUEST['params'][$matches[1]]=$value;
	}
	$_REQUEST['params']=serialize($_REQUEST['params']);

    if(!empty($_REQUEST['b_idsec']))
	{ $_REQUEST['item']=getSectionById((integer)$_REQUEST['b_idsec']);
	  $dataset->fields[]="item";
	}

    if($row=$dataset->Update())
	{ if(isset($_REQUEST['b_template']))
	  { $_REQUEST["b_template"]=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST["b_template"]);
	    copyfile("blocks/".$row['block']."/templates/default/".$row['block'].".tpl","templates/mysite/blocks/".$_REQUEST['b_template']);
	  }

	  A::goUrl(A_Session::get("bprevurl","admin.php"));
	}
	else
	return false;
  }

  function Del()
  {
    $dataset = new A_DataSet("mysite_blocks");
    return $dataset->Delete();
  }

  function SetActive()
  {
    if(isset($_REQUEST['checkblock']))
	foreach($_REQUEST['checkblock'] as $id)
	A::$DB->execute("UPDATE mysite_blocks SET active='Y' WHERE id=".(integer)$id);
	return true;
  }

  function SetUnActive()
  {
    if(isset($_REQUEST['checkblock']))
	foreach($_REQUEST['checkblock'] as $id)
	A::$DB->execute("UPDATE mysite_blocks SET active='N' WHERE id=".(integer)$id);
	return true;
  }

  function Delete()
  {
    if(isset($_REQUEST['checkblock']))
	foreach($_REQUEST['checkblock'] as $id)
	A::$DB->execute("DELETE FROM mysite_blocks WHERE id=".(integer)$id);
	return true;
  }

  function getAllLinks()
  { $links=array();
	if($sections=A::$DB->getAll("SELECT * FROM ".DOMAIN."_sections WHERE module='pages' ORDER BY sort"))
	foreach($sections as $srow)
	{ $section=DOMAIN.'_'.$srow['lang'].'_'.$srow['name'];
	  A::$DB->query("SELECT * FROM {$section} WHERE idker=0 ORDER BY sort");
	  while($row=A::$DB->fetchRow())
	  { $link='/';
	    if($srow['lang']!=DEFAULTLANG)
	    $link.=$srow['lang']!='all'?$srow['lang'].'/':(A::$LANG!=DEFAULTLANG?A::$LANG.'/':'');
		$link.=$srow['name']!=A::$OPTIONS['mainsection']?$srow['urlname'].'/':'';
	    if($row['type']=='page')
	    { if($row['urlname']=='index')
	      $links[$link]='* '.$row['name'];
		  else
	      $links[$link.$row['urlname'].'.html']='* '.$row['name'];
	    }
	    else
	    $links[$link.$row['urlname'].'/']='* '.$row['name'];
	  }
	  A::$DB->free();
	}
	A::$DB->query("SELECT * FROM ".DOMAIN."_sections WHERE module<>'users' AND module<>'voting' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	if($row['name']!=A::$OPTIONS['mainsection'])
	{ $link='/';
	  if($row['lang']!=DEFAULTLANG)
	  $link.=$row['lang']!='all'?$row['lang'].'/':(A::$LANG!=DEFAULTLANG?A::$LANG.'/':'');
	  $links[$link.$row['urlname'].'/']='# '.$row['caption'];
	}
	A::$DB->free();
	return $links;
  }

  function createData()
  {
	A::$DB->query("SELECT * FROM _extensions WHERE type='block'");
	while($row=A::$DB->fetchRow())
	if(file_exists("blocks/{$row['name']}/{$row['name']}.js"))
	$this->AddJScript("/blocks/{$row['name']}/{$row['name']}.js");
	A::$DB->free();

	$bdata['alllinks']=$this->getAllLinks();
	$js="\nvar alllinks = new Array();\n";
	$i=0;
	foreach($bdata['alllinks'] as $link=>$name)
	{ $section=preg_match("/^\/([a-z0-9-]+)\//i",$link,$matches)?$matches[1]:"";
	  $js.="alllinks[$i] = new Array('$link','$name','$section');\n";
	  $i++;
	}
	$this->AddJScript($js,'code');

    if(!empty($_GET['id']))
	$this->EditPage((integer)$_GET['id']);
	else
	$this->MainPage();
  }

  function MainPage()
  {
    $this->template="site_blocks.tpl";
    $this->Assign("caption","Блоки");


	$leftblocks=$rightblocks=$freeblocks=array();
	$aligns=array("left"=>"слева","right"=>"справа","free"=>"заданная");
	$i=0;

	A::$DB->query("SELECT * FROM mysite_blocks WHERE align='left' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { require_once("blocks/{$row['block']}/{$row['block']}.php");
	  $class=$row['block'].'_Block';
	  $params=!empty($row['params'])?unserialize($row['params']):array();
	  if(class_exists($class))
	  $block = new $class($params,$row['block'],$row['name']);
	  if(!empty($block->template))
	  $row['tpl']=AddImageButton("/templates/admin/images/template.gif","edittpl('blocks/{$block->template}')","Редактировать шаблон",16,16);
	  else
	  $row['tpl']="&nbsp;";
	  $row['block']=A::$DB->getOne("SELECT caption FROM _extensions WHERE type='block' AND name=?",$row['block']);
	  $row['align']=$aligns[$row['align']];
      $row['show']=empty($row['show'])?"всегда":"условно";
	  $row['del']=AddImageButton("/templates/admin/images/del.gif","delblock({$row['id']})","Удалить");
	  $row['index']=$i++;
      $leftblocks[]=$row;
    }
	A::$DB->free();

	A::$DB->query("SELECT * FROM mysite_blocks WHERE align='right' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { require_once("blocks/{$row['block']}/{$row['block']}.php");
	  $class=$row['block'].'_Block';
	  $params=!empty($row['params'])?unserialize($row['params']):array();
	  if(class_exists($class))
	  $block = new $class($params,$row['block'],$row['name']);
	  if(!empty($block->template))
	  $row['tpl']=AddImageButton("/templates/admin/images/template.gif","edittpl('blocks/{$block->template}')","Редактировать шаблон",16,16);
	  else
	  $row['tpl']="&nbsp;";
	  $row['block']=A::$DB->getOne("SELECT caption FROM _extensions WHERE type='block' AND name=?",$row['block']);
	  $row['align']=$aligns[$row['align']];
      $row['show']=empty($row['show'])?"всегда":"условно";
	  $row['del']=AddImageButton("/templates/admin/images/del.gif","delblock({$row['id']})","Удалить");
	  $row['index']=$i++;
      $rightblocks[]=$row;
    }
	A::$DB->free();

	A::$DB->query("SELECT * FROM mysite_blocks WHERE align='free' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { require_once("blocks/{$row['block']}/{$row['block']}.php");
	  $class=$row['block'].'_Block';
	  $params=!empty($row['params'])?unserialize($row['params']):array();
	  if(class_exists($class))
	  $block = new $class($params,$row['block'],$row['name']);
	  if(!empty($block->template))
	  $row['tpl']=AddImageButton("/templates/admin/images/template.gif","edittpl('blocks/{$block->template}')","Редактировать шаблон",16,16);
	  else
	  $row['tpl']="&nbsp;";
	  $row['block']=A::$DB->getOne("SELECT caption FROM _extensions WHERE type='block' AND name=?",$row['block']);
	  $row['align']=$aligns[$row['align']];
      $row['show']=empty($row['show'])?"всегда":"условно";
	  $row['del']=AddImageButton("/templates/admin/images/del.gif","delblock({$row['id']})","Удалить");
	  $row['index']=$i++;
      $freeblocks[]=$row;
    }
	A::$DB->free();

	$blocks=array_merge($leftblocks,$rightblocks,$freeblocks);
	$this->Assign("blocks",$blocks);
	$this->Assign("leftblocks",$leftblocks);
	$this->Assign("rightblocks",$rightblocks);
	$this->Assign("freeblocks",$freeblocks);
  }

  function EditPage($id)
  {
    $this->template="site_blocks_edit.tpl";

    $prevurl=A_Session::get("bprevurl","admin.php");
	if($referer=getenv('HTTP_REFERER'))
	{ if(strpos($referer,"item=blocks")==false)
	  { if(strpos($referer,"wizard.php")!==false)
	    A_Session::set("bprevurl",str_replace("mode=","open=",$referer)."&tab=params");
	    else
	    A_Session::set("bprevurl",$referer);

		$prevurl=A_Session::get("bprevurl","admin.php");
	  }
	}
	$this->Assign("bprevurl",$prevurl);

	if($row=A::$DB->getRowById($id,"mysite_blocks"))
	{ $this->Assign("caption",$row['caption']);
	  $this->Assign("block",$row);

	  $iconeditors=array();
	  if(!empty($row['itemeditor']))
      { if($srow=A::$DB->getRowById(getSectionId($row['itemeditor']),"mysite_sections"))
        { $ico="/modules/{$srow['module']}/ico.gif";
	      $link="admin.php?mode=sections&item={$row['itemeditor']}";
	      $iconeditors[]=array('ico'=>$ico,'link'=>$link,'caption'=>$srow['caption']);
        }
      }
      $this->Assign("iconeditors",$iconeditors);
	}
  }
}

A::$MAINFRAME = new SiteBlocks;