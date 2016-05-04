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

class images_Statistic extends A_Statistic
{
  function __construct($mode,$item)
  {
    $this->mode=$mode;
	parent::__construct("",$item);
  }

  function createData()
  {
    $this->Assign("images_count",A::$DB->getOne("SELECT COUNT(*) FROM mysite_images"));
  }
}

$A_STATOBJ = new images_Statistic("files","images");

class FilesImages extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("files_images.tpl");

	$this->AddJScript("/admin/jscripts/files_images.js");
  }

  function Action($action)
  {
    $res=false;
	switch($action)
	{ case "upload": $res=$this->Upload(); break;
	  case "edit": $res=$this->Edit(); break;
	  case "del": $res=$this->Del(); break;
	  case "setrows": $res=$this->setRows(); break;
	  case "delete": $res=$this->Delete(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=files&item=images",array('idsec','page'));
  }

  function setRows()
  {
    if(isset($_REQUEST['rows']))
	A_Session::set("images_rows",(integer)$_REQUEST['rows']);
	return true;
  }

  function Upload()
  {
    unset($_POST['idsec']);
	A::$REGFILES=getSectionById($_REQUEST['idsec']);
	$idimg=UploadImage("uploadfile",$_REQUEST['caption']);
	return $idimg>0;
  }

  function Edit()
  {
	unset($_POST['idsec']);

	require_once('Image/Transform.php');

	$dataset = new A_DataSet("mysite_images");
    $dataset->fields=array("idsec","caption");

	if(!empty($_REQUEST['basename']) && basename($_REQUEST['basename'])!=basename($dataset->data['path']))
	{ $path_parts=pathinfo($_REQUEST['basename']);
	  $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
	  if(preg_match("/([^.]+)\.".$ext."$/i",mb_strtolower($path_parts['basename']),$matches))
	  $newname=translit($matches[1]).".{$ext}";
	  if(empty($newname) || file_exists("files/mysite/reg_images/$newname"))
	  { $this->errors['existsfile']=true;
	    return false;
	  }
	  $_REQUEST['path']="files/mysite/reg_images/$newname";
	  rename($dataset->data['path'],$_REQUEST['path']);
	  $dataset->fields[]="path";
	}
	else
	$_REQUEST['path']=$dataset->data['path'];

	$resize=$x=$y=0;

	if((integer)$_REQUEST['width']>0 && (integer)$_REQUEST['width']!=$dataset->data['width'])
	{ $resize=true;
	  $x=(integer)$_REQUEST['width'];
	}

	if((integer)$_REQUEST['height']>0 && (integer)$_REQUEST['height']!=$dataset->data['height'])
	{ $resize=true;
	  $y=(integer)$_REQUEST['height'];
	}

	if($resize)
	{ require_once('Image/Transform.php');

	  $it = & Image_Transform::factory('GD');
      $it->load($_REQUEST['path']);

	  if($x>0 && $y>0)
	  $it->scaleByXY($x,$y);
	  elseif($x>0)
      $it->scaleByX($x);
      elseif($y>0)
      $it->scaleByY($y);

	  $_REQUEST['width']=$it->new_x;
	  $_REQUEST['height']=$it->new_y;
	  $dataset->fields[]="width";
	  $dataset->fields[]="height";

	  delfile($_REQUEST['path']);
	  $it->save($_REQUEST['path']);
	}

	return $dataset->Update();
  }

  function Del()
  {
	DelRegImage((integer)$_REQUEST['id']);
	return true;
  }

  function Delete()
  {
    if(isset($_REQUEST['checkimg']))
	foreach($_REQUEST['checkimg'] as $id)
	DelRegImage($id);
	return true;
  }

  function createData()
  {
	$this->Assign("caption","Зарегистрированные изображения");

	$sections=array();
	A::$DB->query("
	SELECT DISTINCT i.idsec AS id,s.caption AS name
	FROM mysite_images AS i
	LEFT JOIN mysite_sections AS s ON s.id=i.idsec
	WHERE s.caption<>''
	ORDER BY s.sort");
	while($row=A::$DB->fetchRow())
	$sections[$row['id']]=$row['name'];
	$this->Assign("sections",$sections);
	A::$DB->free();

	if(!empty($_GET['idsec']) && isset($sections[$_GET['idsec']]))
	{ $this->Assign("cursection",$sections[$_GET['idsec']]);
	  $this->Assign("cursectionlink","admin.php?mode=sections&item=".getSectionById($_GET['idsec']));
	}

	$images=array();
	$pager = new A_Pager(A_Session::get("images_rows","20"));
    $pager->query("
	SELECT i.*,s.caption AS section
	FROM mysite_images AS i
	LEFT OUTER JOIN mysite_sections AS s ON s.id=i.idsec
	".(!empty($_GET['idsec'])?"WHERE idsec=".(integer)$_GET['idsec']:"")."
	ORDER BY id DESC");
    while($row=$pager->fetchRow())
    { $row['basename']=basename($row['path']);
	  $images[]=$row;
	}

	$this->Assign("images",$images);
	$this->Assign("images_pager",$pager);
	$this->AddJVar("cimages",count($images));
	$this->Assign("rows",A_Session::get("images_rows",20));
  }
}

A::$MAINFRAME = new FilesImages;