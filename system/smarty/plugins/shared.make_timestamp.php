<?php
/**
 * Smarty shared plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Function: smarty_make_timestamp<br>
 * Purpose:  used by other smarty functions to make a timestamp
 *           from a string.
 * @param string
 * @return string
 */
function smarty_make_timestamp($string)
{
  $time = (integer)$string;
  $time=$time>0?$time:time();
  if(!empty($GLOBALS['A_INCHOUR']))
  $time+=(integer)$GLOBALS['A_INCHOUR']*3600;
  return $time;
}