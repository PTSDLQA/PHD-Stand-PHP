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

function smarty_modifier_number($string)
{
  if(mb_strlen($string)<=7)
  return preg_replace("/([0-9]{3})$/i"," \\1",$string);
  else
  return preg_replace("/([0-9]{3})([0-9]{3})$/i"," \\2 \\1",$string);
}