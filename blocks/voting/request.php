<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Blocks
 */
/**************************************************************************/

/**
 * Серверная сторона AJAX настройки блока "Форма голосования".
 *
 * <a href="http://wiki.a-cms.ru/blocks/voting">Руководство</a>.
 */

class voting_BlockRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "add": $this->Add(); break;
	  case "edit": $this->Edit(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму добавления.
 */

  function Add()
  {
	$form = new A_Form("block_voting_add.tpl");
	$form->data['sections']=A::$DB->getAssoc("SELECT id,caption FROM ".DOMAIN."_sections WHERE module='voting' ORDER BY sort");
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования.
 */

  function Edit()
  {
	$form = new A_Form("block_voting_edit.tpl");
	$block=A::$DB->getRowById($_POST['id'],DOMAIN."_blocks");
	$form->data=!empty($block['params'])?unserialize($block['params']):array();
	$form->data['sections']=A::$DB->getAssoc("SELECT id,caption FROM ".DOMAIN."_sections WHERE module='voting' ORDER BY sort");
	$this->RESULT['html']=$form->getContent();
  }
}

A::$REQUEST = new voting_BlockRequest;