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
 * Блок "Облако тегов".
 *
 * <a href="http://wiki.a-cms.ru/blocks/cloud">Руководство</a>.
 */

class cloud_Block extends A_Block
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
    $this->supportCached();

	$count=isset($this->params['count'])?(integer)$this->params['count']:50;
	$idsec=!empty($this->params['idsearch'])?(integer)$this->params['idsearch']:0;

	$this->Assign('tags',A_SearchEngine::getInstance()->getCloudTags($count,10,$idsec));
  }
}