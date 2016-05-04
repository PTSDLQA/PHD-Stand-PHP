<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Modules
 */
/**************************************************************************/

/**
 * Календарь.
 */

class A_Calendar
{
/**
 * Начальный день.
 */

  public $startDay=0;

/**
 * Список дней недели.
 */

  public $dayNames=array("П", "В", "С", "Ч", "П", "С", "В");

/**
 * Список месяцев.
 */

  public $monthNames=array("Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");

/**
 * Список количества дней в месяцах.
 */

  public $daysInMonth=array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

/**
 * Разделы материалов участвующие в архиве: полный идентификатор раздела => идентификатор модуля.
 */

  public $sections;

/**
 * Полный строковой идентификатор раздела архива.
 */

  public $section;

/**
 * Конструктор.
 *
 * @param string $sections Разделы материалов участвующие в архиве: полный идентификатор раздела => идентификатор модуля.
 * @param string $section Полный строковой идентификатор раздела архива.
 */

  function __construct($sections,$section=SECTION)
  {
    $this->sections=$sections;
	$this->section=$section;
  }

/**
 * Формирование ссылки для выбора месяца.
 *
 * @param integer $month Номер месяца.
 * @param integer $year Номер года.
 * @return string
 */

  function getCalendarLink($month,$year)
  {
    $link=getSectionLink(!empty($this->section)?$this->section:SECTION).'00-'.sprintf('%02d',$month).'-'.$year.'.html';
	return $link;
  }

/**
 * Формирование ссылки для выбора дня.
 *
 * @param integer $day Номер дня.
 * @param integer $month Номер месяца.
 * @param integer $year Номер года.
 * @return string
 */

  function getDateLink($day, $month, $year)
  {

    $date1=mktime(0,0,0,$month,$day,$year);
	$date2=mktime(23,59,59,$month,$day,$year);

	$count=0;
	foreach($this->sections as $section=>$module)
	if($module=='catalog')
	$count+=A::$DB->getOne("SELECT COUNT(*) FROM {$section}_{$module} WHERE date>=$date1 AND date<=$date2 AND active='Y'");

	if($count>0)
	$link=getSectionLink(!empty($this->section)?$this->section:SECTION).sprintf('%02d',$day).'-'.sprintf('%02d',$month).'-'.$year.'.html';
	else
	$link="";

	return $link;
  }

/**
 * Возвращает количетво дней в месяце.
 *
 * @param integer $month Номер месяца.
 * @param integer $year Номер года.
 * @return integer
 */

  function getDaysInMonth($month, $year)
  {
    if ($month<1 || $month>12)
    return 0;

	$d=$this->daysInMonth[$month-1];
    if($month==2)
    { if($year%4==0)
      { if ($year%100==0)
        { if ($year%400==0)
          $d=29;
        }
        else
        $d=29;
      }
    }
    return $d;
  }

/**
 * Формирует HTML код календаря на месяц.
 *
 * @param integer $selday Выбранный день.
 * @param integer $m Номер месяца.
 * @param integer $y Номер года.
 * @return string
 */

  function getMonthHTML($selday, $m, $y)
  {
    $s="";

    $a=$this->adjustDate($m,$y);
    $month=$a[0];
    $year=$a[1];

    $daysInMonth=$this->getDaysInMonth($month, $year);
    $date=getdate(mktime(12, 0, 0, $month, 1, $year));

    $first=$date['wday'];
    $monthName=$this->monthNames[$month - 1];

    $prev=$this->adjustDate($month - 1, $year);
    $next=$this->adjustDate($month + 1, $year);

    $prevMonth=$this->getCalendarLink($prev[0], $prev[1]);
    $nextMonth=$this->getCalendarLink($next[0], $next[1]);

    $header=$monthName." ".$year;

    $s.="<table class=\"calendar\" cellspacing=\"0\" cellpadding=\"0\">\n";
    $s.="<tr>\n";
    $s.="<th align=\"center\">" . (($prevMonth == "") ? "&nbsp;" : "<a href=\"$prevMonth\">&lt;&lt;</a>")  . "</th>\n";
    $s.="<th align=\"center\" class=\"calendar_month\" colspan=\"5\">$header</th>\n";
    $s.="<th align=\"center\">" . (($nextMonth == "") ? "&nbsp;" : "<a href=\"$nextMonth\">&gt;&gt;</a>")  . "</th>\n";
    $s.="</tr>\n";

    $s.="<tr>\n";
    $s.="<td align=\"center\" class=\"calendar_day\">" . $this->dayNames[($this->startDay)%7] . "</td>\n";
    $s.="<td align=\"center\" class=\"calendar_day\">" . $this->dayNames[($this->startDay+1)%7] . "</td>\n";
    $s.="<td align=\"center\" class=\"calendar_day\">" . $this->dayNames[($this->startDay+2)%7] . "</td>\n";
    $s.="<td align=\"center\" class=\"calendar_day\">" . $this->dayNames[($this->startDay+3)%7] . "</td>\n";
    $s.="<td align=\"center\" class=\"calendar_day\">" . $this->dayNames[($this->startDay+4)%7] . "</td>\n";
    $s.="<td align=\"center\" class=\"calendar_day\">" . $this->dayNames[($this->startDay+5)%7] . "</td>\n";
    $s.="<td align=\"center\" class=\"calendar_day\">" . $this->dayNames[($this->startDay+6)%7] . "</td>\n";
    $s.="</tr>\n";

    $d=$this->startDay + 2 - $first;
    while ($d > 1)
    $d -= 7;

    $today=getdate(time());

    while($d<=$daysInMonth)
    { $s.= "<tr>\n";
      for ($i=0; $i < 7; $i++)
      { $class=($year == $today['year'] && $month == $today['mon'] && $d == $today['mday']) ? "calendar_today" : "calendar_cell";

		if($selday==$d && $d>0)
		$class="calendar_selected";

        $s.= "<td class=\"$class\" valign=\"top\">";
        if($d > 0 && $d <= $daysInMonth)
        { $link=$this->getDateLink($d, $month, $year);
          $s.=((empty($link) || $selday==$d) ? ($selday==$d?"<b>$d</b>":$d) : "<a href=\"$link\">$d</a>");
        }
        else
        $s.="&nbsp;";

        $s.="</td>\n";
        $d++;
      }
      $s.="</tr>\n";
    }

    $s.="</table>\n";
    return $s;
  }

/**
 * Корректирование даты.
 *
 * @param integer $month Номер месяца.
 * @param integer $year Номер года.
 * @return array Массив 0 => номер месяца, 1 => номер года.
 */

  function adjustDate($month, $year)
  {
    $a=array();
    $a[0]=$month;
    $a[1]=$year;
    while($a[0]>12)
    { $a[0]-=12;
      $a[1]++;
    }
    while($a[0]<=0)
    { $a[0]+=12;
      $a[1]--;
    }
    return $a;
  }
}