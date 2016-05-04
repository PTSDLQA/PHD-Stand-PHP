<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */
require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
/**
 * Smarty date_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_format<br>
 * Purpose:  format datestamps via strftime<br>
 * Input:<br>
 *         - string: input date string
 *         - format: strftime format for output
 *         - default_date: default date if $string is empty
 * @link http://smarty.php.net/manual/en/language.modifier.date.format.php
 *          date_format (Smarty online manual)
 * @param string
 * @param string
 * @param string
 * @return string|void
 * @uses smarty_make_timestamp()
 */
function smarty_modifier_date_format($string, $format="%d.%m.%Y", $default_date=null)
{
  $date=smarty_make_timestamp($string);

  if(strpos($format,"%D")!==false)
  { $now=date('d',$date)==date('d') && date('m',$date)==date('m') && date('Y',$date)==date('Y');
    if($now)
    $format=str_replace("%D","сегодня",$format);
	else
	$format=str_replace("%D","%d.%m.%Y",$format);
  }

  $format=str_replace("%T","%H:%M",$format);

  if(strpos($format,"%B")!==false)
  { $month = array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
    $m=date("m",$date);
    $format=str_replace("%B",$month[$m-1],$format);
  }

  if($string != '')
  {
    return strftime($format, $date);
  }
  elseif(isset($default_date) && $default_date!='')
  {
    return strftime($format, smarty_make_timestamp($default_date));
  }
  else
  {
    return "";
  }
}