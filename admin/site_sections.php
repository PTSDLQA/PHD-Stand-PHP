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

class SiteSections extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("site_sections.tpl");

	$this->AddJScript("/admin/jscripts/site_sections.js");
  }

  function Action($action)
  {
    $res=false;
    switch($action)
	{ case "add": $res=$this->Add(); break;
	  case "edit": $res=$this->Edit(); break;
	  case "del": $res=$this->Del(); break;
	  case "save": $res=$this->Save(); break;
	  case "setactive": $res=$this->SetActive(); break;
	  case "setunactive": $res=$this->SetUnActive(); break;
	  case "delete": $res=$this->Delete(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=site&item=sections");
  }

  function Add()
  {
    $_REQUEST['name']=substr(strtolower(preg_replace("/[^a-zA-Z0-9]+/i","",$_REQUEST['name'])),0,20);
	$_REQUEST['module']=substr(preg_replace("/[^a-zA-Z0-9_-]+/i","",$_REQUEST['module']),0,20);
    $_REQUEST['lang']='ru';
    $_REQUEST['caption']=strip_tags(trim($_REQUEST['caption']));

	$f1=empty($_REQUEST['name']) || $_REQUEST['name']=="admin" || $_REQUEST['name']=="getfile";
	$f2=A::$DB->existsRow("SELECT * FROM mysite_sections WHERE name=?",$_REQUEST['name']);
	if($f1 || $f2)
	{ $this->errors['doubleid']=true;
	  return false;
	}

	if($xml=loadXML('modules/'.$_REQUEST['module'].'/sql-xml/install.xml',true))
	{ if(!empty($xml['hidden']))
	  return false;
	}
	else
	return false;

	$_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM mysite_sections")+1;
    $_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['icon']=isset($_REQUEST['icon'])?'Y':'N';
	$_REQUEST['menu']=isset($_REQUEST['menu'])?'Y':'N';
	if(A::$OPTIONS['transurl'])
	$_REQUEST['urlname']=$_REQUEST['name'];
	else
	$_REQUEST['urlname']=getURLName($_REQUEST['caption'],$_REQUEST['urlname'],"mysite_sections");

	$dataset = new A_DataSet("mysite_sections");
    $dataset->fields=array("module","name","urlname","caption","sort","active","icon","menu","lang");

	$_REQUEST["caption_ru"]=strip_tags(trim($_REQUEST["caption_ru"]));
	$_REQUEST["title_ru"]=strip_tags(trim($_REQUEST["title_ru"]));
	if(empty($_REQUEST["caption_ru"]) && $_REQUEST['module']!='pages')
	$_REQUEST["caption_ru"]=$_REQUEST['caption'];
	if(empty($_REQUEST["title_ru"]) && $_REQUEST['module']!='pages')
	$_REQUEST["title_ru"]=$_REQUEST['caption'];
	array_push($dataset->fields,"caption_ru","title_ru");

    if($_REQUEST['id']=$dataset->Insert())
    {
	  $section="mysite_ru_".$_REQUEST['name'];

      if($sql=@file_get_contents("modules/".$_REQUEST['module']."/sql-xml/create.sql"))
      { $sql=str_replace("{section}",$section,$sql);
	    $sql=str_replace("{domain}","mysite",$sql);
        try
		{ A::$DB->execSQL($sql);
		}
		catch(exception $exception)
		{ print $exception->__toString().'<br>';
		  A::$DB->Delete("mysite_sections","id=".$_REQUEST['id']);
		  return false;
		}
      }

	  if($data=loadXML("modules/".$_REQUEST['module']."/sql-xml/pages.xml",true))
	  if(!empty($data['page']))
	  { if(isset($data['page']['id']))
	    $data['page']=array($data['page']);
	    foreach($data['page'] as $page)
	    { $page['idsec']=$_REQUEST['id'];
	      $page['caption']=$page['name'];
		  $page['name']=$page['id'];
		  $page['template']=preg_replace("/^[a-zA-Z0-9]+_/i",$_REQUEST['name'].'_',$page['template']);
		  unset($page['id']);
	      A::$DB->Insert("mysite_templates",$page);
	    }
	  }

	  if($data=loadXML("modules/".$_REQUEST['module']."/sql-xml/options.xml",true))
	  if(!empty($data['option']))
	  { if(isset($data['option']['var']))
	    $data['option']=array($data['option']);
	    foreach($data['option'] as $option)
	    { $option['item']=$section;
	      $option['name']=$option['name'];
		  if(!empty($option['value']))
		  $option['value']=$option['value'];
		  if(!empty($option['options']))
		  { $options=array();
		    foreach($option['options'] as $var=>$value)
		    $options[str_replace("_"," ",$var)]=$value;
		    $option['options']=serialize($options);
		  }
		  if(!empty($option['value']) && $option['type']=='mailtpl' || $option['type']=='othertpl')
		  $option['value']=preg_replace("/^[a-zA-Z0-9]+_/i",$_REQUEST['name'].'_',$option['value']);
	      A::$DB->Insert("mysite_options",$option);
	    }
	  }

	  if(is_dir("modules/{$_REQUEST['module']}/templates/default"))
	  { $files=scandir("modules/{$_REQUEST['module']}/templates/default");
	    foreach($files as $file)
	    if(is_file("modules/{$_REQUEST['module']}/templates/default/$file"))
	    { if($_REQUEST['module']!='pages' && preg_match("/^".preg_quote($_REQUEST['module'])."_/i",$file))
	      { $_file=preg_replace("/^".preg_quote($_REQUEST['module'])."_/i",$_REQUEST['name'].'_',$file);
	        $fc=false;
			if($sections=getSectionsByModule($_REQUEST['module']))
	        foreach($sections as $_section)
	        { $__file=preg_replace("/^".preg_quote($_REQUEST['module'])."_/i",getName($_section).'_',$file);
	          if($fc=copyfile("templates/mysite/$__file","templates/mysite/$_file"))
	          break;
	        }
	        if(!$fc)
		    copyfile("modules/{$_REQUEST['module']}/templates/default/$file","templates/mysite/$_file");
	      }
	      else
	      copyfile("modules/{$_REQUEST['module']}/templates/default/$file","templates/mysite/$file");
	    }
	  }
	  if(is_dir("modules/{$_REQUEST['module']}/templates/default/mails"))
	  { $files=scandir("modules/{$_REQUEST['module']}/templates/default/mails");
	    foreach($files as $file)
	    if(is_file("modules/{$_REQUEST['module']}/templates/default/mails/$file"))
	    { if(preg_match("/^".preg_quote($_REQUEST['module'])."_/i",$file))
	      { $_file=preg_replace("/^".preg_quote($_REQUEST['module'])."_/i",$_REQUEST['name'].'_',$file);
		    $fc=false;
			if($sections=getSectionsByModule($_REQUEST['module']))
	        foreach($sections as $_section)
	        { $__file=preg_replace("/^".preg_quote($_REQUEST['module'])."_/i",getName($_section).'_',$file);
	          if($fc=copyfile("templates/mysite/mails/$__file","templates/mysite/mails/$_file"))
	          break;
	        }
	        if(!$fc)
			copyfile("modules/{$_REQUEST['module']}/templates/default/mails/$file","templates/mysite/mails/$_file");
	      }
	      else
	      copyfile("modules/{$_REQUEST['module']}/templates/default/mails/$file","templates/mysite/mails/$file");
	    }
	  }
	  copyDir("modules/".$_REQUEST['module']."/templates/default/others","templates/mysite/others");

	  A::$REGFILES=$section;
	  if($idimg=UploadImage("image",$_REQUEST['caption']))
	  A::$DB->Update("mysite_sections",array('idimg'=>$idimg),"id=".(integer)$_REQUEST['id']);

	  if(file_exists("modules/".$_REQUEST['module']."/include.php"))
	  require_once("modules/".$_REQUEST['module']."/include.php");

	  A::$OBSERVER->Event('CreateSection',$section,$_REQUEST);

	  return true;
    }
    return false;
  }

  function Edit()
  {
    $_REQUEST['name']=substr(strtolower(preg_replace("/[^a-zA-Z0-9]+/i","",$_REQUEST['name'])),0,20);
    $_REQUEST['lang']='ru';
    $_REQUEST['caption']=strip_tags(trim($_REQUEST['caption']));

	$f1=empty($_REQUEST['name']) || $_REQUEST['name']=="admin" || $_REQUEST['name']=="getfile";
	$f2=A::$DB->existsRow("SELECT * FROM mysite_sections WHERE name=? AND id<>".(integer)$_REQUEST['id'],$_REQUEST['name']);
	if($f1 || $f2)
	{ $this->errors['doubleid']=true;
	  return false;
	}

    $_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['icon']=isset($_REQUEST['icon'])?'Y':'N';
	$_REQUEST['menu']=isset($_REQUEST['menu'])?'Y':'N';
	if(A::$OPTIONS['transurl'])
	$_REQUEST['urlname']=$_REQUEST['name'];
	else
	$_REQUEST['urlname']=getURLName($_REQUEST['caption'],$_REQUEST['urlname'],"mysite_sections","id<>".(integer)$_REQUEST['id']);

    $dataset = new A_DataSet("mysite_sections");
    $dataset->fields=array("name","urlname","caption","active","icon","menu","lang");

	$_REQUEST["caption_ru"]=strip_tags(trim($_REQUEST["caption_ru"]));
	$_REQUEST["title_ru"]=strip_tags(trim($_REQUEST["title_ru"]));
	array_push($dataset->fields,"caption_ru","title_ru");

    if($row=$dataset->Update())
    {
      $section="mysite_ru_".$_REQUEST['name'];

	  if($_REQUEST['name']!=$row['name'])
      {
		$oldsection="mysite_ru_".$row['name'];

		if(A::$OPTIONS['mainsection']==$row['name'])
 	    A::$DB->execute("UPDATE mysite_options SET value='".$_REQUEST['name']."' WHERE item='' AND var='mainsection'");

		$tables=A::$DB->getTables();

		foreach($tables as $table)
		if(preg_match("/^".$oldsection."/i",$table))
		{ $newtable=preg_replace("/^".$oldsection."/i",$section,$table);
		  A::$DB->execute("ALTER TABLE `$table` RENAME `$newtable`");
		}

        A::$DB->execute("UPDATE mysite_blocks SET item='$section' WHERE item='$oldsection'");
		A::$DB->execute("UPDATE mysite_options SET item='$section' WHERE item='$oldsection'");
		A::$DB->execute("UPDATE mysite_fields SET item='$section' WHERE item='$oldsection'");
      }

      A::$REGFILES=$section;
      if(isset($_REQUEST['imagedel']))
	  { DelRegImage($row['idimg']);
	    A::$DB->Update("mysite_sections",array('idimg'=>0),"id=".$row['id']);
	  }
	  elseif($idimg=UploadImage("image",$_REQUEST['caption'],$row['idimg']))
	  A::$DB->Update("mysite_sections",array('idimg'=>$idimg),"id=".$row['id']);

	  A::$OBSERVER->Event('UpdateSection',"mysite_ru_".$row['name'],$_REQUEST);

	  return true;
    }
    return false;
  }

  function Del($id=0)
  {
    if($id>0)
	$_REQUEST['id']=$id;

    $dataset = new A_DataSet("mysite_sections");
    if($row=$dataset->Delete())
    {
	  $section="mysite_ru_".$row['name'];

      $tables=A::$DB->getTables();
	  foreach($tables as $table)
	  if(preg_match("/^".$section."/i",$table))
	  A::$DB->execute("DROP TABLE `$table`");

	  if(A::$OPTIONS['mainsection']==$row['name'])
	  A::$DB->execute("UPDATE mysite_options SET value='' WHERE item='' AND var='mainsection'");
	  A::$DB->execute("DELETE FROM mysite_options WHERE item='$section'");
	  A::$DB->execute("DELETE FROM mysite_blocks WHERE item='$section'");
	  A::$DB->execute("DELETE FROM mysite_fields WHERE item='$section'");
	  A::$DB->execute("DELETE FROM mysite_templates WHERE idsec=".$row['id']);

	  A::$REGFILES=$section;

	  DelRegSectionImages($row['id']);
	  DelRegSectionFiles($row['id']);

	  A::$OBSERVER->Event('DeleteSection',$section,$row);

	  return true;
    }
    else
    return false;
  }

  function Save()
  {
	setOption('','mainsection',$_REQUEST['value']);

    return true;
  }

  function SetActive()
  {
    if(isset($_REQUEST['checksection']))
	{ foreach($_REQUEST['checksection'] as $id)
	  A::$DB->execute("UPDATE mysite_sections SET active='Y' WHERE id=".(integer)$id);
    }
	return true;
  }

  function SetUnActive()
  {
    if(isset($_REQUEST['checksection']))
	{ foreach($_REQUEST['checksection'] as $id)
	  A::$DB->execute("UPDATE mysite_sections SET active='N' WHERE id=".(integer)$id);
	}
	return true;
  }

  function Delete()
  {
    if(isset($_REQUEST['checksection']))
	foreach($_REQUEST['checksection'] as $id)
	$this->Del($id);
	return true;
  }

  function createData()
  {
	$this->Assign("caption","Разделы");

	$this->Assign("langcount",1);
	$this->AddJScript("var languages = new Array('ru');","code");

	$sections=array();
    A::$DB->query("SELECT * FROM mysite_sections ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $section="mysite_ru_".$row['name'];
	  $row['section']=$section;
	  $row['link']=getSectionLink($section);
	  $ico="modules/".$row['module']."/ico.gif";
	  $row['ico']=file_exists($ico)?AddImage('/'.$ico,16,16,$row['caption']):AddImage("/templates/admin/images/icons/main_sections.gif",16,16);
	  $row['modcaption']=A::$DB->getOne("SELECT caption FROM _extensions WHERE type='module' AND name=?",$row['module']);
	  $sections[]=$row;
    }

	$this->Assign("sections",$sections);

	$optionsform=array();
	A::$DB->query("SELECT * FROM mysite_sections ORDER BY sort");
    while($row=A::$DB->fetchRow())
	if(isset($optionsform['sections'][$row['name']]))
	$optionsform['sections'][$row['name']].=" / ".$row['caption'];
	else
    $optionsform['sections'][$row['name']]=$row['caption'];
	A::$DB->free();

	$this->Assign("optionsform",$optionsform);

	if(!empty($optionsform['sections']) && empty(A::$OPTIONS['mainsection']))
	{ setOption('','mainsection',key($optionsform['sections']));
	  A::goUrl("admin.php?mode=site&item=sections");
	}
  }
}

A::$MAINFRAME = new SiteSections;