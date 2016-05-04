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
 * Серверная сторона AJAX панели управления модуля "Голосование".
 *
 * <a href="http://wiki.a-cms.ru/modules/voting">Руководство</a>.
 */

class VotingModule_Request extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "getaddvariantform": $this->getAddForm(); break;
	  case "geteditvariantform": $this->getEditForm(); break;
	  case "getresultform": $this->getResultForm(); break;
	  case "setsort": $this->setSort(); break;
    }
  }

/**
 * Обработчик действия: Отдает форму добавления варианта.
 */

  function getAddForm()
  {
    $form = new A_Form("module_voting_add.tpl");
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования варианта.
 */

  function getEditForm()
  {
    $form = new A_Form("module_voting_edit.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION."_variants");
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Сортировка вариантов.
 */

  function setSort()
  {
    $sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
	$i=1;
	foreach($sort as $id)
	A::$DB->Update(SECTION."_variants",array('sort'=>$i++),"id=".(integer)$id);
  }

/**
 * Обработчик действия: Отдает форму с результатами из архива.
 */

  function getResultForm()
  {
    $form = new A_Form("module_voting_result.tpl");
    $form->data=A::$DB->getRowById($_POST['id'],SECTION."_arch");
	$form->data['result']=!empty($form->data['result'])?unserialize($form->data['result']):array();
	foreach($form->data['result'] as $i=>$row)
	$form->data['result'][$i]['pr']=!empty($form->data['count'])?round($row['count']*100/$form->data['count'],2):0;
	$this->RESULT['html'] = $form->getContent();
  }
}

A::$REQUEST = new VotingModule_Request;