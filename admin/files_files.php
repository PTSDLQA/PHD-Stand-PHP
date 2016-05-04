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

class files_Statistic extends A_Statistic
{
  function __construct($mode,$item)
  {
    $this->mode=$mode;
	parent::__construct("",$item);
  }

  function createData()
  {
    $this->Assign("files_count",A::$DB->getOne("SELECT COUNT(*) FROM mysite_files"));
	$size=A::$DB->getOne("SELECT SUM(size) FROM mysite_files");
	$this->Assign("size_sum",sizestring($size));
	$this->Assign("dwnl_sum",A::$DB->getOne("SELECT SUM(dwnl) FROM mysite_files"));
  }
}

$A_STATOBJ = new files_Statistic("files","files");

class FilesFiles extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("files_files.tpl");

	$this->AddJScript("/admin/jscripts/files_files.js");
  }

  function Action($action)
  {
    $res=false;
	switch($action)
	{ case "upload": $res=$this->Upload(); break;
	  case "register": $res=$this->Register(); break;
	  case "edit": $res=$this->Edit(); break;
	  case "del": $res=$this->Del(); break;
	  case "setrows": $res=$this->setRows(); break;
	  case "delete": $res=$this->Delete(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=files&item=files",array('idsec','page'));
  }

  function setRows()
  {
    if(isset($_REQUEST['rows']))
	A_Session::set("files_rows",(integer)$_REQUEST['rows']);
	return true;
  }

  function Upload()
  {
	unset($_POST['idsec']);
	A::$REGFILES=getSectionById($_REQUEST['idsec']);
	$idfile=UploadFile("uploadfile",$_REQUEST['caption']);
	return $idfile>0;
  }

  function Register()
  {
    if(!A::$AUTH->isSuperAdmin())
    return false;

	unset($_POST['idsec']);
	A::$REGFILES=getSectionById($_REQUEST['idsec']);
    $path=preg_replace("/\.\./i","",$_REQUEST['path']);
	if(is_file($path))
	RegisterFile($path,"");
	elseif(is_dir($path))
	{ $dh=opendir($path);
      while($filename = readdir($dh))
      { if($filename=="." || $filename=="..") continue;
	    RegisterFile($path."/$filename","");
	  }
	}
	return true;
  }

  function Edit()
  {
	unset($_POST['idsec']);

	if(!empty($_FILES['uploadfile']) && file_exists($_FILES['uploadfile']['tmp_name']))
	{ A::$REGFILES=getSectionById($_REQUEST['idsec']);
	  return UploadFile("uploadfile",$_REQUEST['caption'],$_REQUEST['id']);
	}
	else
	{ $dataset = new A_DataSet("mysite_files");
      $dataset->fields=array("idsec","name","caption","size","dwnl");
	  if(!empty($_REQUEST['basename']) && basename($_REQUEST['basename'])!=basename($dataset->data['path']))
	  { $path_parts=pathinfo($_REQUEST['basename']);
	    if(empty($path_parts['extension'])) return false;
	    $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
	    if(preg_match("/([^.]+)\.".$ext."$/i",mb_strtolower($path_parts['basename']),$matches))
	    $newname=translit($matches[1]).".{$ext}";
	    if(empty($newname) || file_exists("files/mysite/reg_files/$newname"))
	    { $this->errors['existsfile']=true;
  	      return false;
	    }
	    $_REQUEST['path']="files/mysite/reg_files/$newname";
	    rename($dataset->data['path'],$_REQUEST['path']);
	    $dataset->fields[]="path";
	  }
	  else
	  $_REQUEST['path']=$dataset->data['path'];
	  if(isset($_REQUEST['realsize']))
      $_REQUEST['size']=filesize($_REQUEST['path']);
	  return $dataset->Update();
	}
  }

  function Del()
  {
	DelRegFile((integer)$_REQUEST['id']);
	return true;
  }

  function Delete()
  {
    if(isset($_REQUEST['checkfile']))
	foreach($_REQUEST['checkfile'] as $id)
	DelRegFile($id);
	return true;
  }

  function createData()
  {
	$this->Assign("caption","Зарегистрированные файлы");

	$sections=A::$DB->getAssoc("
	SELECT DISTINCT f.idsec AS id,s.caption AS name
	FROM mysite_files AS f
	LEFT JOIN mysite_sections AS s ON s.id=f.idsec
	WHERE s.caption<>''
	ORDER BY s.sort");
	$this->Assign("sections",$sections);

	if(!empty($_GET['idsec']) && isset($sections[$_GET['idsec']]))
	{ $this->Assign("cursection",$sections[$_GET['idsec']]);
	  $this->Assign("cursectionlink","admin.php?mode=sections&item=".getSectionById($_GET['idsec']));
	}

	$files=array();
	$pager = new A_Pager(A_Session::get("files_rows","20"));
    $pager->query("
	SELECT f.*,s.caption AS section
	FROM mysite_files AS f
	LEFT JOIN mysite_sections AS s ON s.id=f.idsec
	".(!empty($_GET['idsec'])?"WHERE idsec=".(integer)$_GET['idsec']:"")."
	ORDER BY id DESC");
    while($row=$pager->fetchRow())
    { $row['basename']=basename($row['path']);
	  $row['size']=$row['size']>1024*1024?round($row['size']/(1024*1024),2)." Mb":round($row['size']/1024,2)." Kb";
	  $files[]=$row;
    }

	$this->Assign("files",$files);
	$this->Assign("files_pager",$pager);
	$this->AddJVar("cfiles",count($files));
	$this->Assign("rows",A_Session::get("files_rows",20));
  }
}

A::$MAINFRAME = new FilesFiles;