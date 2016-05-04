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

function smarty_function_lightbox_init($params, &$smarty)
{
  $html='';
  $html.='<script type="text/javascript" src="/system/jsaculous/prototype.js"></script>'."\n";
  $html.='<script type="text/javascript" src="/system/jsaculous/scriptaculous.js?load=effects,builder"></script>'."\n";
  $html.='<script type="text/javascript" src="/system/jsaculous/lightbox.js"></script>'."\n";
  $html.='<link rel="stylesheet" href="/templates/admin/lightbox/lightbox.css" type="text/css" media="screen">'."\n";
  return $html;
}