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

function smarty_function_attachfile($params, &$smarty)
{
  if(!empty($params['id']) && method_exists($smarty,"addAttachmentById"))
  $smarty->addAttachmentById($params['id']);
}