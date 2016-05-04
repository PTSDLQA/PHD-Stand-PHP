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
 * Блок "Форма поиска по сайту".
 *
 * <a href="http://wiki.a-cms.ru/blocks/search">Руководство</a>.
 */

class search_Block extends A_Block
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$this->Assign("query",!empty($_GET['query'])?htmlspecialchars($_GET['query']):"");
	$this->Assign("sections",A_SearchEngine::getInstance()->getSections());
  }
}