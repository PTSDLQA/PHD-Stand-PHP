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

function smarty_function_dateselect($params, &$smarty)
{
  $name=!empty($params['name'])?$params['name']:"";
  $time=!empty($params['date'])?$params['date']:time();
  $usetime=!empty($params['usetime'])?"true":"false";
  $maxtime=!empty($params['maxtime'])?"true":"false";
  $onchange=!empty($params['onchange'])?$params['onchange']:"";
  if($usetime=='true')
  { $date=date("d.m.Y H:i",$time);
	$str='<input type="hidden" id="'.$name.'_utc" name="'.$name.'" value="'.$time.'">';
    $str.='<input type="text" id="'.$name.'_txt" name="'.$name.'_txt" value="'.$date.'" readonly="readonly" onchange="'.$onchange.'" size="17" />&nbsp;';
    $str.='<img alt="Выбрать дату" onclick="new CalendarDateSelect($(this).previous(),{time:true,year_range:10,popup:\'force\'});" src="/templates/admin/images/calendar.gif" width="16" height="16" style="vertical-align:middle;cursor:pointer;"/>';
  }
  else
  { $date=date("d.m.Y",$time);
	$str='<input type="hidden" id="'.$name.'_utc" name="'.$name.'" value="'.$time.'">';
    $str.='<input type="text" id="'.$name.'_txt" name="'.$name.'_txt" value="'.$date.'" readonly="readonly" onchange="'.$onchange.'" size="11" />&nbsp;';
    $str.='<img alt="Выбрать дату" onclick="new CalendarDateSelect($(this).previous(),{time:false,year_range:10,popup:\'force\',maxtime:'.$maxtime.'});" src="/templates/admin/images/calendar.gif" width="16" height="16" style="vertical-align:middle;cursor:pointer;"/>';
  }
  return $str;
}