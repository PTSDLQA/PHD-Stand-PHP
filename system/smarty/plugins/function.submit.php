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

function smarty_function_submit($params, &$smarty)
{
  $caption="";
  $width="120px";
  $class="submit";
  $attr="";
  foreach($params as $_key => $_val)
  switch($_key)
  { case "caption": $caption=$_val; break;
	case "class": $class=$_val; break;
	case "width": $width=$_val; break;
	default: $attr.=" $_key=\"$_val\"";
  }
  return "<input name=\"savebutton\" class=\"$class\" type=\"submit\" value=\"$caption\" style=\"width:$width\"$attr/>";
}