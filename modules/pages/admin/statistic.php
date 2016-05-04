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
 * Статистика раздела на базе модуля "Страницы".
 *
 * <a href="http://wiki.a-cms.ru/modules/pages">Руководство</a>.
 */

class pages_Statistic extends A_Statistic
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
    $this->Assign("pages_count",A::$DB->getOne("SELECT COUNT(*) FROM {$this->section} WHERE type='page'"));
	$this->Assign("dirs_count",A::$DB->getOne("SELECT COUNT(*) FROM {$this->section} WHERE type='dir'"));
  }
}