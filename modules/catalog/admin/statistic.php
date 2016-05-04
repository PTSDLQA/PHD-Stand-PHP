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
 * Статистика раздела на базе модуля "Каталог материалов".
 *
 * <a href="http://wiki.a-cms.ru/modules/catalog">Руководство</a>.
 */

class catalog_Statistic extends A_Statistic
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
    $this->Assign("categories_count",A::$DB->getOne("SELECT COUNT(*) FROM {$this->section}_categories"));
	$this->Assign("items_count",A::$DB->getOne("SELECT COUNT(*) FROM {$this->section}_catalog"));
	$this->Assign("last_date",A::$DB->getOne("SELECT MAX(date) FROM {$this->section}_catalog"));
	if($usecomments=getOption($this->section,"usecomments"))
	{ $idsec=getSectionId($this->section);
	  $this->Assign("usecomments",$usecomments);
	  $this->Assign("comments_count",A::$DB->getOne("SELECT COUNT(*) FROM ".DOMAIN."_comments WHERE idsec=$idsec"));
	}
  }
}