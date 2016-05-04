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

function smarty_function_captcha($params, &$smarty)
{
  $width=160;
  $height=50;
  $attr="";
  foreach($params as $_key => $_val)
  switch($_key)
  { case "width": $width=$_val; break;
	case "height": $height=$_val; break;
	default: $attr.=" $_key=\"$_val\"";
  }
  $ch=md5(time().'captcha');
  return "<img src=\"/captcha.php?ch=$ch\" width=\"$width\" height=\"$height\"{$attr}/>";
}