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

class SystemExtensions extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("system_extensions.tpl");

	$this->AddJScript("/admin/jscripts/system_extensions.js");
  }

  function Action($action)
  {
    @set_time_limit(0);

	$res=false;
    switch($action)
	{ case "upload": $res=$this->Upload(); break;
	  case "update": $res=$this->Update(); break;
	  case "export": $res=$this->Export(); break;
	  case "exportall": $res=$this->ExportAll(); break;
	  case "uninstall": $res=$this->Del(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=system&item=extensions",array('tab'));
  }

  function writeable($dirIn,$dirTo)
  {
    if(!is_dir($dirIn)) return false;
    $result=true;
	$dir=dir($dirIn);
    while($file=$dir->read())
    { if($file!='.' && $file!='..')
	  { if(is_dir($dirIn.'/'.$file))
	    $result=$result && $this->writeable($dirIn.'/'.$file,$dirTo.'/'.$file);
		else
		$result=$result && (!is_file($dirTo.'/'.$file) || is_writeable($dirTo.'/'.$file));
      }
    }
    $dir->close();
    return $result;
  }

  function Import($file,&$updates)
  {
    clearDir("files/tmp/tmp");

    if(extractArchive($file,"files/tmp/tmp"))
	{
	  if(is_file("files/tmp/tmp/sql-xml/install.xml"))
	  $xml = loadXML("files/tmp/tmp/sql-xml/install.xml",true);
	  elseif(is_file("files/tmp/tmp/install.xml"))
	  $xml = loadXML("files/tmp/tmp/install.xml",true);
	  else
	  return false;

	  if(!$xml) return false;

	  $curversion=A::$DB->getOne("SELECT version FROM _extensions WHERE type=? AND name=?",array($xml['type'],$xml['id']));

	  if($curversion)
	  { if($xml['version']<$curversion)
		continue;

		$from=(float)preg_replace("/[0-9]{2}$/","",$curversion);
		$to=(float)preg_replace("/[0-9]{2}$/","",$xml['version']);

		if(($to-$from)>0)
		$updates[]=array('type'=>$xml['type'],'name'=>$xml['id'],'from'=>$from,'to'=>$to);
	  }

	  if(empty($xml['system']) || $xml['system']>A::$OPTIONS['version'])
	  { if(empty($this->errors))
	    $this->errors['invalidsystem']=$xml['system'];
	    return false;
	  }

	  $f=false;

	  switch($xml['type'])
	  { case 'module':
		  if($f=self::writeable("files/tmp/tmp","modules/".$xml['id']))
		  { delDir("modules/".$xml['id']);
		    copyDir("files/tmp/tmp","modules/".$xml['id']);
		  }
		  break;
		case 'block':
		  if($f=self::writeable("files/tmp/tmp","blocks/".$xml['id']))
		  { delDir("blocks/".$xml['id']);
		    copyDir("files/tmp/tmp","blocks/".$xml['id']);
		  }
		  break;
	  }

	  return $f;
	}
	else
	return false;
  }

  function Upload($extpath='')
  {
	$updates=array();

	mk_dir("files/tmp");
	mk_dir("files/tmp/tmp");

	if(empty($extpath))
	{
      for($i=0;$i<=5;$i++)
	  if(file_exists($_FILES['extensionfile'.$i]['tmp_name']))
      { $path_parts=pathinfo($_FILES['extensionfile'.$i]['name']);
	    $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
	    if($ext=='gz')
	    $f=self::Import($_FILES['extensionfile'.$i]['tmp_name'],$updates);
	    elseif($ext=='zip' && extension_loaded('zip'))
	    { clearDir("files/tmp");
		  $zip = new ZipArchive();
          $zip->open($_FILES['extensionfile'.$i]['tmp_name']);
          if($zip->extractTo("files/tmp"))
          { $files=scandir("files/tmp");
			foreach($files as $file)
			if(is_file("files/tmp/$file"))
			$f=self::Import("files/tmp/$file",$updates);
          }
	    }
	  }
	}
	else
	{ $path_parts=pathinfo($extpath);
	  $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));

	  if($ext=='gz')
	  { if($f=self::Import($extpath,$updates))
		delfile($extpath);
	  }
	}

	if(empty($f))
	{ if(empty($this->errors))
	  $this->errors['invalidfile']=true;
	  return false;
	}
	else
	{
	  self::Update();

	  if(!empty($updates))
	  {
	    foreach($updates as $update)
		switch($update['type'])
		{ case "module":
		    $sections=getSectionsByModule($update['name']);
			foreach($sections as $section)
			{ for($v=(float)$update['from'];$v<(float)$update['to'];$v+=0.01)
			  { $sqlfile=str_replace(",",".",sprintf("modules/{$update['name']}/sql-xml/update_%.2f-%.2f.sql",$v,$v+0.01));
			    if($sql=@file_get_contents($sqlfile))
		        { $sql=str_replace("{section}",$section,$sql);
		          $sql=str_replace("{section_id}",getSectionId($section),$sql);
			      $sql=str_replace("{domain}","mysite",$sql);
			      A::$DB->execSQL($sql);
			    }
			  }
			}
			break;
		}
	  }

	  return true;
	}
  }

  function Update()
  {
	$modules=A::$DB->getCol("SELECT name FROM _extensions WHERE type='module'");

	$dh=opendir("modules");
    while($filename = readdir($dh))
    { if($filename=="." || $filename=="..") continue;
      if(is_dir("modules/$filename"))
      { copyDir("modules/$filename/templates/admin","templates/admin",true);
        if($data=loadXML("modules/$filename/sql-xml/install.xml",true))
		{ $data['caption']=$data['name'];
		  $data['name']=$data['id'];
		  $data['type']='module';
		  unset($data['id']);
		  if(isset($data['noupdate']))
		  unset($data['noupdate']);
		  if(isset($data['hidden']))
		  unset($data['hidden']);
		  if(!in_array($filename,$modules))
		  { A::$DB->Insert("_extensions",$data);
		    $modules[]=$data['name'];
		  }
		  else
		  A::$DB->Update("_extensions",$data,"type='module' AND name='{$data['name']}'");
		}
	  }
    }
    closedir($dh);

	foreach($modules as $module)
	if(!is_dir("modules/$module"))
	{ A::$DB->execute("DELETE FROM _extensions WHERE type='module' AND name=?",$module);
	  A::$DB->execute("DELETE FROM mysite_sections WHERE module=?",$module);
	}

    $blocks=A::$DB->getCol("SELECT name FROM _extensions WHERE type='block'");

    $dh=opendir("blocks");
    while($filename = readdir($dh))
    { if($filename=="." || $filename=="..") continue;
      if(is_dir("blocks/$filename"))
	  { copyDir("blocks/$filename/templates/admin","templates/admin",true);
        if($data=loadXML("blocks/$filename/install.xml",true))
	    { $data['caption']=$data['name'];
		  $data['name']=$data['id'];
		  $data['type']='block';
		  unset($data['id']);
		  if(isset($data['noupdate']))
		  unset($data['noupdate']);
		  if(isset($data['hidden']))
		  unset($data['hidden']);
		  if(!in_array($filename,$blocks))
		  { A::$DB->Insert("_extensions",$data);
		    $blocks[]=$data['name'];
		  }
		  else
		  A::$DB->Update("_extensions",$data,"type='block' AND name='{$data['name']}'");
		}
	  }
    }
	closedir($dh);

	foreach($blocks as $block)
	if(!is_dir("blocks/$block"))
	{ A::$DB->execute("DELETE FROM _extensions WHERE type='block' AND name=?",$block);
	  A::$DB->execute("DELETE FROM mysite_blocks WHERE block=?",$block);
	}

	return true;
  }

  function Export($idex=0)
  {
	if($idex>0)
	$_REQUEST['id']=$idex;
	else
	{ mk_dir("files/tmp");
	  clearDir("files/tmp");
	}

	$row=A::$DB->getRowById($_REQUEST['id'],"_extensions");
	if(!$row) return false;

	switch($row['type'])
	{ case 'module':
	    $prefix='module';
		$exx=A::$DB->getCol("SELECT name FROM _extensions WHERE type='module' AND name<>'{$row['name']}'");
		break;
	  case 'block':
	    $prefix='block';
		$exx=A::$DB->getCol("SELECT name FROM _extensions WHERE type='block' AND name<>'{$row['name']}'");
		break;
	}

    if(1)
    {
	  mk_dir($row['type']."s/".$row['name']."/templates");
	  mk_dir($row['type']."s/".$row['name']."/templates/admin");

	  $dh=opendir("templates/admin");
      while($filename = readdir($dh))
      { if($filename=="." || $filename=="..") continue;
	    if(preg_match("/^".$prefix."_".$row['name']."[._]/i",$filename))
	    { $fl=true;
	      foreach($exx as $ex)
	      if(mb_strlen($row['name'])<mb_strlen($ex) && preg_match("/^".$prefix."_".$ex."[._]/i",$filename))
		  $fl=false;
		  if($fl)
	      copyfile("templates/admin/$filename",$row['type']."s/".$row['name']."/templates/admin/$filename",true,true);
	    }
      }
	  closedir($dh);
	  $dh=opendir("templates/admin/forms");
      while($filename = readdir($dh))
      { if($filename=="." || $filename=="..") continue;
	    if(preg_match("/^".$prefix."_".$row['name']."[._]/i",$filename))
	    { mk_dir($row['type']."s/".$row['name']."/templates/admin/forms");
		  $fl=true;
	      foreach($exx as $ex)
	      if(mb_strlen($row['name'])<mb_strlen($ex) && preg_match("/^".$prefix."_".$ex."[._]/i",$filename))
		  $fl=false;
		  if($fl)
		  copyfile("templates/admin/forms/$filename",$row['type']."s/".$row['name']."/templates/admin/forms/$filename",true,true);
	    }
      }
	  closedir($dh);
	  $dh=opendir("templates/admin/others");
      while($filename = readdir($dh))
      { if($filename=="." || $filename=="..") continue;
	    if(preg_match("/^statistic_".$row['type']."_".$row['name']."[._]/i",$filename))
	    { mk_dir($row['type']."s/".$row['name']."/templates/admin/others");
	      $fl=true;
	      foreach($exx as $ex)
	      if(mb_strlen($row['name'])<mb_strlen($ex) && preg_match("/^statistic_".$row['type']."_".$ex."[._]/i",$filename))
		  $fl=false;
		  if($fl)
		  copyfile("templates/admin/others/$filename",$row['type']."s/".$row['name']."/templates/admin/others/$filename",true,true);
	    }
	    elseif(preg_match("/^special_".$row['type']."_".$row['name']."[._]/i",$filename))
	    { mk_dir($row['type']."s/".$row['name']."/templates/admin/others");
	      $fl=true;
	      foreach($exx as $ex)
	      if(mb_strlen($row['name'])<mb_strlen($ex) && preg_match("/^special_".$row['type']."_".$ex."[._]/i",$filename))
		  $fl=false;
		  if($fl)
		  copyfile("templates/admin/others/$filename",$row['type']."s/".$row['name']."/templates/admin/others/$filename",true,true);
	    }
      }
	  closedir($dh);

	  $dh=opendir("templates/admin/mails");
      while($filename = readdir($dh))
      { if($filename=="." || $filename=="..") continue;
	    if(preg_match("/^".$row['name']."[._]/i",$filename))
	    { mk_dir($row['type']."s/".$row['name']."/templates/admin/mails");
	      $fl=true;
	      foreach($exx as $ex)
	      if(mb_strlen($row['name'])<mb_strlen($ex) && preg_match("/^".$prefix."_".$ex."[._]/i",$filename))
		  $fl=false;
		  if($fl)
		  copyfile("templates/admin/mails/$filename",$row['type']."s/".$row['name']."/templates/admin/mails/$filename",true,true);
	    }
      }
	  closedir($dh);

    }
    if($row['type']=='block')
	$xml = loadXML($xmlf=$row['type']."s/".$row['name']."/install.xml",true);
	else
	$xml = loadXML($xmlf=$row['type']."s/".$row['name']."/sql-xml/install.xml",true);
	if($xml)
    { $xml['name']=$row['caption'];
	  $xml['sort']=$row['sort'];
	  if($xml['version']!=$row['version'] || $xml['system']!=$row['system'])
	  { $xml['version']=$row['version'];
        $xml['system']=$row['system'];
      }
	  @file_put_contents($xmlf,getXML($xml,'extension'));
    }

	$file="files/tmp/".$row['type']."_".$row['name']."_".preg_replace("/([0-9]{2})$/",".\\1",$row['version']).".tar.gz";

	if($idex>0)
	{ if(createArchive($file,$row['type']."s/".$row['name']))
	  return $file;
	  else
	  return false;
	}
	else
	return outArchive($file,$row['type']."s/".$row['name']);
  }

  function ExportAll()
  {
	if(!extension_loaded('zip'))
	return false;

	mk_dir("files/tmp");
    clearDir("files/tmp");

    switch($_REQUEST['type'])
    { case 'module':
      case 'block': $type=$_REQUEST['type']; break;
      default: $type="module"; break;
    }

    require_once('HTTP/Download.php');

    $zip = new ZipArchive;
    $zip->open("files/tmp/{$type}s.zip",ZIPARCHIVE::CREATE);

    A::$DB->query("SELECT * FROM _extensions WHERE type='{$type}'");
    while($row=A::$DB->fetchRow())
    if($file=$this->Export($row['id']))
    $zip->addFile($file,basename($file));
    A::$DB->free();

	$zip->close();
	if(is_file("files/tmp/{$type}s.zip"))
	{ $params['file'] = "files/tmp/{$type}s.zip";
      $params['contenttype'] = "application/zip";
      $params['contentdisposition'] = array(HTTP_DOWNLOAD_ATTACHMENT,"{$type}s.zip");
	  HTTP_Download::staticSend($params, false);
      return true;
    }
  }

  function Del()
  {
    $erow=A::$DB->getRowById($_REQUEST['id'],"_extensions");
	if(!$erow) return false;

	clearDir("files/tmp");

	if($erow['type']=='module' && A::$DB->existsRow("SELECT * FROM mysite_sections WHERE module=?",$erow['name']))
	{ $this->errors['err_modules']=true;
	  return false;
	}
	if($erow['type']=='block' && A::$DB->existsRow("SELECT * FROM mysite_blocks WHERE block=?",$erow['name']))
	{ $this->errors['err_blocks']=true;
	  return false;
	}

	delDir($erow['type']."s/".$erow['name']."/");
	A::$DB->execute("DELETE FROM _extensions WHERE id=".$erow['id']);

	return true;
  }

  function createData()
  {
    $this->Assign("caption","Установленные расширения");
    $this->Assign("usezip",extension_loaded('zip'));

    A::$DB->query("SELECT * FROM _extensions WHERE type='module' ORDER BY sort,id");
    $modules=array();
	while($row=A::$DB->fetchRow())
	{ $ico="modules/{$row['name']}/ico.gif";
	  $row['ico']=file_exists($ico)?AddImage('/'.$ico,16,16,$row['caption']):AddImage("/templates/admin/images/icons/main_sections.gif",16,16);
	  $modules[]=$row;
	}
	A::$DB->free();
	$this->Assign("modules",$modules);

    A::$DB->query("
	SELECT * FROM _extensions WHERE type='block' ORDER BY sort,id");
    $blocks=array();
	while($row=A::$DB->fetchRow())
	$blocks[]=$row;
	A::$DB->free();
	$this->Assign("blocks",$blocks);
  }
}

A::$MAINFRAME = new SystemExtensions;