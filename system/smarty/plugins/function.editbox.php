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

function smarty_function_editbox($params, &$smarty)
{
  $name="";
  $text="";
  $max=255;
  $width="100%";
  $attr="";
  foreach($params as $_key => $_val)
  switch($_key)
  { case "name": $name=$_val; break;
    case "text": $text=htmlspecialchars($_val); break;
	case "max": $max=$_val; break;
	case "width": $width=$_val; break;
	default: $attr.=" $_key=\"$_val\"";
  }
  return "<input type=\"text\" name=\"$name\" value=\"$text\" maxlength=\"$max\" style=\"width:$width\"$attr/>";
}