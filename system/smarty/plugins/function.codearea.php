<?php
/**************************************************************************/
/* Smarty plugin
/* @copyright 2011 "Астра Вебтехнологии"
/* @author Vitaly Hohlov <admin@a-cms.ru>
/* @link http://a-cms.ru
 * @package Smarty
 * @subpackage plugins
/**************************************************************************/

function smarty_function_codearea($params, &$smarty)
{
  $name=!empty($params['name'])?$params['name']:"code";
  $type=!empty($params['type'])?$params['type']:"bblite";
  $text=!empty($params['text'])?htmlspecialchars($params['text']):"";
  $html='';

  if(empty($params['lite']))
  {
    $html.='
    <link rel="stylesheet" type="text/css" href="/templates/admin/markitup/skin/style.css" />
    <link rel="stylesheet" type="text/css" href="/templates/admin/markitup/set/'.$type.'/style.css" />';
    if(!empty($params['width']))
    { $html.="\n<style>\n.markItUp { width: {$params['width']}; }\n";
      if(!empty($params['width2']))
      $html.=".markItUpEditor { width: {$params['width2']}; }\n</style>\n";
      else
      { if(strpos($params['width'],"%"))
        $params['width']=((integer)$params['width']-8).'%';
        else
        $params['width']=((integer)$params['width']-60).'px';
	    $html.=".markItUpEditor { width: {$params['width']}; }\n</style>\n";
	  }
    }
    if(!empty($params['height']))
    $html.="<style>\n.markItUpEditor { height: {$params['height']}; }</style>\n";
    $html.='
    <script type="text/javascript" src="/system/jsquery/jquery.js"></script>
    <script type="text/javascript" src="/system/jsquery/jquery_markitup.js"></script>
    <script type="text/javascript" src="/system/jsquery/jquery_markitup_'.$type.'.js"></script>';
  }

  $html.='
  <script type="text/javascript">$(document).ready(function(){$(\'#'.$name.'_id\').markItUp(mySettings);});</script>
  <textarea id="'.$name.'_id" name="'.$name.'" cols="80" rows="10">'.$text.'</textarea>';

  return $html;
}