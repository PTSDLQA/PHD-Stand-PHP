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

function smarty_function_ie6warning($params, &$smarty)
{
  $html="";
  if(stripos($_SERVER['HTTP_USER_AGENT'],'msie 6')!==false)
  $html='<div style="width:100%;height:20px;color:red;background:#ffffee"><p style="padding:3px">Ваш веб-браузер очень устарел! <a target="_blank" href="http://ie6.a-cms.ru/" style="color:black">Что это значит?</a></p></div>';
  return $html;
}