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
 * Серверная сторона AJAX для компонента "редактор дополнительных полей".
 */

class A_FieldsEditorRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
	{ case "getaddfieldform": $this->getAddForm(); break;
	  case "geteditfieldform": $this->getEditForm(); break;
	  case "setsort": $this->setSort(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму добавления поля.
 */

  function getAddForm()
  {
    $item=MODE=='sections'?SECTION:STRUCTURE;
    $form = new A_Form("objcomp_fieldseditor_add.tpl");
	$form->data['vars']=array();
	$form->data['usefill']=$_POST['usefill'];
	$form->data['usesearch']=$_POST['usesearch'];
	$form->data['usenofront']=$_POST['usenofront'];
	$form->data['languages']=array(array("name"=>"name_ru"));
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования поля.
 */

  function getEditForm()
  {
    $item=MODE=='sections'?SECTION:STRUCTURE;

    $form = new A_Form("objcomp_fieldseditor_edit.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],"mysite_fields");
	$form->data['vars']=array();
	$form->data['usefill']=$_POST['usefill'];
	$form->data['usesearch']=$_POST['usesearch'];
	$form->data['usenofront']=$_POST['usenofront'];
	$form->data['languages']=array(array("name"=>"name_ru","text"=>$form->data["name_ru"]));
	$this->RESULT['html']=$form->getContent();
  }

  function setSort()
  {
    $sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
	$i=1;
	foreach($sort as $id)
	A::$DB->Update("mysite_fields",array('sort'=>$i++),"id=".(integer)$id);
  }
}

A::$REQUEST = new A_FieldsEditorRequest;