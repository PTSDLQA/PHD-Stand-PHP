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

function smarty_function_colorselect($params, &$smarty)
{
  $name="";
  $value="FFFFFF";
  $attr="";
  foreach($params as $_key => $_val)
  switch($_key)
  { case "name": $name=$_val; break;
    case "color":
	case "value": $value=htmlspecialchars(strtoupper($_val)); break;
	default: $attr.=" $_key=\"$_val\"";
  }
  $str="<span id='{$name}_cbox' style='border:1px solid gray;background:#$value;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<input id=\"{$name}_text\" type=\"text\" name=\"$name\" value=\"$value\" maxlength=6 style=\"width:55px\"$attr onchange=\"$('{$name}_cbox').style.background='#'+this.value\"/>&nbsp;";
  $str.='<img id="'.$name.'_img" alt="Выбрать цвет" onclick="getcolorselect(\''.$name.'\')" src="/templates/admin/images/colors.gif" width="16" height="16" style="vertical-align:middle;cursor:pointer;"/>';
  return $str;
}