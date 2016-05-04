<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Modules
 */
/**************************************************************************/

/**
 * Панель управления модуля "Страницы".
 *
 * <a href="http://wiki.a-cms.ru/modules/pages">Руководство</a>.
 */

class PagesModule_Admin extends A_MainFrame
{
/**
 * Конструктор.
 */

  function __construct()
  {
    parent::__construct("module_pages.tpl");

	$this->AddJScript("/modules/pages/admin/pages.js");
	$this->AddJScript("/system/objcomp/jscripts/tpleditor.js");
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    $res=false;
    switch($action)
    { case "adddir": $res=$this->AddDir(); break;
	  case "addpage": $res=$this->AddPage(); break;
	  case "editdir": $res=$this->EditDir(); break;
	  case "editpage": $res=$this->EditPage(); break;
	  case "del": $res=$this->Del(); break;
	  case "delete": $res=$this->Delete(); break;
	  case "setrows": $res=$this->SetRows(); break;
	  case "setactive": $res=$this->SetActive(); break;
	  case "setunactive": $res=$this->SetUnActive(); break;
	  case "move": $res=$this->Move(); break;
	  case "cmove": $res=$this->CMove(); break;
    }
	if($res)
	A::goUrl("admin.php?mode=sections&item=".SECTION,array('page'));
  }

/**
 * Обработчик действия: Добавление подраздела.
 */

  function AddDir()
  {
	$curdir = (integer)empty($_REQUEST['parent_id'])?A_Session::get(SECTION."_cid",0):$_REQUEST['parent_id'];

	if($row=A::$DB->getRowById($curdir,SECTION))
    { $_REQUEST['idker']=$curdir;
      $_REQUEST['level']=$row['level']+1;
    }
    else
    { $_REQUEST['idker']=0;
      $_REQUEST['level']=0;
    }
	$_REQUEST['date']=time();
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION,"type='dir' AND idker=".(integer)$_REQUEST['idker']);
	$_REQUEST['type']="dir";
    $_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".SECTION." WHERE idker=".(integer)$_REQUEST['idker'])+1;
    $_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['inmap']=isset($_REQUEST['inmap'])?'Y':'N';

    $dataset = new A_DataSet(SECTION);
    $dataset->fields=array("date","idker","level","urlname","name","sort","type","active","inmap");

    if($id=$dataset->Insert())
	{
	  $_REQUEST['parent_id']=$id;
	  $_REQUEST['urlname']="index";
	  $_REQUEST['tags']="";
	  $_REQUEST['title']="";
	  $_REQUEST['template']=A::$OPTIONS['tpldefault'];
	  $_REQUEST['content']=!empty($_REQUEST['content'])?$_REQUEST['content']:"";

	  return $this->AddPage();
	}
	else
	return false;
  }

/**
 * Обработчик действия: Изменение подраздела.
 */

  function EditDir()
  {
	$row=A::$DB->getRowById($_REQUEST['id'],SECTION);
	if(!$row) return false;

	$_REQUEST['date']=time();
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION,"type='dir' AND idker={$row['idker']} AND id<>".$row['id']);
	$_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['inmap']=isset($_REQUEST['inmap'])?'Y':'N';

    $dataset = new A_DataSet(SECTION);
    $dataset->fields=array("date","name","urlname","active","inmap");
    return $dataset->Update();
  }

/**
 * Обработчик действия: Добавление страницы.
 */

  function AddPage()
  {
	$curdir = (integer)empty($_REQUEST['parent_id'])?A_Session::get(SECTION."_cid",0):$_REQUEST['parent_id'];

    if($row=A::$DB->getRowById($curdir,SECTION))
    { $_REQUEST['idker']=$curdir;
      $_REQUEST['level']=$row['level']+1;
    }
    else
    { $_REQUEST['idker']=0;
      $_REQUEST['level']=0;
    }
	$_REQUEST['date']=time();
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION,"type='page' AND idker=".(integer)$_REQUEST['idker']);
    $_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".SECTION." WHERE idker=".(integer)$_REQUEST['idker'])+1;
    $_REQUEST['type']='page';
	$_REQUEST["template"]=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST["template"]);
	$_REQUEST['keywords']=getkeywords($_REQUEST['content']);
	$_REQUEST['description']=truncate($_REQUEST['content'],350);
	$_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['inmap']=isset($_REQUEST['inmap'])?'Y':'N';

	$dataset = new A_DataSet(SECTION,true);
    $dataset->fields=array("date","idker","level","urlname","name","tags","keywords","description","content","type","sort","template","active","inmap");

	if($id=$dataset->Insert())
	{
	  $name=getTreePath(SECTION,$_REQUEST['level']==0||$_REQUEST['urlname']!='index'?$id:$_REQUEST['idker']," - ");

	  if($_REQUEST['active']=='Y')
	  A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$id,'name'=>$name,'content'=>$_REQUEST['content'],'tags'=>$_REQUEST['tags']));

	  if(!empty($_REQUEST["template"]))
	  copyfile("modules/pages/templates/default/pages_page.tpl","templates/".DOMAIN."/".$_REQUEST["template"]);

	  return $id;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Изменение страницы.
 */

  function EditPage()
  {
	$row=A::$DB->getRowById($_REQUEST['id'],SECTION);
	if(!$row) return false;

	$_REQUEST['date']=time();
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['urlname']=getUrlName($_REQUEST['name'],$_REQUEST['urlname'],SECTION,"type='page' AND idker={$row['idker']} AND id<>".$row['id']);
	$_REQUEST['keywords']=getkeywords($_REQUEST['content']);
	$_REQUEST['description']=truncate($_REQUEST['content'],350);
	$_REQUEST["template"]=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST["template"]);
	$_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['inmap']=isset($_REQUEST['inmap'])?'Y':'N';

	$dataset = new A_DataSet(SECTION,true);
    $dataset->fields=array("date","name","urlname","keywords","description","content","tags","template","active","inmap");

    if($row=$dataset->Update())
	{
	  $name=getTreePath(SECTION,$row['level']==0||$row['urlname']!='index'?$row['id']:$row['idker']," - ");

	  if($_REQUEST['active']=='Y')
	  A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$row['id'],'name'=>$name,'content'=>$_REQUEST['content'],'tags'=>$_REQUEST['tags']));
	  else
	  A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$row['id']));

	  if(!empty($_REQUEST["template"]))
	  copyfile("modules/pages/templates/default/pages_page.tpl","templates/".DOMAIN."/".$_REQUEST["template"]);

	  while($row['idker'])
	  if($row=A::$DB->getRowById($row['idker'],SECTION))
	  A::$DB->Update(SECTION,array('date'=>$_REQUEST['date']),"id=".$row['id']);

	  return true;
	}
	else
	return false;
  }

/**
 * Удаление страницы или подраздела.
 *
 * @param integer $id Идентификатор страницы или подраздела.
 */


  function DelBranch($id)
  {
    A::$DB->query("SELECT id FROM ".SECTION." WHERE idker=".(integer)$id);
    while($row=A::$DB->fetchRow())
    $this->DelBranch($row['id']);
	A::$DB->free();
    A::$DB->execute("DELETE FROM ".SECTION." WHERE id=".(integer)$id);

	A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$id));
  }

/**
 * Обработчик действия: Удаление страницы или подраздела.
 */

  function Del($id=0)
  {
	if($id>0) $_REQUEST['id']=$id;

	$this->DelBranch((integer)$_REQUEST['id']);
	return true;
  }

/**
 * Обработчик действия: Удаление группы элементов.
 */

  function Delete()
  {
    if(!empty($_REQUEST['checkpages']))
	foreach($_REQUEST['checkpages'] as $id)
	$this->Del((integer)$id);
	return true;
  }

/**
 * Обработчик действия: Количество строк на странице.
 */

  function SetRows()
  {
	A_Session::set(SECTION."_rows",$_REQUEST['rows']);
	setcookie(SECTION."_rows",$_REQUEST['rows'],time()+31104000);
	return true;
  }

/**
 * Обработчик действия: Включение группы страниц или подразделов.
 */

  function SetActive()
  {
    if(isset($_REQUEST['checkpages']))
	foreach($_REQUEST['checkpages'] as $id)
	A::$DB->execute("UPDATE ".SECTION." SET active='Y' WHERE id=".(integer)$id);
	return true;
  }

/**
 * Обработчик действия: Выключение группы страниц или подразделов.
 */

  function SetUnActive()
  {
    if(isset($_REQUEST['checkpages']))
	foreach($_REQUEST['checkpages'] as $id)
	{ A::$DB->execute("UPDATE ".SECTION." SET active='N' WHERE id=".(integer)$id);
	  A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$id));
	}
	return true;
  }

/**
 * Обновление информации об уровнях элементов.
 *
 * @param integer $id Идентификатор элемента.
 * @param integer $level Уровень элемента.
 */

  function ReLevels($id,$level)
  {
    A::$DB->query("SELECT id FROM ".SECTION." WHERE idker=".(integer)$id);
    while($row=A::$DB->fetchRow())
    { A::$DB->execute("UPDATE ".SECTION." SET level=$level WHERE id=".$row['id']);
	  $this->ReLevels($row['id'],$level+1);
	}
	A::$DB->free();
  }

/**
 * Обработчик действия: Перемещение страницы или подраздела.
 *
 * @param integer $id=0 Идентификатор элемента.
 */

  function Move($id=0)
  {
	if($id>0) $_REQUEST['id']=$id;
	if(empty($_REQUEST['id'])) return false;

    $row=A::$DB->getRowById($_REQUEST['id'],SECTION);
	if(!$row || $_REQUEST['id']==$_REQUEST['idto']) return false;

    $update=array();
	if(!empty($_REQUEST['idto']))
	$update['level']=A::$DB->getOne("SELECT level FROM ".SECTION." WHERE id=".(integer)$_REQUEST['idto'])+1;
	else
	$update['level']=0;
	$update['idker']=(integer)$_REQUEST['idto'];
	$update['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".SECTION." WHERE idker=".(integer)$_REQUEST['idto'])+1;
	$update['urlname']=getURLName($row['name'],$row['urlname'],SECTION,"idker=".(integer)$_REQUEST['idto']);
	A::$DB->Update(SECTION,$update,"id=".$row['id']);

	$this->ReLevels($row['id'],$update['level']+1);

	$name=getTreePath(SECTION,$row['level']==0||$row['urlname']!='index'?$row['id']:$row['idker']," - ");
	if($row['active']=='Y')
	A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$row['id'],'name'=>$name,'content'=>$row['content'],'tags'=>$row['tags']));

	return true;
  }

/**
 * Обработчик действия: Перемещение группы страниц или подразделов.
 */

  function CMove()
  {
    if(!empty($_REQUEST['checkpages']))
	foreach($_REQUEST['checkpages'] as $id)
	$this->Move((integer)$id);
	return true;
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
    $this->Assign("curdir",(integer)A_Session::get(SECTION."_cid",0));

    if(!empty($_GET['page']))
	$this->AddJVar("cur_page",(integer)$_GET['page']);

	$this->Assign("fieldsbox",new A_FieldsEditor(SECTION,array('tab'=>'opt','tab_opt'=>'fields'),false,false));

	$this->Assign("optbox",new A_OptionsBox("",array("idgroup"=>1)));

	$this->Assign("rows",(integer)A_Session::get(SECTION."_rows",isset($_COOKIE[SECTION.'_rows'])?$_COOKIE[SECTION.'_rows']:20));
  }
}

A::$MAINFRAME = new PagesModule_Admin;