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
 * Серверная сторона AJAX панели управления модуля "Поиск по сайту".
 *
 * <a href="http://wiki.a-cms.ru/modules/search">Руководство</a>.
 */

class SearchModule_Request extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  { switch($action)
	{ case "getindexform": $this->getIndexForm(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму переиндексирования.
 */

  function getIndexForm()
  {
    $form = new A_Form("module_search_index.tpl");
	$form->data['sections']=array();
	A::$DB->query("SELECT * FROM ".DOMAIN."_sections WHERE lang='".LANG."' OR lang='all' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	if(function_exists($row['module'].'_searchIndexAll'))
	$form->data['sections'][]=$row;
	A::$DB->free();
	$this->RESULT['html']=$form->getContent();
  }
}

A::$REQUEST = new SearchModule_Request;