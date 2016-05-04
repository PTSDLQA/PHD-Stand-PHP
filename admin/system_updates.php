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

class SystemUpdates extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("system_updates.tpl");
  }

  function Action($action)
  {
	$res=false;
    switch($action)
	{ case "update": $res=$this->Update(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=system&item=updates".($res!==true?"&update=$res":""));
  }

  function Update()
  {
    mk_dir("files/tmp");
	clearDir("files/tmp");

	if(isset($_FILES['updatefile']['tmp_name']) && file_exists($_FILES['updatefile']['tmp_name']))
    { $path_parts=pathinfo($_FILES['updatefile']['name']);
	  $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
	  if($ext=='gz')
	  { try
	    { return $this->UpdateSystem($_FILES['updatefile']['tmp_name']);
	    }
	    catch(exception $exception)
	    { print $exception->__toString().'<br>';
		  return false;
	    }
	  }
	}
	return "err_file";
  }

  function writeable($dirIn='files/tmp/files',$dirTo='.')
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

  function UpdateSystem($path)
  {
	if(extractArchive($path,"files/tmp"))
	{
	  $xml = loadXML("files/tmp/update.xml",true);

	  if(!$xml) return false;

	  $fupdate = empty($xml['version']) || (!empty($xml['version']) && $xml['version']>A::$OPTIONS['version']);

	  if($fupdate || !empty($xml['tofull']))
	  {
		if(!$this->writeable())
		return "err_unpack";

		copyDir("files/tmp/files",".",true);

		if($sql=@file_get_contents("files/tmp/sql/tofull.sql"))
		{ $sql=str_replace("{domainname}",preg_replace("/^www\./i","",HOSTNAME),$sql);
		  A::$DB->execSQL($sql);
		}

		if(!empty($xml['version']))
		{
		  $from=(float)preg_replace("/[0-9]{2}$/","",A::$OPTIONS['version']);
		  $to=(float)preg_replace("/[0-9]{2}$/","",$xml['version']);

		  for($v=$from;$v<$to;$v+=0.01)
		  { $sqlfile=str_replace(",",".",sprintf("files/tmp/sql/domain_%.2f-%.2f.sql",$v,$v+0.01));
		    if($sql=@file_get_contents($sqlfile))
		    { $sql=str_replace("{domain}","mysite",$sql);
		      A::$DB->execSQL($sql);
		      copyfile($sqlfile,"system/sql-xml/".basename($sqlfile),true);
		    }

			$sqlfile=str_replace(",",".",sprintf("files/tmp/sql/system_%.2f-%.2f.sql",$v,$v+0.01));
		    if($sql=@file_get_contents($sqlfile))
		    A::$DB->execSQL($sql);

            $phpfile=str_replace(",",".",sprintf("files/tmp/php/update_%.2f-%.2f.php",$v,$v+0.01));
		    if(is_file($phpfile))
		    require_once($phpfile);
		  }

		  A::$DB->Update("_options",array('value'=>$xml['version']),"var='version'");
		}

		delFilesByDir("templates_c/admin");
		delDir("files/tmp");

		return "ok" ;
	  }
	  else
	  return "err_version";
	}
	return "err_file";
  }

  function createData()
  {
    $this->Assign("caption","Обновление");
  }
}

A::$MAINFRAME = new SystemUpdates;