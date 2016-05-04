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
 * Блок "Список категорий".
 *
 * <a href="http://wiki.a-cms.ru/blocks/categories">Руководство</a>.
 */

class categories_Block extends A_Block
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$this->supportCached(true);

	$module=getModuleBySection($this->section);
	$idcat=(integer)$this->params['idcat'];
	$cpath=array();

	if($this->section==SECTION && A::$MAINFRAME->idcat>0)
	{ $catrow=A::$DB->getRowById(A::$MAINFRAME->idcat,"{$this->section}_categories");
	  if(isset($this->params['curcheck']))
	  { if(A::$DB->getCount("{$this->section}_categories","idker=".A::$MAINFRAME->idcat))
	    $idcat=A::$MAINFRAME->idcat;
	    else
	    $idcat=$catrow['idker'];
	  }
	  $this->Assign("category",$catrow);
	  $cpath[]=$catrow['id'];
	  while($catrow && $catrow['idker']>0)
	  { $cpath[]=$catrow['idker'];
	    $catrow=A::$DB->getRowById($catrow['idker'],"{$this->section}_categories");
	  }
	}

	$levels=A::$DB->getOne("SELECT MAX(level) FROM {$this->section}_categories WHERE active='Y'");
	$this->Assign("levels",$levels);

	if(!empty($this->params['rows']))
    A::$DB->queryLimit("SELECT * FROM {$this->section}_categories WHERE idker={$idcat} AND active='Y' ORDER BY sort",0,(integer)$this->params['rows']);
	else
	A::$DB->query("SELECT * FROM {$this->section}_categories WHERE idker={$idcat} AND active='Y' ORDER BY sort");
	$links=array();
	while($row=A::$DB->fetchRow())
	{ $row['link']=call_user_func($module."_createCategoryLink",$row['id'],$this->section);
	  $row['selected']=$this->section==SECTION && in_array($row['id'],$cpath);
	  $row['sublinks']=array();
	  A::$DB->query("SELECT * FROM {$this->section}_categories WHERE idker={$row['id']} AND active='Y' ORDER BY sort");
	  while($subrow=A::$DB->fetchRow())
	  { $subrow['link']=call_user_func($module."_createCategoryLink",$subrow['id'],$this->section);
		$subrow['selected']=$this->section==SECTION && in_array($subrow['id'],$cpath);
	    $row['sublinks'][]=$subrow;
	  }
	  A::$DB->free();
	  $row['subcategories']=$row['sublinks'];
	  $links[]=$row;
	}
	A::$DB->free();

	$this->Assign("links",$links);
	$this->Assign("categories",$links);
  }
}