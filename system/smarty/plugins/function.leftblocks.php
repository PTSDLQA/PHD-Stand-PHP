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

function smarty_function_leftblocks($params,&$smarty)
{
  $content="";
  $separator=isset($params['separator'])?$params['separator']:"";
  if(!empty($smarty->_tpl_vars['leftblocks']))
  foreach($smarty->_tpl_vars['leftblocks'] as &$block)
  $content.=$block->getContent().$separator;
  return $content;
}