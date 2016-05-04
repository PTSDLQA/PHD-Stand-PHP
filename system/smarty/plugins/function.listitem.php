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

function smarty_function_listitem($params, &$smarty)
{
  if(!empty($params['list']) && !empty($params['id']))
  {
    $list=loadList($params['list']);
	if(isset($list[$params['id']]))
	return is_array($list[$params['id']])&&empty($params['fulldata'])?$list[$params['id']]['name']:$list[$params['id']];
  }
  return "";
}