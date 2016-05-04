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

function smarty_function_help($params, &$smarty)
{
  require_once $smarty->_get_plugin_filepath('function','popup');

  if(!isset($params['fgcolor']))
  $params['fgcolor']="#ffffee";
  if(!isset($params['noclose']))
  $params['noclose']=true;
  if(!isset($params['sticky']))
  $params['sticky']=false;
  if(!isset($params['bgcolor']))
  $params['bgcolor']="cccccc";
  if(!isset($params['width']))
  $params['width']=300;

  return smarty_function_popup($params, $smarty);
}