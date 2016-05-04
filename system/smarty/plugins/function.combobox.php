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

function smarty_function_combobox($params, &$smarty)
{
  if(!extension_loaded('xsl'))
  { require_once $smarty->_get_plugin_filepath('function','selectbox');
    return smarty_function_selectbox($params, $smarty);
  }

  if(isset($params['name']))
  { $name=$params['name'];
    unset($params['name']);
  }
  else
  $name="combo";

  if(isset($params['text']))
  { $value=trim($params['text']);
	unset($params['text']);
  }
  else
  $value="";

  if(isset($params['selected']))
  { $selected=$params['selected'];
	unset($params['selected']);
  }
  else
  $selected=0;

  if(isset($params['options']))
  { $options=$params['options'];
    unset($params['options']);
  }
  else
  $options=array();

  if(!empty($options) && empty($value) && !empty($selected))
  $params['title']=isset($options[$selected])?$options[$selected]:"";
  else
  $params['title']=$value;

  $params['id']='wfc_'.$name;
  if(empty($params['mode']) || $params['mode']=='combo')
  $params['ownvalues']=true;
  $params['onchange']="this.title.form.{$name}.value=this.value;this.title.form.{$name}_txt.value=this.title.value;";

  if(!A::$REQUEST)
  { require_once 'system/libs/smartselect.php';
    $FWC=SmartSelect::getInstance();
    $str=$FWC->newSmartSelect($options,$params);
  }
  else
  { $json1=array();
    foreach($options as $_key=>$_value)
    $json1[]=str_replace("'",'\u0027',json_encode(array(trim($_value),$_key)));
	$json1="[".implode(",",$json1)."]";
    $json2=json_encode($params);
    $str='<div class="smartselect" source=\''.$json1.'\' attr=\''.$json2.'\'/></div>';
  }

  $str.='<input type="hidden" name="'.$name.'_txt" value="'.htmlspecialchars($params['title']).'">';
  $str.='<input type="hidden" name="'.$name.'" value="'.htmlspecialchars(!empty($options)?$selected:$value).'">';

  return $str;
}