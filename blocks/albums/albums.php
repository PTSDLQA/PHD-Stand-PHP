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
 * Блок "Список альбомов".
 *
 * <a href="http://wiki.a-cms.ru/blocks/albums">Руководство</a>.
 */

class albums_Block extends A_Block
{
/**
 * Специальная обработка параметров блока.
 *
 * @return array
 */

  function prepareParams()
  {
    $params=array();
	foreach($_REQUEST as $name=>$value)
	if(preg_match("/^b_(.+)$/i",$name,$matches))
	{ if($matches[1]=='filter' && !A::$AUTH->isSuperAdmin())
	  continue;
	  $params[$matches[1]]=$value;
	}
	return $params;
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	if($this->params['sort']!=5)
	$this->supportCached();

	$this->params['idcat']=(integer)$this->params['idcat'];
	$this->params['rows']=(integer)$this->params['rows'];

	if($this->params['idcat'])
	{ $catrow=A::$DB->getRowById($this->params['idcat'],"{$this->section}_categories");
	  $catrow['link']=gallery_createCategoryLink($this->params['idcat'],$this->section);
	  $this->Assign("category",$catrow);
	}

	switch($this->params['sort'])
	{ default:
	  case 1: $sort="date DESC"; break;
	  case 2: $sort="date"; break;
	  case 3: $sort="name"; break;
	  case 4: $sort="sort"; break;
	  case 5: $sort="RAND()"; break;
	}

	$sql="
	SELECT *,svote/cvote AS vote FROM {$this->section}_albums
	WHERE active='Y'".($this->params['idcat']?" AND idcat={$this->params['idcat']}":"").
	(!empty($this->params['filter'])?" AND {$this->params['filter']}":"")."
	ORDER BY $sort";

	if($this->params['rows'])
	A::$DB->queryLimit($sql,0,$this->params['rows']);
	else
	A::$DB->query($sql);

    $albums=array();
	while($row=A::$DB->fetchRow())
    { $row['category']=getTreePath($this->section."_categories",$row['idcat']);
	  $row['link']=gallery_createItemLink($row['id'],$this->section);
	  $row['vote']=round($row['vote'],2);
	  $row['images']=A::$DB->getAll("
	  SELECT * FROM ".getDomain($this->section)."_images
	  WHERE idsec=? AND iditem=? ORDER BY sort",array($this->section_id,$row['id']));
	  if($this->options['usetags'])
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  prepareValues($this->section,$row);
	  $row=A::$OBSERVER->Modifier('gallery_prepareValues',$this->section,$row);
      $albums[]=$row;
    }
	A::$DB->free();

	$this->Assign("albums",$albums);
  }
}