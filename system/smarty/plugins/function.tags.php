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

function smarty_function_tags($params, &$smarty)
{
  $name="tags";
  $value="";
  $width="100%";
  $attr="";

  foreach($params as $_key => $_val)
  switch($_key)
  { case "name": $name=htmlspecialchars($_val); break;
    case "text":
	case "value": $value=htmlspecialchars($_val); break;
	case "width": $width=$_val; break;
	default: $attr.=" $_key=\"$_val\"";
  }

  if(!empty($params['mode']) && $params['mode']=='textarea')
  $str='<textarea id="tags_editor" name="'.$name.'" style="width:'.$width.'" rows=3'.$attr.'>'.$value.'</textarea>';
  else
  $str='<input type="text" id="tags_editor" name="'.$name.'" value="'.$value.'" style="width:'.$width.'"'.$attr.'>';
  $str.='<span id="indicator" style="height:11px; display:none;">Загрузка...</span>';
  $str.='<div id="tags_choices" class="autocomplete"></div>';

  if(A_MODE==A_MODE_FRONT && empty(A::$REQUEST))
  { if($search=getSectionByModule('search'))
    { $str.="\n<script type=\"text/javascript\">\n";
      $str.="new Ajax.Autocompleter('tags_editor', 'tags_choices', '/request.php?mode=front&item={$search}&action=gettags',{paramName:'query',minChars:1,indicator:'indicator',tokens:','});\n";
      $str.="\$('tags_editor').observe('focus', function(){ if(\$('tags_editor').form) \$('tags_editor').form.observe('submit', Event.stop); });\n";
      $str.="\$('tags_editor').observe('blur',  function(){ if(\$('tags_editor').form) \$('tags_editor').form.stopObserving('submit', Event.stop); });\n";
	  $str.="</script>\n";
	}
  }

  return $str;
}