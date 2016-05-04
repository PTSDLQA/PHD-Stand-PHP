<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Blocks
 */
/**************************************************************************/

class categoriestree_Block extends A_Block
{
  function createData()
  {
	$this->supportCached(true);

	$module=getModuleBySection($this->section);
	$idcat=(integer)$this->params['idcat'];
	$cpath=array();

	if($this->section==SECTION && A::$MAINFRAME->idcat>0)
	{ $catrow=A::$DB->getRowById(A::$MAINFRAME->idcat,"{$this->section}_categories");
	  $this->Assign("category",$catrow);
	  $cpath[]=$catrow['id'];
	  while($catrow && $catrow['idker']>0)
	  { $cpath[]=$catrow['idker'];
	    $catrow=A::$DB->getRowById($catrow['idker'],"{$this->section}_categories");
	  }
	}

	$categories=array();
	A::$DB->query("SELECT id,idker,name FROM {$this->section}_categories ORDER BY level,sort");
	while($row=A::$DB->fetchRow())
	{ $row['name']=addcslashes($row['name'],"'");
	  $row['link']=call_user_func($module."_createCategoryLink",$row['id'],$this->section);
	  $row['selected']=$this->section==SECTION && in_array($row['id'],$cpath);
	  $categories[]=$row;
	}
	A::$DB->free();

	$this->Assign("categories",$categories);
  }
}