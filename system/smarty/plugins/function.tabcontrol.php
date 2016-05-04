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

function smarty_function_tabcontrol($params, &$smarty)
{
  if(!empty($params['id']))
  { $idtab='_'.$params['id'];
    unset($params['id']);
  }
  else
  $idtab="";

  $params=A::$OBSERVER->Modifier('prepareTabControl',$idtab,$params);

  $tab=!empty($_REQUEST['tab'.$idtab])?$_REQUEST['tab'.$idtab]:key($params);
  $_REQUEST['tab'.$idtab]=$tab;
  $content="<script type=\"text/javascript\">
  var tabs_id{$idtab}='$tab';
  function switchTab{$idtab}(id)
  { if(id!=tabs_id{$idtab})
    { \$('tabcontent{$idtab}_'+tabs_id{$idtab}).style.display='none';
	  \$('tabcontent{$idtab}_'+id).style.display='';
	  \$(tabs_id{$idtab}).className='';
      \$(id).className='currenttab';
      tabs_id{$idtab}=id;
    }
  }
  </script>";
  $content.="<div id='tabs'><ul>";
  foreach($params as $_key => $_val)
  { $_val=str_replace(' ','&nbsp;',$_val);
    $content.="<li".($_key==$tab?" class=\"currenttab\"":"")." id=\"$_key\"><a href=\"javascript:switchTab{$idtab}('$_key')\">$_val</a></li>";
  }
  $content.="</ul></div><div class=\"clear\"><!-- --></div>";
  return $content;
}