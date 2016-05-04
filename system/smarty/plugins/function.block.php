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

function smarty_function_block($params, &$smarty)
{
  if(!empty($params['id']))
  { $idb=$params['id'];
    unset($params['id']);
    if(!empty($smarty->_tpl_vars['blocks'][$idb]))
    return $smarty->_tpl_vars['blocks'][$idb]->getContent($params);
  }
  return "";
}