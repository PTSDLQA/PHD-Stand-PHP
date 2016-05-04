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
 * Серверная сторона AJAX панели управления модуля "Фотогалерея".
 *
 * <a href="http://wiki.a-cms.ru/modules/gallery">Руководство</a>.
 */

class GalleryModule_Request extends A_Request
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
	  case "getaddimageform": $this->getAddImageForm(); break;
	  case "setsort": $this->setSort(); break;
    }
  }

/**
 * Обработчик действия: Отдает форму добавления альбома.
 */

  function getAddForm()
  {
    $form = new A_Form("module_gallery_add.tpl");
	$form->data['idcat']=(integer)$_POST['idcat'];
	$form->data['categories']=A::$DB->getAll("SELECT id,idker,name FROM ".SECTION."_categories ORDER BY level,sort");
	$form->fieldseditor_addprepare();
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования альбома.
 */

  function getEditForm()
  {
    $form = new A_Form("module_gallery_edit.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION."_albums");
	$form->data['categories']=A::$DB->getAll("SELECT id,idker,name FROM ".SECTION."_categories ORDER BY level,sort");
	$form->fieldseditor_editprepare();
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму перемещения альбомов.
 */

  function getMoveItemsForm()
  {
    if(empty($_POST['items'])) return;
    $form = new A_Form("module_gallery_move.tpl");
	$form->data['idcat']=$_POST['idcat'];
	$form->data['items']=array_values($_POST['items']);
	$form->data['categories']=A::$DB->getAll("SELECT id,idker,name FROM ".SECTION."_categories ORDER BY level,sort");
	if(count($form->data['categories'])>0)
	$this->RESULT['html']=$form->getContent();
	else
	$this->RESULT['html']=AddLabel("Нет вариантов перемещения.");
  }

/**
 * Обработчик действия: Отдает форму добавления фото.
 */

  function getAddImageForm()
  {
    $form = new A_Form("module_gallery_addimage.tpl");
	$form->data['idalb']=(integer)$_POST['idalb'];
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Сортировка.
 */

  function setSort()
  {
    $sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
	$i=1;
	foreach($sort as $id)
	A::$DB->Update(DOMAIN."_images",array('sort'=>$i++),"id=".(integer)$id);
  }
}

A::$REQUEST = new GalleryModule_Request;