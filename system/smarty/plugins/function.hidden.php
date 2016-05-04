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

function smarty_function_hidden($params, &$smarty)
{
  $params['value']=(string)$params['value'];
  if(!empty($params['name']))
  { $value=!empty($params['value'])?htmlspecialchars($params['value']):"";
    return "<input type=\"hidden\" name=\"{$params['name']}\" value=\"{$value}\"/>";
  }
  else
  return "";
}