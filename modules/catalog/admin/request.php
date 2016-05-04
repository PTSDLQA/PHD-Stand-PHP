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
 * Серверная сторона AJAX панели управления модуля "Каталог материалов".
 *
 * <a href="http://wiki.a-cms.ru/modules/catalog">Руководство</a>.
 */

class CatalogModule_Request extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "getadditemform": $this->getAddForm(); break;
	  case "getedititemform": $this->getEditForm(); break;
	  case "getmoveitemsform": $this->getMoveItemsForm(); break;
	  case "getfilterform": $this->getFilterForm(); break;
	  case "applyitem": $this->applyItem(); break;
    }
  }

/**
 * Обработчик действия: Отдает форму добавления записи.
 */

  function getAddForm()
  {
    $form = new A_Form("module_catalog_add.tpl");
	$form->data['idcat']=(integer)$_POST['idcat'];
	$form->data['categories']=A::$DB->getAll("SELECT id,idker,name FROM ".SECTION."_categories ORDER BY level,sort");
	$form->fieldseditor_addprepare();
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования записи.
 */

  function getEditForm()
  {
    $form = new A_Form("module_catalog_edit.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION."_catalog");
	$form->data['categories']=A::$DB->getAll("SELECT id,idker,name FROM ".SECTION."_categories ORDER BY level,sort");
	$form->fieldseditor_editprepare();
	$form->data['filesbox'] = new A_Files((integer)$_POST['id']);
	$form->data['imagesbox'] = new A_Images((integer)$_POST['id']);
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму фильтров.
 */

  function getFilterForm()
  {
    $data=A_Session::get(SECTION."_filter",array());
    $form = new A_Form("module_catalog_filter.tpl");
	$form->data['idcat']=$_POST['idcat'];
	$form->data['name']=$data['name'];
	$form->data['content']=$data['content'];
	$form->data['date']=$data['date'];
	$form->data['from']=$data['date1'];
	$form->data['to']=$data['date2'];
	$form->data['statuss']=array(0=>"Не выбрано","Y"=>"Активные","N"=>"Неактивные");
	$form->data['status']=$data['status'];
	$form->fieldseditor_filterprepare($data);
	$frame = new A_Frame("default_form.tpl","Фильтр",$form);
	$this->RESULT['html'] = $frame->getContent();
  }

/**
 * Обработчик действия: Отдает форму перемещения записей.
 */

  function getMoveItemsForm()
  {
    if(empty($_POST['items'])) return;
    $form = new A_Form("module_catalog_move.tpl");
	$form->data['idcat']=$_POST['idcat'];
	$form->data['items']=array_values($_POST['items']);
	$form->data['categories']=A::$DB->getAll("SELECT id,idker,name FROM ".SECTION."_categories ORDER BY level,sort");
	if(count($form->data['categories'])>0)
	$this->RESULT['html']=$form->getContent();
	else
	$this->RESULT['html']=AddLabel("Нет вариантов перемещения.");
  }

  function applyItem()
  {
    $this->RESULT['result']=false;

	$row=A::$DB->getRowById($_REQUEST['id'],SECTION."_catalog");
	if(!$row)
	return false;

	require_once("modules/catalog/admin/catalog.php");

	if(CatalogModule_Admin::EditItem())
    { $this->RESULT['date']=date("d.m.Y H:i",$_REQUEST['mdate']);
	  $this->RESULT['result']=true;
	}
  }
}

A::$REQUEST = new CatalogModule_Request;