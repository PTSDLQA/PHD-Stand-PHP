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
 * Статистика раздела на базе модуля "Поиск по сайту".
 *
 * <a href="http://wiki.a-cms.ru/modules/search">Руководство</a>.
 */

class search_Statistic extends A_Statistic
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
    $this->Assign("index_count",A::$DB->getOne("SELECT COUNT(*) FROM {$this->section}"));
  }
}