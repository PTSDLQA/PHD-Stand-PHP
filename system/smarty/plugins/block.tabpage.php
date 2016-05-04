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

function smarty_block_tabpage($params,$content,&$smarty)
{
  $idtab=!empty($params['idtab'])?'_'.$params['idtab']:"";

  if(empty($params['id'])) return "";

  $content=A::$OBSERVER->Modifier('prepareTab',$params['id'],$content);

  if(!empty($_REQUEST['tab'.$idtab]) && $params['id']==$_REQUEST['tab'.$idtab])
  return "<div id=\"tabcontent{$idtab}_{$params['id']}\">\n$content\n</div>";
  else
  return "<div id=\"tabcontent{$idtab}_{$params['id']}\" style=\"display:none\">\n$content\n</div>";
}