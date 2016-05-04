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

function smarty_function_selectbox($params, &$smarty)
{
  $selected=0;
  $options="";
  $attr="";

  if(!empty($params['selected']))
  { $selected=htmlspecialchars($params['selected']);
    unset($params['selected']);
  }

  if(isset($params['size']))
  unset($params['size']);

  if(!empty($params['options']))
  { require_once $smarty->_get_plugin_filepath('function','html_options');
    $options=smarty_function_html_options(array('options'=>$params['options'],'selected'=>$selected),$smarty);
    unset($params['options']);
  }

  if(!empty($params['list']))
  { require_once $smarty->_get_plugin_filepath('function','html_options');
    $options=smarty_function_html_options(array('options'=>loadList($params['list']),'selected'=>$selected),$smarty);
    unset($params['options']);
  }

  if(!empty($params['empty']))
  { $options="<option value=\"0\">".htmlspecialchars($params['empty'])."</option>\n".$options;
    unset($params['empty']);
  }

  foreach($params as $_key => $_val)
  $attr.=" $_key=\"$_val\"";

  return "<select$attr/>$options</select>";
}