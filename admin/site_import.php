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

if(extension_loaded('zip'))
{
  class Zip extends ZipArchive
  {
    function addDir($path,$lpath='')
	{
      if(!empty($lpath))
	  $this->addEmptyDir($lpath);
      $files=scandir($path);
      foreach($files as $file)
	  if($file!='.' && $file!='..')
	  { if(is_dir($path.'/'.$file))
		$this->addDir($path.'/'.$file,!empty($lpath)?($lpath.'/'.$file):$file);
        elseif(is_file($path.'/'.$file))
		$this->addFile($path.'/'.$file,$lpath.'/'.$file);
      }
    }
  }
}

class SiteImport extends A_MainFrame
{
  static $eupdate=false;

  function __construct()
  {
    parent::__construct("site_import.tpl");
  }

  function Action($action)
  {
    @set_time_limit(0);

	$res=false;
	if(A::$AUTH->isSuperAdmin())
    switch($action)
	{ case "import": $res=$this->Import(); break;
	  case "export": $res=$this->Export(); break;
	}
	if($res)
	{ $link="admin.php?mode=site&item=import";
	  if($res!==true) $link.="&import=$res";
	  A::goUrl($link);
	}
	else
	A::goUrl("admin.php?mode=site&item=import&import=failed");
  }

  function install_extension($type,$name)
  {
    if(is_dir("files/mysite/tmp/extensions/{$type}s/{$name}"))
	{ if(copyDir("files/mysite/tmp/extensions/{$type}s/{$name}","{$type}s/{$name}",true))
	  { SiteImport::$eupdate=true;
	    return true;
	  }
	}
	return false;
  }

  function Import()
  {
    switch($_REQUEST['importmode'])
	{ case 1:
	    if(isset($_FILES['importfile']['tmp_name']))
		{ $path=$_FILES['importfile']['tmp_name'];
          if(empty($path) || !is_file($path))
		  return false;
		  $path_parts=pathinfo($_FILES['importfile']['name']);
	      $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
		}
		else
		return false;
		break;
	  case 2:
	    $path=$_REQUEST['importpath'];
        if(empty($path) || !is_file($path))
		return false;
		$path_parts=pathinfo($path);
	    $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
		break;
	  case 3:
	    if(isset($_FILES['configarch']['tmp_name']))
		{ $path=$_FILES['configarch']['tmp_name'];
          if(empty($path) || !is_file($path))
		  return false;
		  $path_parts=pathinfo($_FILES['configarch']['name']);
	      $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
		}
		else
		return false;
		break;
	}

	if($ext=='gz' || $ext=='zip')
	{
	  mk_dir("files/mysite/tmp");
	  clearDir("files/mysite/tmp");

	  if(extractArchive($path,"files/mysite/tmp",$ext))
	  {
		$xml = loadXML("files/mysite/tmp/configuration.xml",true);

		if(!$xml) return false;

		if(empty($xml['free']))
		return urlencode("Для импорта конфигурации необходима полная версия системы");

		if($xml['system']>A::$OPTIONS['version'])
		return urlencode("Для импорта конфигурации необходима версия системы {$xml['system']} или выше.");

		$modules=A::$DB->getAssoc("SELECT name,version FROM _extensions WHERE type='module'");
		$blocks=A::$DB->getAssoc("SELECT name,version FROM _extensions WHERE type='block'");

		$updates=array();

		if(!empty($xml['modules']['module']))
		{ if(!isset($xml['modules']['module'][0]))
		  $xml['modules']['module']=array($xml['modules']['module']);

		  foreach($xml['modules']['module'] as $module)
		  { if(!isset($modules[$module['name']]))
		    { if(!SiteImport::install_extension('module',$module['name']))
		      return urlencode("Для импорта конфигурации необходим установленный модуль '{$module['name']}'.");
		    }
			elseif($module['version']>$modules[$module['name']])
			{ if(SiteImport::install_extension('module',$module['name']))
			  { $from=(float)preg_replace("/[0-9]{2}$/","",$modules[$module['name']]);
			    $to=(float)preg_replace("/[0-9]{2}$/","",$module['version']);
			    if(($to-$from)>0)
			    $updates[]=array('type'=>'module','name'=>$module['name'],'from'=>$from,'to'=>$to);
			  }
			  else
		  	  return urlencode("Для импорта конфигурации необходим установленный модуль '{$module['name']}' версии {$module['version']} или выше.");
			}
			elseif($module['version']<$modules[$module['name']])
			{ $from=(float)preg_replace("/[0-9]{2}$/","",$module['version']);
			  $to=(float)preg_replace("/[0-9]{2}$/","",$modules[$module['name']]);
			  if(($to-$from)>0)
			  $updates[]=array('type'=>'module','name'=>$module['name'],'from'=>$from,'to'=>$to);
			}
		  }
		}

		if(!empty($xml['blocks']['block']))
		{ if(!isset($xml['blocks']['block'][0]))
		  $xml['blocks']['block']=array($xml['blocks']['block']);

		  foreach($xml['blocks']['block'] as $block)
		  { if(!isset($blocks[$block['name']]))
		    { if(!SiteImport::install_extension('block',$block['name']))
			  return urlencode("Для импорта конфигурации необходим установленный блок '{$block['name']}'.");
			}
			elseif($block['version']>$blocks[$block['name']])
			{ if(!SiteImport::install_extension('block',$block['name']))
			  return urlencode("Для импорта конфигурации необходим установленный блок '{$block['name']}' версии {$block['version']} или выше.");
			}
		  }
		}

		if(is_dir("files/mysite/tmp/files"))
		{ $dh=opendir("files/mysite");
          while($filename = readdir($dh))
          { if($filename=="." || $filename==".." || $filename=='tmp') continue;
            if(is_dir("files/mysite/".$filename))
			delDir("files/mysite/".$filename);
			else
			delfile("files/mysite/".$filename);
          }
          closedir($dh);

		  $dh=opendir("files/mysite/tmp/files");
          while($filename = readdir($dh))
          { if($filename=="." || $filename==".." || $filename=='tmp') continue;
            @rename("files/mysite/tmp/files/$filename","files/mysite/$filename");
          }
          closedir($dh);

          delFilesByDir("cache/images/mysite");
		}

		if(is_dir("files/mysite/tmp/templates"))
		{ clearDir("templates/mysite");
		  clearDir("wizard_config/mysite");
		  delFilesByDir("templates_c/mysite");
		  copyDir("files/mysite/tmp/templates","templates/mysite");
		}

		if(is_dir("files/mysite/tmp/wizard_config"))
		copyDir("files/mysite/tmp/wizard_config","wizard_config/mysite");

		if(is_dir("files/mysite/tmp/db"))
		{
		  $tables=A::$DB->getTables();
	      foreach($tables as $table)
	      if(preg_match("/^mysite_/i",$table))
	      A::$DB->execute("DROP TABLE `$table`");

          $i=0;
		  while($sql=@file_get_contents("files/mysite/tmp/db/dump{$i}.sql"))
          { $sql=str_replace("{DOMAIN}","mysite",$sql);
		    A::$DB->execSQL($sql);
			$i++;
		  }

		  $from=(float)preg_replace("/[0-9]{2}$/","",$xml['system']);
		  $to=(float)preg_replace("/[0-9]{2}$/","",A::$OPTIONS['version']);

		  for($v=$from;$v<$to;$v+=0.01)
		  { $sqlfile=str_replace(",",".",sprintf("system/sql-xml/domain_%.2f-%.2f.sql",$v,$v+0.01));
			if($sql=@file_get_contents($sqlfile))
		    A::$DB->execSQL(str_replace("{domain}","mysite",$sql));
		  }

		  foreach($updates as $update)
		  switch($update['type'])
		  { case "module":
		      $sections=getSectionsByModule($update['name']);
			  foreach($sections as $section)
			  { for($v=$update['from'];$v<$update['to'];$v+=0.01)
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

		if(SiteImport::$eupdate)
		{ require_once("admin/system_extensions.php");
		  SystemExtensions::Update();
		}

		delDir("files/mysite/tmp");

		return "ok";
	  }
	}
    return false;
  }

  function Export()
  {
	mk_dir("files/mysite/tmp");
    clearDir("files/mysite/tmp");

	require_once('HTTP/Download.php');

	$usezip = $_REQUEST['type']=='zip' && extension_loaded('zip');

    if($usezip)
    { $zip = new Zip;
      $zip->open("files/mysite/tmp/configuration.zip",ZIPARCHIVE::CREATE);
    }
    else
    { require_once("Archive/Tar.php");
	  $arch = new Archive_Tar("files/mysite/tmp/configuration.tar.gz",true);
    }

	if(isset($_REQUEST['bd']))
	{
	  mk_dir("files/mysite/tmp/db");

	  $this->mysqlbackup("files/mysite/tmp/db");

      $i=0;
	  while($sql=@file_get_contents("files/mysite/tmp/db/dump{$i}.sql"))
	  { $sql=str_replace("http://".HOSTNAME."/","/",$sql);
        delfile("files/mysite/tmp/db/dump{$i}.sql");
        file_put_contents("files/mysite/tmp/db/dump{$i}.sql",$sql);
		$i++;
	  }

	  if($usezip)
	  $zip->addDir("files/mysite/tmp/db","db");
	  else
	  $arch->addModify(array("files/mysite/tmp/db"),'',"files/mysite/tmp");
	}

	if(isset($_REQUEST['theme']))
	{
	  copyDir("templates/mysite","files/mysite/tmp/templates");
	  if($usezip)
	  $zip->addDir("files/mysite/tmp/templates","templates");
	  else
	  $arch->addModify(array("files/mysite/tmp/templates"),'',"files/mysite/tmp");
	  if($data=@file_get_contents('wizard_config/mysite/config.dat'))
      { $config=unserialize($data);
        if(!empty($config['theme']['name']))
        { copyDir('wizard_config/mysite',"files/mysite/tmp/wizard_config");
		  delFilesByDir("files/mysite/tmp/wizard_config",array('preview.html','preview.css'));
		  if($usezip)
	      $zip->addDir("files/mysite/tmp/wizard_config","wizard_config");
	      else
          $arch->addModify(array("files/mysite/tmp/wizard_config"),'',"files/mysite/tmp");
        }
      }
	}

	if(isset($_REQUEST['extensions']))
	{
	  mk_dir("files/mysite/tmp/extensions");

	  A::$DB->query("SELECT DISTINCT module FROM mysite_sections ORDER BY module");
	  while($row=A::$DB->fetchRow())
	  { mk_dir("files/mysite/tmp/extensions/modules");
	    copyDir("modules/".$row['module'],"files/mysite/tmp/extensions/modules/{$row['module']}");
	  }
	  A::$DB->free();

	  A::$DB->query("SELECT DISTINCT block FROM mysite_blocks ORDER BY block");
	  while($row=A::$DB->fetchRow())
	  { mk_dir("files/mysite/tmp/extensions/blocks");
	    copyDir("blocks/".$row['block'],"files/mysite/tmp/extensions/blocks/{$row['block']}");
	  }
	  A::$DB->free();

      if($usezip)
	  $zip->addDir("files/mysite/tmp/extensions","extensions");
	  else
	  $arch->addModify(array("files/mysite/tmp/extensions"),'',"files/mysite/tmp");
	}

	$data=array('system'=>A::$OPTIONS['version'],'free'=>'Y','name'=>'');

	if(isset($_REQUEST['bd']) || file_exists("files/mysite/tmp/extensions"))
	{
	  $modules=A::$DB->getAssoc("SELECT name,version FROM _extensions WHERE type='module'");
	  $blocks=A::$DB->getAssoc("SELECT name,version FROM _extensions WHERE type='block'");

	  A::$DB->query("SELECT DISTINCT module FROM mysite_sections ORDER BY module");
	  while($row=A::$DB->fetchRow())
	  $data['modules']['module'][]=array('name'=>$row['module'],'version'=>$modules[$row['module']]);
	  A::$DB->free();

	  A::$DB->query("SELECT DISTINCT block FROM mysite_blocks ORDER BY block");
	  while($row=A::$DB->fetchRow())
	  $data['blocks']['block'][]=array('name'=>$row['block'],'version'=>$blocks[$row['block']]);
	  A::$DB->free();
	}

	file_put_contents("files/mysite/tmp/configuration.xml",getXML($data,'configuration'));

	if($usezip)
	$zip->addFile("files/mysite/tmp/configuration.xml","configuration.xml");
	else
	$arch->addModify(array("files/mysite/tmp/configuration.xml"),'',"files/mysite/tmp");

	if(isset($_REQUEST['files']))
	{ $files=$_files=array();
	  $dh=opendir("files/mysite");
      while($filename = readdir($dh))
      { if($filename=="." || $filename==".." || $filename=='tmp') continue;
        $files[]=$filename;
        $_files[]="files/mysite/".$filename;
      }
      closedir($dh);

	  if($usezip)
	  { foreach($files as $file)
	    if(is_dir("files/mysite/".$file))
	    $zip->addDir("files/mysite/".$file,"files/$file");
	    elseif(is_file("files/mysite/".$file))
	    $zip->addFile("files/mysite/".$file,"files/".$file);
	  }
	  else
	  $arch->addModify($_files,'files',"files/mysite");
	}


	if($usezip)
	{ $zip->close();
	  if(file_exists("files/mysite/tmp/configuration.zip"))
	  { $params['file'] = "files/mysite/tmp/configuration.zip";
        $params['contenttype'] = "application/zip";
        $params['contentdisposition'] = array(HTTP_DOWNLOAD_ATTACHMENT, "mysite.zip");
	    HTTP_Download::staticSend($params, false);
        return true;
      }
	}
	elseif(file_exists("files/mysite/tmp/configuration.tar.gz"))
    { $params['file'] = "files/mysite/tmp/configuration.tar.gz";
      $params['contenttype'] = "application/gzip";
      $params['contentdisposition'] = array(HTTP_DOWNLOAD_ATTACHMENT, "mysite.tar.gz");
	  HTTP_Download::staticSend($params, false);
      return true;
	}
	else
	return false;
  }

  function mysqlbackup($path,$structure_only=false,$crlf="\n")
  {
	$result=A::$DB->execute("SELECT VERSION() AS version");
    if($result != FALSE && $result->num_rows > 0)
	{ $row   = $result->fetch_array();
      $match = explode('.', $row['version']);
    } else {
    $result=A::$DB->execute("SHOW VARIABLES LIKE \'version\'");
    if ($result != FALSE && $result->num_rows > 0){
     $row   = $result->fetch_row();
     $match = explode('.', $row[1]);
    }
    }

    $strfrom = array('\\','\'',"\x00","\x0a","\x0d","\x1a");
    $strto = array('\\\\','\\\'','\0','\n','\r','\Z');

    if (!isset($match) || !isset($match[0])) {
     $match[0] = 3;
    }
    if (!isset($match[1])) {
     $match[1] = 21;
    }
    if (!isset($match[2])) {
     $match[2] = 0;
    }
    if(!isset($row)) {
     $row = '3.21.0';
    }

    define('MYSQL_INT_VERSION', (integer)sprintf('%d%02d%02d', $match[0], $match[1], intval($match[2])));
    define('MYSQL_STR_VERSION', $row['version']);
    unset($match);

	$filecount=0;
	$cursize=0;
    $fp = fopen($path."/dump{$filecount}.sql","wb");

    $sql = "";

    $tables=A::$DB->getTables();

	foreach($tables as $tablename)
	{

    if(!preg_match("/^mysite_/i",$tablename))
    continue;

    $tablenameout=$tablename;

    $sql="";

    $sql.="DROP TABLE IF EXISTS `$tablenameout`;".$crlf;
    $sql.="CREATE TABLE `$tablenameout`(".$crlf;

    $types=array();
    $result=A::$DB->execute("show fields  from `$tablename`");

    while ($row = $result->fetch_array())
	{ $types[]=preg_replace("/\(.+$/i","",$row['Type']);
      $sql .= "  `".$row['Field']."`";
      $sql .= ' ' . $row['Type'];
      if($row['Null'] != 'YES')
      $sql.=' NOT NULL';
	  if(isset($row['Default']))
      $sql.=' DEFAULT \'' . $row['Default'] . '\'';
      if($row['Extra'] != '')
      $sql .= ' ' . $row['Extra'];
      $sql .= ",".$crlf;
   }

   $result->free();
   $sql = ereg_replace(',' . $crlf . '$', '', $sql);

   $index=array();
   $result = A::$DB->execute("SHOW KEYS FROM `$tablename`");
    while ($row = $result->fetch_array()) {
     $ISkeyname    = $row['Key_name'];
	 $Index_type = (isset($row['Index_type'])) ? $row['Index_type'] : '';
     $ISsub_part = (isset($row['Sub_part'])) ? $row['Sub_part'] : '';
     if (mb_strtoupper($ISkeyname) != 'PRIMARY' && $row['Non_unique'] == 0) {
      $ISkeyname = "UNIQUE|$ISkeyname";
     }
     if (mb_strtoupper($Index_type) == 'FULLTEXT') {
      $ISkeyname = "FULLTEXT|$ISkeyname";
     }
     if (!isset($index[$ISkeyname])) {
      $index[$ISkeyname] = array();
     }
     if ($ISsub_part > 1) {
      $index[$ISkeyname][] = "`".$row['Column_name'].'`(' . $ISsub_part . ')';
     } else {
      $index[$ISkeyname][] = "`".$row['Column_name']."`";
     }
    }
    $result->free();

    while (list($x, $columns) = @each($index)) {
     $sql     .= ",".$crlf;
     if ($x == 'PRIMARY') {
      $sql .= '  PRIMARY KEY (';
      } else if (mb_substr($x, 0, 6) == 'UNIQUE') {
      $sql .= '  UNIQUE `' . mb_substr($x, 7) . '` (';
     } else if (mb_substr($x, 0, 8) == 'FULLTEXT') {
      $sql .= '  FULLTEXT `' . mb_substr($x, 9) . '` (';
     } else {
      $sql .= '  KEY `' . $x . '` (';
     }
     $sql     .= implode($columns, ', ') . ')';
    }
    $sql .=  $crlf.") ENGINE=MyISAM DEFAULT CHARSET=utf8;".$crlf.$crlf;

  fwrite($fp,$sql);
  $cursize+=mb_strlen($sql);
  if($cursize>614400)
  { fclose($fp);
    $filecount++;
	$cursize=0;
    $fp = fopen($path."/dump{$filecount}.sql","wb");
  }

 if($structure_only == FALSE)
 {
  $result = A::$DB->execute("SELECT * FROM  `$tablename`");

  $fields_info = $result->fetch_fields();
  $fields_cnt   = count($fields_info);

  while ($row = $result->fetch_row()) {
   $table_list     = '(';
   for ($j = 0; $j < $fields_cnt; $j++) {
    $table_list .= $fields_info[$j]->name . ', ';
   }
   $table_list = mb_substr($table_list, 0, -2);
   $table_list     .= ')';

   $sql = 'INSERT INTO `'.$tablenameout.'` VALUES (';
   for ($j = 0; $j < $fields_cnt; $j++) {
    if (!isset($row[$j])) {
     $sql .= ' NULL, ';
    } else if ($row[$j] == '0' || $row[$j] != '')
	{

     $type = strtolower($types[$j]);

     if($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' || $type == 'int' ||
                        $type == 'bigint'  ||$type == 'timestamp') {
      $sql .= $row[$j] . ', ';
     }
	 elseif($type == 'tinyblob' || $type == 'smallblob' || $type == 'mediumblob' || $type == 'blob' || $type == 'bigblob')
	 {
	   if (empty($row[$j]) && $row[$j] != '0')
	   $sql .= '\'\', ';
       else
	   $sql .= '0x' . bin2hex($row[$j]).', ';
	 }
     else
	 {
        $dummy = str_replace($strfrom,$strto,$row[$j]);
        $sql .= "'" . $dummy . "', ";
     }
    } else {
     $sql .= "'', ";
    }
   }
   $sql = ereg_replace(', $', '', $sql);
   $sql .= ");".$crlf;
   fwrite($fp,$sql);
   $cursize+=mb_strlen($sql);
   if($cursize>614400)
   { fclose($fp);
     $filecount++;
	 $cursize=0;
     $fp = fopen($path."/dump{$filecount}.sql","wb");
   }

  } $result->free(); fwrite($fp,$crlf);  } }  fclose($fp);
  }

  function createData()
  {
	$this->Assign("caption","Импорт / Экспорт");

	if(!empty($_REQUEST['import']))
	switch($_REQUEST['import'])
	{ case 'ok': $this->Assign("debugdata",'<script type="text/javascript">alert("Импорт успешно завершен.")</script>'); break;
	  case 'failed': $this->Assign("debugdata",'<script type="text/javascript">alert("Не удалось импортировать файл.")</script>'); break;
	  default: $this->Assign("debugdata",'<script type="text/javascript">alert("'.$_REQUEST['import'].'")</script>'); break;
    }

    $this->Assign("usezip",extension_loaded('zip'));
  }
}

A::$MAINFRAME = new SiteImport;