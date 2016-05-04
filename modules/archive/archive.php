<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Modules
 */
/**************************************************************************/

require_once("modules/archive/calendar.php");

/**
 * Модуль "Архив материалов".
 *
 * <a href="http://wiki.a-cms.ru/modules/archive">Руководство</a>.
 */

class ArchiveModule extends A_MainFrame
{
/**
 * Выбранный день.
 */

  public $day=0;

/**
 * Выбранный месяц.
 */

  public $month=0;

/**
 * Выбранный год.
 */

  public $year=0;

/**
 * Маршрутизатор URL.
 */

  function Router($uri)
  {
    if(count($uri)==1)
	{ if(preg_match("/^([0-9]{2})-([0-9]{2})-([0-9]{4})\.html$/",reset($uri),$match))
	  { $this->day=$match[1];
	    $this->month=$match[2];
	    $this->year=$match[3];
		$this->page="archive";
      }
	  else
	  A::NotFound();
	}
	elseif(count($uri)==0)
    $this->page="archive";
	else
    A::NotFound();
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$this->supportCached();
    $this->addCacheParam_Get('page');

	if($this->month==0)
	$this->month=date('m');
	if($this->year==0)
	$this->year=date('Y');

	$checkeds=!empty(A::$OPTIONS['sections'])?unserialize(A::$OPTIONS['sections']):array();

	$srows=$sections=array();
    foreach($checkeds as $idsec)
	if($srow=A::$DB->getRowById($idsec,DOMAIN."_sections"))
	{ $section = DOMAIN."_".$srow['lang']."_".$srow['name'];
	  $sections[$section]=$srow['module'];
	  $srows[$section]=$srow;
	}

	$calendar = new A_Calendar($sections);
	$this->Assign("calendar",$calendar->getMonthHTML($this->day,$this->month,$this->year));

    if($this->day>0)
	{ $date1=mktime(0,0,0,$this->month,$this->day,$this->year);
	  $date2=mktime(23,59,59,$this->month,$this->day,$this->year);
	  $this->Assign("date",$date1);
	}
	else
	{ $date1=mktime(0,0,0,$this->month,1,$this->year);
	  $date2=mktime(0,0,0,$this->month+1>12?1:$this->month+1,1,$this->month+1>12?$this->year+1:$this->year)-1;
	  $this->Assign("date1",$date1);
	  $this->Assign("date2",$date2);
	}

	$items=array();

	foreach($sections as $section=>$module)
	{
	  $srows[$section]['caption']=$srows[$section]['caption_'.LANG];
	  $srows[$section]['link']=getSectionLink($section);

	  A::$DB->query("SELECT *,svote/cvote AS vote FROM {$section}_catalog WHERE date>={$date1} AND date<={$date2} AND active='Y' ORDER BY date");
	  while($row=A::$DB->fetchRow())
	  { $row['section']=$section;
		$items[]=$row;
	  }
	  A::$DB->free();
	}

	$items=array_multisort_key($items,'date');
    $pager = new A_Pager(A::$OPTIONS['rows']);
    $items=$pager->setItems($items);
    foreach($items as $i=>$row)
    { $section=$row['section'];
	  $row['section']=$srows[$section];
	  $row['section_name']=$srows[$section]['caption'];
	  $row['section_link']=$srows[$section]['link'];
	  $row['link']=catalog_createItemLink($row['id'],$section);
	  $row['vote']=round($row['vote'],2);
	  $row['category']=getTreePath("{$section}_categories",$row['idcat']);
	  $row['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array($srows[$section]['id'],$row['id']));
	  $row['idimg']=isset($row['images'][0]['id'])?$row['images'][0]['id']:0;
	  $row['files']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_files WHERE idsec=? AND iditem=? ORDER BY sort",array($srows[$section]['id'],$row['id']));
	  foreach($row['files'] as $i=>$data)
	  { $row['files'][$i]['link']=(LANG==DEFAULTLANG?"":"/".LANG)."/getfile/".$data['id']."/".$data['name'];
	    $row['files'][$i]['size']=sizestring($data['size']);
	  }
	  $row['idfile']=isset($row['files'][0]['id'])?$row['files'][0]['id']:0;
	  if(!empty($row['tags']))
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  prepareValues($section,$row);
	  $items[$i]=$row;
	}

	$this->Assign("items",$items);
	$this->Assign("items_pager",$pager);

	$this->AddNavigation(SECTION_NAME);
  }
}

if(A::$CACHE->page)
A::$CACHE->page->restore();

A::$MAINFRAME = new ArchiveModule;