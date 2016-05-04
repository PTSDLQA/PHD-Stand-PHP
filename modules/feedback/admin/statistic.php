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
 * Статистика раздела на базе модуля "Обратная связь".
 *
 * <a href="http://wiki.a-cms.ru/modules/feedback">Руководство</a>.
 */

class feedback_Statistic extends A_Statistic
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
    $this->Assign("archcount",A::$DB->getCount($this->section.'_arch'));
  }
}