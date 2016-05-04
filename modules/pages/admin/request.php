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
 * Серверная сторона AJAX панели управления модуля "Страницы".
 *
 * <a href="http://wiki.a-cms.ru/modules/pages">Руководство</a>.
 */

class PagesModule_Request extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "indir": $this->InDir(); break;
	  case "delitem": $this->DelItem(); break;
	  case "getadddirform": $this->getAddDirForm(); break;
	  case "geteditdirform": $this->getEditDirForm(); break;
      case "getaddpageform"; $this->getAddPageForm(); break;
      case "geteditpageform"; $this->getEditPageForm(); break;
	  case "getmoveform"; $this->getMoveForm(); break;
	  case "getmovepagesform": $this->getMovePagesForm(); break;
	  case "getgrid": $this->getGrid(); break;
	  case "applypage": $this->applyPage(); break;
	  case "setsort": $this->setSort(); break;
    }
  }

/**
 * Обработчик действия: Переход в подраздел.
 */

  function InDir()
  {
    A_Session::set(SECTION."_cid",(integer)$_POST['id']);
	$this->getGrid();
  }

/**
 * Удаление страницы или подраздела.
 *
 * @param integer $id Идентификатор элемента.
 */

  function DelItemBranch($id)
  {
    A::$DB->query("SELECT id FROM ".SECTION." WHERE idker=$id");
    while($row=A::$DB->fetchRow())
    $this->DelItemBranch($row['id']);
	A::$DB->free();
    A::$DB->execute("DELETE FROM ".SECTION." WHERE id=$id");

	A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$id));
  }

/**
 * Обработчик действия: Удаление страницы или подраздела.
 */

  function DelItem()
  {
    $this->DelItemBranch((integer)$_POST['id']);
	$this->getGrid();
  }

/**
 * Обработчик действия: Отдает форму добавления подраздела.
 */

  function getAddDirForm()
  {
	$form = new A_Form("module_pages_adddir.tpl");
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования подраздела.
 */

  function getEditDirForm()
  {
	$form = new A_Form("module_pages_editdir.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION);
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму добавления страницы.
 */

  function getAddPageForm()
  {
	$form = new A_Form("module_pages_addpage.tpl");
    $form->data['usemap']=getSectionByModule('sitemap');
	$form->fieldseditor_addprepare();
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования страницы.
 */

  function getEditPageForm()
  {
	$form = new A_Form("module_pages_editpage.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION);
    $form->data['usemap']=getSectionByModule('sitemap');
    $form->fieldseditor_editprepare();
	$this->RESULT['html'] = $form->getContent();
  }

  function getDirs(&$values,$id,$noid)
  {
    A::$DB->query("
	SELECT id,idker,name,level,sort
	FROM ".SECTION." WHERE idker={$id} AND type='dir' AND id<>{$noid}
	ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $row['level_sort']=sprintf("%03d_%03d",$row['level'],$row['sort']);
	  $values[]=$row;
      $this->getDirs($values,$row['id'],$noid);

	}
	A::$DB->free();
  }

/**
 * Обработчик действия: Отдает форму перемещения элемента.
 */

  function getMoveForm()
  {
	$form = new A_Form("module_pages_move.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION);
	$form->data['dirs']=array();
	$this->getDirs($form->data['dirs'],0,$form->data['id']);
	$form->data['dirs']=array_multisort_key($form->data['dirs'],"level_sort");
	$this->RESULT['title']="Перемещение элемента &laquo;".$form->data['name']."&raquo;";
	if(count($form->data['dirs'])>0)
	$this->RESULT['html']=$form->getContent();
	else
	$this->RESULT['html']=AddLabel("Нет вариантов перемещения.");
  }

/**
 * Обработчик действия: Отдает форму пермещения элементов.
 */

  function getMovePagesForm()
  {
    if(empty($_POST['pages'])) return;
    $form = new A_Form("module_pages_cmove.tpl");
	$form->data['pages']=array_values($_POST['pages']);
	$form->data['idker']=A::$DB->getOne("SELECT idker FROM ".SECTION." WHERE id=".(integer)current($form->data['pages']));
	$form->data['dirs']=array();
	$cid=(integer)A_Session::get(SECTION."_cid",0);
	$this->getDirs($form->data['dirs'],0,$cid);
	$form->data['dirs']=array_multisort_key($form->data['dirs'],"level_sort");
	if($cid>0 && count($form->data['dirs'])==0)
	{ $row=A::$DB->getRow("SELECT id,idker,name,level,sort FROM ".SECTION." WHERE id=$cid");
	  $row['level_sort']=sprintf("%03d_%03d",$row['level'],$row['sort']);
	  $form->data['dirs'][]=$row;
	}
	if(count($form->data['dirs'])>0)
	$this->RESULT['html']=$form->getContent();
	else
	$this->RESULT['html']=AddLabel("Нет вариантов перемещения.");
  }

/**
 * Обработчик действия: Сохранение страницы.
 */

  function applyPage()
  {
    $this->RESULT['result']=false;

	$row=A::$DB->getRowById($_REQUEST['id'],SECTION);
	if(!$row)
	return false;

	$_REQUEST['date']=time();
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION,"type='page' AND idker={$row['idker']} AND id<>".$row['id']);
	$_REQUEST['keywords']=getkeywords($_REQUEST['content']);
	$_REQUEST['description']=truncate($_REQUEST['content'],350);
	$_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';
	$_REQUEST['inmap']=isset($_REQUEST['inmap'])?'Y':'N';

	$dataset = new A_DataSet(SECTION,true);
    $dataset->fields=array("date","name","urlname","keywords","description","content","tags","active","inmap");
	$_REQUEST["template"]=preg_replace("/[^a-zA-Z0-9._-]+/i","",$_REQUEST["template"]);
	$dataset->fields[]='template';

    if($row=$dataset->Update())
	{
	  $name=getTreePath(SECTION,$row['level']==0||$row['urlname']!='index'?$row['id']:$row['idker']," - ");
	  A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$row['id'],'name'=>$name,'content'=>$_REQUEST['content'],'tags'=>$_REQUEST['tags']));

	  if(!empty($_REQUEST["template"]))
	  copyfile("modules/pages/templates/default/pages_page.tpl","templates/".DOMAIN."/".$_REQUEST["template"]);

	  while($row['idker'])
	  if($row=A::$DB->getRowById($row['idker'],SECTION))
	  A::$DB->Update(SECTION,array('date'=>$_REQUEST['date']),"id=".$row['id']);

	  $this->RESULT['date']=date("d.m.Y H:i",$_REQUEST['date'])."&nbsp;&nbsp;";
	  $this->RESULT['result']=true;

	  return true;
	}
  }

  function getPath($id)
  {
	if($row=A::$DB->getRow("SELECT idker,urlname FROM ".SECTION." WHERE id=$id"))
    return $this->getPath($row['idker']).$row['urlname']."/";
    else
	{ $link=DOMAINNAME==preg_replace("/^www\./i","",HOSTNAME)?DOMAINNAME.getSectionLink(SECTION):getSectionLink(SECTION);
	  return preg_replace("/^http\:\/\//i","",$link);
	}
  }

/**
 * Обработчик действия: Отдает таблицу с элементами текущего уровня.
 */

  function getGrid()
  {
	$form = new A_Form("module_pages_grid.tpl");
	$curdir=(integer)A_Session::get(SECTION."_cid",0);
	$rows=(integer)A_Session::get(SECTION."_rows",isset($_COOKIE[SECTION.'_rows'])?$_COOKIE[SECTION.'_rows']:20);
	$form->data['seo']=getStructureByPlugin('seo');
	$form->data['title']=$this->getPath($curdir);
	$form->data['pages']=array();
	$pager = new A_Pager($rows,"gopage");
	$pager->query("SELECT * FROM ".SECTION." WHERE idker={$curdir} ORDER BY sort");
    if($curdir>0 && $row=A::$DB->getRowById($curdir,SECTION))
	{ $grow[0]="&nbsp;";
	  $grow[1]=AddImageButton("/templates/admin/images/back.gif","indir({$row['idker']})","Уровень выше",16,16);
	  $grow[2]=AddClickText("...","indir({$row['idker']})");
	  $grow[3]=$grow[5]=$grow[6]=$grow[7]=$grow[8]="&nbsp;";
	  $grow[4]=0;
	  $form->data['sub']=true;
	  $form->data['pages'][]=$grow;
	}
	else
	$form->data['sub']=false;
	$crows=0;
	while($row=$pager->fetchRow())
	{ $grow[0]="<input type=\"checkbox\" id=\"checkp{$crows}\" name=\"checkpages[]\" value=\"{$row['id']}\"/>";
	  $grow['link']=pages_createItemLink($row['id'],SECTION);
	  if($row['type']=='dir')
	  { $grow[1]=AddImage("/templates/admin/images/dir.gif",16,16);
	    $grow[2]=AddLink(truncate($row['name'],100),"javascript:indir({$row['id']})","Войти в подраздел");
		$grow[3]="&nbsp;";
	    $grow[4]=$row['date'];
		$grow[5]=AddImageButton("/templates/admin/images/edit.gif","geteditdirform({$row['id']})","Редактировать",16,16);
		$grow[6]=AddImageButtonLink("/templates/admin/images/browse.gif",$grow['link'],"Просмотр на сайте",16,16,' target="_blank"');
	  }
	  else
	  { $grow[1]=AddImage("/templates/admin/images/text.gif",16,16);
		$grow[2]=AddLink(truncate($row['name'],100),"javascript:geteditpageform({$row['id']})","Редактировать");
		$grow[3]="<a href=\"javascript:edittpl('{$row['template']}')\" title=\"Редактировать шаблон\">{$row['template']}</a>";
		$grow[4]=$row['date'];
		$grow[5]="&nbsp;";
		$grow[6]=AddImageButtonLink("/templates/admin/images/browse.gif",$grow['link'],"Просмотр на сайте",16,16,' target="_blank"');
	  }
      $grow[7]=AddImageButton("/templates/admin/images/move.gif","getmoveform({$row['id']})","Переместить",16,16);
	  $grow[8]=AddImageButton("/templates/admin/images/del.gif","delitem({$row['id']},'{$row['urlname']}')","Удалить",16,16);
	  $grow['id']=$row['id'];
	  $grow['active']=$row['active'];
	  $form->data['pages'][]=$grow;
	  $crows++;
    }
	$pager->free();
	$form->data['pager']=$pager;
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Сортировка.
 */

  function setSort()
  {
    $sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
    $page=!empty($_POST['page'])?(integer)$_POST['page']:0;
    $rows=(integer)A_Session::get(SECTION."_rows",isset($_COOKIE[SECTION.'_rows'])?$_COOKIE[SECTION.'_rows']:20);
	$i=$page*$rows+1;
	foreach($sort as $id)
	A::$DB->Update(SECTION,array('sort'=>$i++),"id=".(integer)$id);
  }
}

A::$REQUEST = new PagesModule_Request;