<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Blocks
 */
/**************************************************************************/

require_once("modules/archive/calendar.php");

/**
 * Блок "Календарь архива материалов".
 *
 * <a href="http://wiki.a-cms.ru/blocks/archive">Руководство</a>.
 */

class archive_Block extends A_Block
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$this->supportCached(true);

	if($this->section==SECTION)
    { $day=A::$MAINFRAME->day;
	  $month=A::$MAINFRAME->month;
	  $year=A::$MAINFRAME->year;
	}
	else
	{ $day=0;
	  $month=0;
	  $year=0;
	}

	if($month==0) $month=date("m",time());
	if($year==0) $year=date("Y",time());

	$checkeds=!empty($this->options['sections'])?unserialize($this->options['sections']):array();

	$sections=array();
    foreach($checkeds as $idsec)
	if($row=A::$DB->getRowById($idsec,DOMAIN."_sections"))
	{ $section = DOMAIN."_".$row['lang']."_".$row['name'];
	  $sections[$section]=$row['module'];
	}

	$calendar = new A_Calendar($sections,$this->section);

	$this->Assign("calendar",$calendar->getMonthHTML($day,$month,$year));
  }
}