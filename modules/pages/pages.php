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
 * Модуль "Страницы".
 *
 * <a href="http://wiki.a-cms.ru/modules/pages">Руководство</a>.
 */

class PagesModule extends A_MainFrame
{
/**
 * Идентификатор активной страницы.
 */

  public $id=0;

/**
 * Идентификатор родительского подраздела.
 */

  public $idker=0;

/**
 * Список строковых идентификаторов полного пути.
 */

  public $fullpath=array();

/**
 * Данные активной страницы.
 */

  public $pagedata;

/**
 * Маршрутизатор URL.
 *
 * @param array $uri Элементы полного пути URL.
 */

  function Router($uri)
  {
	foreach($uri as $param)
	{ if(preg_match("/^(.+)\.html$/",$param,$match))
	  { if($this->pagedata=A::$DB->getRow("SELECT * FROM ".SECTION." WHERE idker={$this->id} AND type='page' AND urlname='{$match[1]}' AND active='Y'"))
        { $this->id=$id=$this->pagedata['id'];
		  $this->idker=$this->pagedata['idker'];
		}
        else
	    A::NotFound();
	  }
	  else
	  { if($this->pagedata=A::$DB->getRow("SELECT * FROM ".SECTION." WHERE idker={$this->id} AND type='dir' AND urlname='{$param}' AND active='Y'"))
        { $this->id=$this->pagedata['id'];
		  $this->idker=$this->pagedata['id'];
		  $this->fullpath[]=$param;
		}
        else
	    A::NotFound();
	  }
	}

	$idker=$this->id;

	if(!isset($id))
	{ if($this->pagedata=A::$DB->getRow("SELECT * FROM ".SECTION." WHERE idker={$idker} AND type='page' AND urlname='index' AND active='Y'"))
	  $this->id=$id=$this->pagedata['id'];
	}
	if(!isset($id))
	{ if($this->pagedata=A::$DB->getRow("SELECT * FROM ".SECTION." WHERE idker={$idker} AND type='page' AND active='Y' ORDER BY sort LIMIT 0,1"))
	  $this->id=$this->pagedata['id'];
	}

	if(!$this->id || empty($this->pagedata))
	A::NotFound();

	$this->template=$this->pagedata['template'];
  }

  function AddNavPages($id,$fl)
  {
    if($row=A::$DB->getRow("SELECT id,idker,name FROM ".SECTION." WHERE id=".(integer)$id))
	{ if($row['idker'])
	  $this->AddNavPages($row['idker'],true);
	  if($fl)
	  { if(!empty($row['name']))
		$this->AddNavigation($row['name'],pages_createItemLink($row['id'],SECTION));
		$this->title=$row['name'].(!empty($this->title)?' - '.$this->title:'');
	  }
	  else
	  { if(!empty($row['name']))
	    $this->AddNavigation($row['name']);
	  }
	}
  }

/**
 * Формирование данных доступных в шаблоне активного типа.
 */

  function createData()
  {
	$this->supportCached();

	prepareValues(SECTION,$this->pagedata);

	$this->pagedata['latname']=$this->pagedata['urlname'];

	if(A::$OPTIONS['usetags'])
	$this->pagedata['tags']=A_SearchEngine::getInstance()->convertTags($this->pagedata['tags']);

	$this->Assign_by_ref("page",$this->pagedata);
	$this->Assign_by_ref("content",$this->pagedata['content']);
	$this->Assign_by_ref("fullpath",$this->fullpath);

	if($this->pagedata['level'])
	{ if(SNAME!=A::$OPTIONS['mainsection'])
	  $this->AddNavigation(SECTION_NAME,getSectionLink(SECTION));
	  if($this->pagedata['idker'])
	  $this->AddNavPages($this->pagedata['idker'],$this->pagedata['urlname']!="index");
	}
	elseif(SNAME!=A::$OPTIONS['mainsection'])
	{ if($this->pagedata['urlname']!='index')
	  $this->AddNavigation(SECTION_NAME,getSectionLink(SECTION));
	  else
	  $this->AddNavigation(SECTION_NAME);
	}

	if(!empty($this->pagedata['title']))
	$this->title=$this->pagedata['title'];
	if(!(A::$OPTIONS['mainsection'] && count(A::$URIPARAMS)==0))
	$this->title=$this->pagedata['name'].(!empty($this->title)?' - '.$this->title:'');
	$this->keywords=$this->pagedata['keywords'];
    $this->description=$this->pagedata['description'];
  }
}

if(A::$CACHE->page)
A::$CACHE->page->restore();

A::$MAINFRAME = new PagesModule;