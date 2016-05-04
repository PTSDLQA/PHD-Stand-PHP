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
 * Блок "Список ссылок на страницы".
 *
 * <a href="http://wiki.a-cms.ru/blocks/pages">Руководство</a>.
 */

class pages_Block extends A_Block
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$this->supportCached(true);

	$this->params['idcat']=(integer)$this->params['idcat'];

	if(isset($this->params['curcheck']) && $this->section==SECTION && A::$MAINFRAME->id>0)
	$this->params['idcat']=A::$MAINFRAME->idker;

    $links=array();
	A::$DB->query("SELECT * FROM {$this->section} WHERE idker={$this->params['idcat']} AND active='Y' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ if($row['urlname']=='index') continue;
	  $row['link']=pages_createItemLink($row['id'],$this->section);
	  $row['selected']=$this->section==SECTION && ($row['id']==A::$MAINFRAME->id || ($row['type']=='dir' && $row['id']==A::$MAINFRAME->idker));
	  $row['subindex']=false;
	  if($row['type']=='dir')
	  { $row['sublinks']=array();
	    A::$DB->query("SELECT id,name,urlname,type,level,content FROM {$this->section} WHERE idker={$row['id']} AND active='Y' ORDER BY sort");
		while($subrow=A::$DB->fetchRow())
		{ if($subrow['urlname']=='index')
		  { $row['subindex']=true;
		    continue;
		  }
		  $subrow['link']=pages_createItemLink($subrow['id'],$this->section);
		  $subrow['selected']=$this->section==SECTION && $subrow['id']==A::$MAINFRAME->id;
		  $row['sublinks'][]=$subrow;
		}
		A::$DB->free();
	  }
	  elseif($this->options['usetags'])
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  prepareValues($this->section,$row);
	  $links[]=$row;
	}
	A::$DB->free();

	$this->Assign("links",$links);
  }
}