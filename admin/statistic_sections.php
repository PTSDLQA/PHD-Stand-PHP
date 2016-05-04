<?php
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package APanel
 */
/**************************************************************************/

class SectionsStatistic extends A_MainFrame
{
  public $statistics=array();

  function __construct()
  {
    parent::__construct("statistic_sections.tpl");
  }

  function createData()
  {
	$this->Assign("caption","Статистика");

	A::$DB->query("SELECT * FROM mysite_sections ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $file="modules/{$row['module']}/admin/statistic.php";
	  if(file_exists($file))
	  { $section = "mysite_ru_".$row['name'];
	    require_once($file);
	    $stat=array();
		$stat['name']=$row['caption'];
		$class=$row['module'].'_Statistic';
	    $stat['block'] = new $class($section,$row['module']);
		$this->statistics[]=$stat;
	  }
	}
	A::$DB->free();

	$this->Assign_by_ref("statistics",$this->statistics);
  }
}

A::$MAINFRAME = new SectionsStatistic;