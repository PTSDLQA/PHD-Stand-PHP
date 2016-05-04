<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Blocks
 */
/**************************************************************************/

/**
 * Блок "Список фото из галереи".
 *
 * <a href="http://wiki.a-cms.ru/blocks/gallery">Руководство</a>.
 */

class gallery_Block extends A_Block
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	if($this->params['sort']!=2)
	$this->supportCached();

	switch($this->params['sort'])
	{ default:
	  case 1: $sort="sort"; break;
	  case 2: $sort="RAND()"; break;
	}

	$where="i.idsec=$this->section_id";
	if($this->params['idalb']>0)
	$where.=" AND i.iditem=".(integer)$this->params['idalb'];
	elseif($this->params['idcat']>0)
	{ if($idalbs=A::$DB->getCol("SELECT id FROM {$this->section}_albums WHERE idcat=".(integer)$this->params['idcat']))
	  $where.=" AND (i.iditem=".implode(" OR i.iditem=",$idalbs).")";
	}

	$images=array();
	A::$DB->query("
	SELECT i.* FROM ".getDomain($this->section)."_images AS i
	LEFT JOIN {$this->section}_albums AS a ON a.id=i.iditem
	WHERE $where AND a.active='Y' ORDER BY $sort".(!empty($this->params['rows'])?" LIMIT 0,".(integer)$this->params['rows']:""));
	while($row=A::$DB->fetchRow())
	{ if(isset($links[$row['iditem']]))
	  $row['link']=$links[$row['iditem']];
	  else
	  $row['link']=$links[$row['iditem']]=gallery_createItemLink($row['iditem'],$this->section);
	  $images[]=$row;
	}

	$this->Assign("images",$images);
  }
}