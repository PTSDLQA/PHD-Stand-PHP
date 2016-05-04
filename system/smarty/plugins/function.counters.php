<?php
/**************************************************************************/
/* Smarty plugin
/* @copyright 2011 "Астра Вебтехнологии"
/* @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
/* @link http://a-cms.ru
 * @package Smarty
 * @subpackage plugins
/**************************************************************************/

function smarty_function_counters($params, &$smarty)
{
  if($smarty->compile_dir=='templates_c/admin/wizard/')
  return '<img src="/templates/admin/images/counter.jpg" width="88" height="31">';
  else
  return A::$OPTIONS['codecounters'];
}