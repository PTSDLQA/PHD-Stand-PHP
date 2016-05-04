<?php
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AComponents
 */
/**************************************************************************/

/**
 * Серверная сторона AJAX для компонента "файловый менеджер".
 */

class A_FileAdminRequest extends A_Request
{
  private $basedir;
  private $curdir;
  private $image_ext=array('gif','jpg','jpeg','bmp','png');
  private $text_ext=array('php','txt','html','htm','css','js','tpl','xml','sql');
  private $cpage=25;

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
	$this->basedir='./'.preg_replace("/^\//i","",preg_replace("/[^a-zA-Zа-яА-Я0-9_\/-]/iu","",A_Session::get($_POST['oid']."_basedir")));
	$this->curdir='./'.preg_replace("/^\//i","",preg_replace("/[^a-zA-Zа-яА-Я0-9_\/-]/iu","",A_Session::get($_POST['oid']."_curdir")));

	switch($action)
    { case "getdir": $this->getdir(); break;
	  case "back": $this->back(); break;
	  case "rename": $this->rename(); break;
	  case "delfolder": $this->deldir(); break;
	  case "delfile": $this->delfile(); break;
	  case "mkfolder": $this->mkdir(); break;
	  case "mkfile": $this->mkfile(); break;
	  case "editfile": $this->editfile(); break;
	  case "savefile": $this->savefile(); break;
	  case "uploadform": $this->uploadform(); break;
	  case "upload": $this->upload(); break;
	  default: $this->getgrid();
    }
  }

/**
 * Обработчик действия: Содержимое текущего каталога.
 */

  function getdir()
  {
    $_POST['dir']=preg_replace("/[^a-zA-Zа-яА-Я0-9_-]/iu","",$_POST['dir']);
    if(is_dir($this->curdir.$_POST['dir']))
	{ A_Session::set($_POST['oid']."_curdir",$this->curdir.$_POST['dir'].'/');
	  $this->curdir=$this->curdir.$_POST['dir'].'/';
	}
	$this->getgrid();
  }

/**
 * Обработчик действия: В каталог выше.
 */

  function back()
  {
    if($this->curdir!=$this->basedir)
	{ $this->curdir=preg_replace("/[a-zA-Zа-яА-Я0-9_-]+\/$/iu","",$this->curdir);
	  A_Session::set($_POST['oid']."_curdir",$this->curdir);
    }
	$this->getgrid();
  }

/**
 * Обработчик действия: Переименование файла или каталога.
 */

  function rename()
  {
    $_POST['name']=preg_replace("/[^a-zA-Zа-яА-Я0-9._-]/iu","",$_POST['name']);
    $_POST['newname']=preg_replace("/[^a-zA-Zа-яА-Я0-9._-]/iu","",$_POST['newname']);

	if(!is_file($this->curdir.$_POST['newname']))
	{ rename($this->curdir.$_POST['name'],$this->curdir.$_POST['newname']);
	  $this->RESULT['result']=true;
	}
	else
	$this->RESULT['result']=false;
	$this->getgrid();
  }

/**
 * Обработчик действия: Удаление каталога.
 */

  function deldir()
  {
    $_POST['name']=preg_replace("/[^a-zA-Zа-яА-Я0-9_-]/iu","",$_POST['name']);
    $this->RESULT['result']=delDir($this->curdir.$_POST['name']);
	A::$OBSERVER->Event('FileManager_DelDir',$this->curdir.$_POST['name']);
	$this->getgrid();
  }

/**
 * Обработчик действия: Удаление файла.
 */

  function delfile()
  {
    $_POST['name']=preg_replace("/[^a-zA-Zа-яА-Я0-9._-]/iu","",$_POST['name']);

	if(is_file($this->curdir.$_POST['name']))
	{ unlink($this->curdir.$_POST['name']);
	  $this->RESULT['result']=true;
	  A::$OBSERVER->Event('FileManager_DelFile',$this->curdir.$_POST['name']);
	}
	else
	$this->RESULT['result']=false;
	$this->getgrid();
  }

/**
 * Обработчик действия: Создание каталога.
 */

  function mkdir()
  {
    $_POST['name']=preg_replace("/[^a-zA-Zа-яА-Я0-9_-]/iu","",$_POST['name']);
	$this->RESULT['result']=mk_dir($this->curdir.$_POST['name']);
	A::$OBSERVER->Event('FileManager_MkDir',$this->curdir.$_POST['name']);
    $this->getgrid();
  }

/**
 * Обработчик действия: Создание файла.
 */

  function mkfile()
  {
    $_POST['name']=preg_replace("/[^a-zA-Zа-яА-Я0-9._-]/iu","",$_POST['name']);
	if(!is_file($this->curdir.$_POST['name']))
	{ $this->RESULT['result']=file_put_contents($this->curdir.$_POST['name'],'');
	  A::$OBSERVER->Event('FileManager_MkFile',$this->curdir.$_POST['name']);
	}
	$this->getgrid();
  }

/**
 * Обработчик действия: Отдает форму редактирования файла.
 */

  function editfile()
  {
    $_POST['name']=preg_replace("/[^a-zA-Zа-яА-Я0-9._-]/iu","",$_POST['name']);

	if(is_file($this->curdir.$_POST['name']))
	{ $path_parts=pathinfo($this->curdir.$_POST['name']);
	  $ext=preg_replace("/[^a-z0-9]/i","",mb_strtolower($path_parts['extension']));

	  if(in_array($ext,$this->text_ext))
	  { $form = new A_Form("objcomp_fileadmin_edit.tpl");
	    $this->RESULT['title']="Редактор файла '".$_POST['name']."'";
	    $this->RESULT['height']=650;
	    $this->RESULT['ext']=$ext;
		$form->data['name']=$_POST['name'];
	    $form->data['text']=@file_get_contents($this->curdir.$_POST['name']);
		$form->data['tpls']=array();

	    if($ext=='tpl')
	    { if(preg_match_all("/\{block id=\"([a-z0-9-_]+)\"/i",$form->data['text'],$matches))
	      foreach($matches[1] as $block)
	      if($block=A::$DB->getRow("SELECT * FROM mysite_blocks WHERE name=?",$block))
		  { $params=!empty($block['params'])?unserialize($block['params']):array();
		    if(!empty($params['template']))
		    $form->data['tpls']['blocks/'.$params['template']]='> blocks/'.$params['template'];
		  }
		  if(strpos($this->curdir,'/blocks/')!==false)
	      { $blocks=array();
		    A::$DB->query("SELECT * FROM mysite_blocks");
	        while($block=A::$DB->fetchRow())
			{ $params=!empty($block['params'])?unserialize($block['params']):array();
		      if(!empty($params['template']) && $params['template']==$_POST['name'])
		      { if(!empty($block['name']))
			    $blocks[]=$block['name'];
		        if(!empty($block['frame']))
			    $form->data['tpls']['frames/'.$block['frame']]='< frames/'.$block['frame'];
			  }
		    }
	        A::$DB->free();
	        if(!empty($blocks))
			foreach($blocks as $block)
			{ $files=scandir('templates/mysite');
		      sort($files);
		      foreach($files as $file)
		      if(preg_match("/\.tpl$/i",$file))
		      { $content=@file_get_contents('templates/mysite/'.$file);
		        if(strpos($content,"{block id=\"{$block}\"")!==false)
		        $form->data['tpls'][$file]='< '.$file;
		      }
		    }
	      }
	      elseif(strpos($this->curdir,'/frames/')!==false)
	      { A::$DB->query("SELECT * FROM mysite_blocks WHERE frame=?",$_POST['name']);
	        while($block=A::$DB->fetchRow())
            { $params=!empty($block['params'])?unserialize($block['params']):array();
		      if(!empty($params['template']))
		      $form->data['tpls']['blocks/'.$params['template']]='> blocks/'.$params['template'];
		    }
	        A::$DB->free();
	      }
	      else
	      { if(preg_match_all("/\{include file=\"([a-z0-9.-_]+)\"/i",$form->data['text'],$matches))
		    foreach($matches[1] as $file)
		    $form->data['tpls'][$file]='> '.$file;

		    $files=scandir('templates/mysite');
		    sort($files);
		    foreach($files as $file)
		    if(preg_match("/\.tpl$/i",$file))
		    { $content=@file_get_contents('templates/mysite/'.$file);
		      if(strpos($content,"{include file=\"{$_POST['name']}\"")!==false)
		      $form->data['tpls'][$file]='< '.$file;
		    }
          }
	    }

	    $this->RESULT['type']=1;
	    $this->RESULT['html']=$form->getContent();

	  }
	  else
	  $this->RESULT['type']=3;
	}
	else
	$this->RESULT['type']=3;
  }

/**
 * Обработчик действия: Сохранение файла.
 */

  function savefile()
  {
    $_POST['name']=preg_replace("/[^a-zA-Zа-яА-Я0-9._-]/iu","",$_POST['name']);
	$this->RESULT['result']=file_put_contents($this->curdir.$_POST['name'],trim($_POST['text']));
	A::$OBSERVER->Event('FileManager_UpdateFile',$this->curdir.$_POST['name']);
  }

/**
 * Обработчик действия: Отдает форму загрузки файла.
 */

  function uploadform()
  {
    $form = new A_Form("objcomp_fileadmin_upload.tpl");
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Загрузка файла.
 */

  function upload()
  {
	$messages=array();
	$files=array();

	for($i=0;$i<=5;$i++)
	if(isset($_FILES['fa_file'.$i]['tmp_name']) && file_exists($_FILES['fa_file'.$i]['tmp_name']))
	{
	  $filename=escapeFileName($_FILES['fa_file'.$i]['name'],$ext);

	  if(!$filename) continue;

	  if(file_exists($this->curdir.$filename) && !isset($_POST['replace']))
	  $messages[]='Файл c именем "'.$filename.'" уже существует';
      else
	  { if(in_array($ext,$this->text_ext))
	    { if($content=@file_get_contents($_FILES['fa_file'.$i]['tmp_name']))
		  file_put_contents($this->curdir.$filename,!mb_check_encoding($content,'UTF-8')?mb_convert_encoding($content,'UTF-8','Windows-1251'):$content);
	    }
	    else
	    copyfile($_FILES['fa_file'.$i]['tmp_name'],$this->curdir.$filename,true);
	    $files[]=$this->curdir.$filename;
	  }

	  A::$OBSERVER->Event('FileManager_Upload','mysite',$files);
	}

	$this->RESULT['messages']=$messages;
	$this->getgrid();
  }

/**
 * Формирование таблицы с содержимым текущего каталога.
 */

  function getgrid()
  {
	if(!isset($_GET['page'])) $_GET['page']=0;

    require_once("Image/Transform.php");
	$it = Image_Transform::factory("GD");

    $folders=array();
    $files=array();

	if(!is_dir($this->curdir))
	$this->curdir=$this->basedir;

    $dh=opendir($this->curdir);
    while(false !== ($filename = readdir($dh)))
    { if($filename=="." || $filename=="..") continue;
	  if(is_dir($this->curdir.$filename))
	  $folders[]=$filename;
	  elseif($filename!=".htaccess")
	  { $path_parts=pathinfo($filename);
	    $files[]=array('name'=>$filename,'ext'=>preg_replace("/[^a-z0-9]/i","",mb_strtolower($path_parts['extension'])));
	  }
	}
    sort($folders);
	$files=array_multisort_key($files,array('ext','name'));

	$grid = new A_Grid(7);
	$grid->title=$this->curdir;
	$grid->width=array("20","",80,120,20,20,20);
	$grid->headers=array("&nbsp;","Название","Размер","Дата","&nbsp;","&nbsp;","&nbsp;");

    if($this->curdir!=$this->basedir)
	{ $row[0]=AddImageButton("/templates/admin/images/back.gif","inback()","На уровень вверх",16,16);
	  $row[1]=AddClickText("...","inback()","На уровень вверх");
	  $row[2]=$row[3]=$row[4]=$row[5]=$row[6]="&nbsp;";
	  $grid->AddRow($row);
	}

	$i=0;

	foreach($folders as $folder)
	{ if($i>=$_GET['page']*$this->cpage && $i<$_GET['page']*$this->cpage+$this->cpage)
	  { $row[0]=AddImageButton("/templates/admin/images/dir.gif","indir('$folder')","Войти в каталог",16,16);
	    $row[1]=AddClickText($folder,"indir('$folder')","Войти в каталог");
		$row[2]="-";
		$row[3]=strftime("%d.%m.%Y %H:%M:%S",filemtime($this->curdir.$folder));
		$row[4]="&nbsp;";
		$row[5]=AddImageButton("/templates/admin/images/edit.gif","renamefolder('$folder')","Переименовать",16,16);
		$row[6]=AddImageButton("/templates/admin/images/del.gif","delfolder('$folder')","Удалить",16,16);
	    $grid->AddRow($row);
	  }
	  $i++;
    }

	foreach($files as $frow)
	{ $file=$frow['name'];
	  $ext=$frow['ext'];
	  if($i>=$_GET['page']*$this->cpage && $i<$_GET['page']*$this->cpage+$this->cpage)
	  { if(in_array($ext,$this->image_ext))
	    $row[0]=AddImageButton("/templates/admin/images/image.gif","editfile('$file')","Просмотр",16,16);
	    elseif($ext=="tpl")
		$row[0]=AddImageButton("/templates/admin/images/template.gif","editfile('$file')","Редактировать",16,16);
		elseif(in_array($ext,$this->text_ext))
	    $row[0]=AddImageButton("/templates/admin/images/text.gif","editfile('$file')","Редактировать",16,16);
		else
	    $row[0]=AddImage("/templates/admin/images/file.gif",16,16);
	    if(in_array($ext,$this->image_ext))
	    { $it->load($this->curdir.$file);
	      $row[1]=AddClickText($file,"open_imgwindow('/{$this->curdir}{$file}','$file',$it->img_x,$it->img_y)","Просмотр");
	    }
		elseif($ext=="tpl")
		{ $text=@file_get_contents($this->curdir.$file);
		  $desc=preg_match("/^[^\n]*\{\*([^*^}]*)\*\}[^\n]*\n/i",$text,$matches)>0?$matches[0]:"";
		  $row[1]=AddClickText($file." ".$desc,"editfile('$file')","Редактировать");
		}
		elseif(in_array($ext,$this->text_ext))
		$row[1]=AddClickText($file,"editfile('$file')","Редактировать");
		else
		$row[1]=$file;
		$row[2]=sizestring(filesize($this->curdir.$file));
		$row[3]=strftime("%d.%m.%Y %H:%M:%S",filemtime($this->curdir.$file));
		$row[4]=AddImageButtonLink("/templates/admin/images/save.gif","download.php?file=".urlencode(preg_replace("/^\.\//i","",$this->curdir.$file)),"Скачать",16,16);
		$row[5]=AddImageButton("/templates/admin/images/edit.gif","renamefile('$file')","Переименовать",16,16);
		$row[6]=AddImageButton("/templates/admin/images/del.gif","delfile('$file')","Удалить",16,16);
		$grid->AddRow($row);
	  }
	  $i++;
    }

	$pager = new A_Pager($this->cpage,"fagopage");
	$pager->setPages(ceil((count($folders)+count($files))/$this->cpage));

	$this->RESULT['html']=$grid->getContent().$pager->getContent();
  }
}

A::$REQUEST = new A_FileAdminRequest;