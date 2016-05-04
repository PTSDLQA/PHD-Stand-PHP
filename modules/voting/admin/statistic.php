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
 * Статистика раздела на базе модуля "Голосование".
 *
 * <a href="http://wiki.a-cms.ru/modules/voting">Руководство</a>.
 */

class voting_Statistic extends A_Statistic
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
    $options=getOptions($this->section);

	$this->Assign("datebegin",$options['datebegin']);
	$this->Assign("dateend",$options['dateend']);

	if(!empty($options['datebegin']) && !empty($options['dateend']) && !($options['datebegin']<time() && $options['dateend']>time()))
	$status=1;
	elseif(empty($options['active']))
	$status=2;
	else
	$status=3;

	$this->Assign("status",$status);

	$this->Assign("vote_count",A::$DB->getOne("SELECT SUM(count) FROM {$this->section}_variants"));
  }
}