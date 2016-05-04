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

function smarty_function_loadlist($params, &$smarty)
{
  if(!empty($params['var']) && !empty($params['list']))
  { $list=loadList($params['list']);
    if(!empty($params['id']))
    $smarty->Assign($params['var'],isset($list[$params['id']])?$list[$params['id']]:"");
    else
    $smarty->Assign($params['var'],$list);
  }
  else
  $smarty->Assign($params['var'],array());
}