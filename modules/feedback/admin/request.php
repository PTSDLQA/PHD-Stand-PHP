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
 * Серверная сторона AJAX панели управления модуля "Обратная связь".
 *
 * <a href="http://wiki.a-cms.ru/modules/feedback">Руководство</a>.
 */

class FeedbackModule_Request extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
	{ case "geteditpageform": $this->getPageForm(); break;
	  case "getaddfieldform": $this->getAddForm(); break;
	  case "geteditfieldform": $this->getEditForm(); break;
	  case "getmessageform": $this->getmessageform(); break;
	  case "setsort": $this->setSort(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму редактирования текста страницы.
 */

  function getPageForm()
  {
    $form = new A_Form("module_feedback_page.tpl");
	$form->data['content']=getTextOption(SECTION,'content');
	$this->RESULT['title']=SECTION_NAME;
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму добавления поля.
 */

  function getAddForm()
  {
    $form = new A_Form("module_feedback_add.tpl");
	$form->data['vars']=getLists();
	$form->data['full']=is_dir('plugins');
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования поля.
 */

  function getEditForm()
  {
    $form = new A_Form("module_feedback_edit.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],DOMAIN."_fields");
	$form->data['vars']=getLists();
	$form->data['name']=$form->data['name_'.LANG];
	$form->data['full']=is_dir('plugins');
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму с тестом архивного сообщения.
 */

  function getmessageform()
  {
    $form = new A_Form("module_feedback_message.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION."_arch");
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Сортировка.
 */

  function setSort()
  {
    $sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
	$i=1;
	foreach($sort as $id)
	A::$DB->Update(DOMAIN."_fields",array('sort'=>$i++),"id=".(integer)$id);
  }
}

A::$REQUEST = new FeedbackModule_Request;