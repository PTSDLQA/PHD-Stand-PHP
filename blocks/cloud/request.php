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
 * Серверная сторона AJAX настройки блока "Облако тегов".
 *
 * <a href="http://wiki.a-cms.ru/blocks/cloud">Руководство</a>.
 */

class cloud_BlockRequest extends A_Request
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
	$form = new A_Form("block_cloud_add.tpl");
	$form->data['idsec']=A::$DB->getOne("SELECT id FROM ".DOMAIN."_sections WHERE module='search'");
	$form->data['sections']=A_SearchEngine::getInstance()->getSections();
	foreach($form->data['sections'] as $idsec=>$caption)
	if(!getOption(getSectionById($idsec),'usetags'))
	unset($form->data['sections'][$idsec]);
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования.
 */

  function Edit()
  {
	$form = new A_Form("block_cloud_edit.tpl");
	$block=A::$DB->getRowById($_POST['id'],DOMAIN."_blocks");
	$form->data=!empty($block['params'])?unserialize($block['params']):array();
	if(!isset($form->data['count']))
	$form->data['count']=50;
	$form->data['sections']=A_SearchEngine::getInstance()->getSections();
	foreach($form->data['sections'] as $idsec=>$caption)
	if(!getOption(getSectionById($idsec),'usetags'))
	unset($form->data['sections'][$idsec]);
	$this->RESULT['html']=$form->getContent();
  }
}

A::$REQUEST = new cloud_BlockRequest;