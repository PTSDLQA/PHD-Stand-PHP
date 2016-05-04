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
 * Модуль "Каталог материалов".
 *
 * <a href="http://wiki.a-cms.ru/modules/catalog">Руководство</a>.
 */

class CatalogModule extends A_MainFrame
{
/**
 * Идентификатор активной категории.
 */

  public $idcat=0;

/**
 * Данные активной категории.
 */

  public $category;

/**
 * Идентификатор активного материала.
 */

  public $iditem=0;

/**
 * Данные активного материала.
 */

  public $itemdata;

/**
 * Маршрутизатор URL.
 *
 * @param array $uri Элементы полного пути URL.
 */

  function Router($uri)
  {
	foreach($uri as $param)
	if(preg_match("/^(.+)\.html$/",$param,$match))
    { if($this->itemdata=A::$DB->getRow("SELECT * FROM ".SECTION."_catalog WHERE idcat={$this->idcat} AND urlname='{$match[1]}' AND active='Y'"))
	  $this->iditem=$this->itemdata['id'];
	  else
	  A::NotFound();
	  break;
    }
	else
    { if($this->category=A::$DB->getRow("SELECT * FROM ".SECTION."_categories WHERE idker={$this->idcat} AND urlname='{$param}' AND active='Y'"))
      $this->idcat=$this->category['id'];
	  else
      A::NotFound();
    }

	if($this->iditem>0)
	$this->page="page";
	elseif($this->idcat>0)
	$this->page="category";
	else
	$this->page="main";
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
	{ case "addvote": $this->AddVote(); break;
	  case "addcomment": $this->AddComment(); break;
	}
  }

/**
 * Обработчик действия: Оценивание материала.
 */

  function AddVote()
  {
	if(!A_Session::get(SECTION."_vote_".$this->iditem,false))
	{ if($vote=(integer)$_REQUEST['vote'])
	  { A::$DB->execute("UPDATE ".SECTION."_catalog SET cvote=cvote+1,svote=svote+{$vote} WHERE id=".$this->iditem);
	    A_Session::set(SECTION."_vote_".$this->iditem,true);
	    A::goUrl(catalog_createItemLink($this->iditem,SECTION));
	  }
	}

	return false;
  }

/**
 * Обработчик действия: Комментирование материала.
 */

  function AddComment()
  {
	if(!getAccess("comment"))
	return false;

	if(empty($_REQUEST['captcha']) || md5(strtolower($_REQUEST['captcha']))!=A_Session::get('captcha'))
	{ $this->errors['captcha']=true;
	  return false;
	}
	A_Session::unregister('captcha');

    $data=array();
    $data['date']=time();
	$data['idsec']=SECTION_ID;
	$data['iduser']=A::$AUTH->id;
	$data['iditem']=$this->iditem;
	$data['name']=strip_tags($_REQUEST['name']);
    $data['bbcode']=$_REQUEST['message'];
    $data['message']=parse_bbcode($data['bbcode']);
    $data['active']=A::$OPTIONS['cactive']?'N':'Y';

	if(empty($data['name']) || empty($data['message']))
	return false;

    if($id=A::$DB->Insert(DOMAIN."_comments",$data))
	{
	  $count=A::$DB->getCount(DOMAIN."_comments","idsec=".SECTION_ID." AND iditem={$this->iditem}");
	  A::$DB->execute("UPDATE ".SECTION."_catalog SET comments={$count} WHERE id={$this->iditem}");

	  $link=catalog_createItemLink($this->iditem,SECTION);

	  if(!empty(A::$OPTIONS['cemail']))
	  { if(!empty(A::$OPTIONS['commenttpl']))
		{ $item=A::$DB->getRowById($this->iditem,SECTION."_catalog");
	      $item['link']="http://".HOSTNAME.$link;
	      $mail = new A_Mail(A::$OPTIONS['commenttpl'],"html");
		  $mail->Assign("item",$item);
		  $mail->Assign("comment",$data);
	      $mail->send(A::$OPTIONS['cemail']);
		}
	  }

	  if(A::$OPTIONS['cactive'])
	  A::goUrl($link.'?newcomment=1');
	  else
	  A::goUrl($link);
	}
	else
	return false;
  }

/**
 * Формирование данных доступных в шаблоне активного типа.
 */

  function createData()
  {
	switch($this->page)
	{ case "main": $this->MainPage(); break;
	  case "category": $this->CategoryPage(); break;
	  case "page": $this->ItemPage(); break;
	}
  }

/**
 * Добавление полного пути категории в строку навигации.
 *
 * @param integer $id Идентификатор категории.
 */

  function AddNavCategories($id)
  {
    if($id)
	{ if($row=A::$DB->getRow("SELECT id,idker,name FROM ".SECTION."_categories WHERE id=".(integer)$id))
	  { $this->title=$row['name'].(!empty($this->title)?" - ".$this->title:"");
	    $this->AddNavCategories($row['idker']);
	    $this->AddNavigation($row['name'],catalog_createCategoryLink($row['id'],SECTION));
	  }
	}
  }

/**
 * Формирование данных доступных в шаблоне главной страницы раздела.
 */

  function MainPage()
  {
	if(empty($_GET['filter']))
	{ $this->supportCached();
	  $this->addCacheParam_Get('filter');
      $this->addCacheParam_Get('page');
    }

    if(!empty($_GET['idcat']))
	{ if($this->category=A::$DB->getRowById($_GET['idcat'],SECTION."_categories"))
      A::goUrl(catalog_createCategoryLink($this->category['id'],SECTION).'?'.getenv('QUERY_STRING'));
	}

	$levels=A::$DB->getOne("SELECT MAX(level) FROM ".SECTION."_categories WHERE active='Y'");
	$this->Assign("levels",$levels);

	$categories=array();
    A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker=0 AND active='Y' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $row['link']=catalog_createCategoryLink($row['id'],SECTION);
	  $row['subcategories']=array();
      A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker={$row['id']} AND active='Y' ORDER BY sort");
	  while($subrow=A::$DB->fetchRow())
	  { $subrow['link']=catalog_createCategoryLink($subrow['id'],SECTION);
	    $row['subcategories'][]=$subrow;
	  }
	  A::$DB->free();
	  $categories[]=$row;
    }
	A::$DB->free();
	$this->Assign("categories",$categories);

	if(!empty($_GET['sort']))
	A_Session::set(SECTION.'_msort',$_GET['sort']);
	if(!empty($_GET['rows']))
	A_Session::set(SECTION.'_mrows',$_GET['rows']);

	$sort=escape_order_string(A_Session::get(SECTION.'_msort',!empty(A::$OPTIONS['mysort'])?A::$OPTIONS['mysort']:A::$OPTIONS['sort']));
	$rows=(integer)A_Session::get(SECTION.'_mrows',A::$OPTIONS['mrows']);

	$this->Assign("rows",$rows);
	$this->Assign("sort",$sort);

	$where=$this->frontfilter();

	$items=array();
	$pager = new A_Pager($rows);
	if(A::$OPTIONS['mainmode']==0)
	$pager->query("SELECT *,svote/cvote AS vote FROM ".SECTION."_catalog WHERE idcat=0 AND active='Y'{$where} ORDER BY {$sort}");
	else
	$pager->query("SELECT *,svote/cvote AS vote FROM ".SECTION."_catalog WHERE active='Y'{$where} ORDER BY {$sort}");

 	while($row=$pager->fetchRow())
    { $row['link']=catalog_createItemLink($row['id'],SECTION);
      $row['vote']=round($row['vote'],2);
	  if(A::$OPTIONS['mainmode']==1)
	  $row['category']=getTreePath(SECTION."_categories",$row['idcat']);
	  if(A::$OPTIONS['useimages'])
	  { $row['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$row['id']));
	    $row['idimg']=isset($row['images'][0]['id'])?$row['images'][0]['id']:0;
	  }
	  if(A::$OPTIONS['usefiles'])
	  { $row['files']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_files WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$row['id']));
	    foreach($row['files'] as $i=>$data)
	    { $row['files'][$i]['link']=(LANG==DEFAULTLANG?"":"/".LANG)."/getfile/".$data['id']."/".$data['name'];
	      $row['files'][$i]['size']=sizestring($data['size']);
	    }
	    $row['idfile']=isset($row['files'][0]['id'])?$row['files'][0]['id']:0;
	  }
	  if(A::$OPTIONS['usetags'])
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  prepareValues(SECTION,$row);
	  $row=A::$OBSERVER->Modifier('catalog_prepareValues',SECTION,$row);
      $items[]=$row;
    }
    $pager->free();

	$this->Assign("items",$items);
	$this->Assign("items_pager",$pager);

	$this->AddNavigation(SECTION_NAME);
  }

/**
 * Формирование данных доступных в шаблоне страницы категории.
 */

  function CategoryPage()
  {
    if(empty($_GET['filter']))
	{ $this->supportCached();
	  $this->addCacheParam_Get('filter');
      $this->addCacheParam_Get('page');
    }

    if(A::$OPTIONS['usetags'])
	$this->category['tags']=A_SearchEngine::getInstance()->convertTags($this->category['tags']);
	$this->category=A::$OBSERVER->Modifier('fcategory_prepareValues',SECTION,$this->category);
    $this->Assign("category",$this->category);

	$categories=array();
    A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker={$this->idcat} AND active='Y' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $row['link']=catalog_createCategoryLink($row['id'],SECTION);
	  $row['subcategories']=array();
      A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker={$row['id']} AND active='Y' ORDER BY sort");
	  while($subrow=A::$DB->fetchRow())
	  { $subrow['link']=catalog_createCategoryLink($subrow['id'],SECTION);
	    $row['subcategories'][]=$subrow;
	  }
	  A::$DB->free();
	  $categories[]=$row;
    }
	A::$DB->free();
	$this->Assign("categories",$categories);

	if(!empty($_REQUEST['sort']))
	A_Session::set(SECTION.'_csort',$_REQUEST['sort']);
	if(!empty($_REQUEST['rows']))
	A_Session::set(SECTION.'_crows',$_REQUEST['rows']);

	$sort=escape_order_string(A_Session::get(SECTION.'_csort',!empty(A::$OPTIONS['mysort'])?A::$OPTIONS['mysort']:A::$OPTIONS['sort']));
	$rows=(integer)A_Session::get(SECTION.'_crows',A::$OPTIONS['crows']);

	$this->Assign("rows",$rows);
	$this->Assign("sort",$sort);

	$where=$this->frontfilter();

	$items=array();
	$pager = new A_Pager($rows);
	$pager->query("SELECT *,svote/cvote AS vote FROM ".SECTION."_catalog WHERE idcat={$this->idcat} AND active='Y'{$where} ORDER BY {$sort}");
	while($row=$pager->fetchRow())
    { $row['link']=catalog_createItemLink($row['id'],SECTION);
	  $row['category']=getTreePath(SECTION."_categories",$row['idcat']);
      $row['vote']=round($row['vote'],2);
	  if(A::$OPTIONS['useimages'])
	  { $row['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$row['id']));
	    $row['idimg']=isset($row['images'][0]['id'])?$row['images'][0]['id']:0;
	  }
	  if(A::$OPTIONS['usefiles'])
	  { $row['files']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_files WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$row['id']));
	    foreach($row['files'] as $i=>$data)
	    { $row['files'][$i]['link']=(LANG==DEFAULTLANG?"":"/".LANG)."/getfile/".$data['id']."/".$data['name'];
	      $row['files'][$i]['size']=sizestring($data['size']);
	    }
	    $row['idfile']=isset($row['files'][0]['id'])?$row['files'][0]['id']:0;
	  }
	  if(A::$OPTIONS['usetags'])
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  prepareValues(SECTION,$row);
	  $row=A::$OBSERVER->Modifier('catalog_prepareValues',SECTION,$row);
      $items[]=$row;
    }
    $pager->free();

	$this->Assign("items",$items);
	$this->Assign("items_pager",$pager);

	$this->AddNavigation(SECTION_NAME,getSectionLink(SECTION));
	$this->AddNavCategories($this->category['idker']);
	$this->AddNavigation($this->category['name']);

	$this->title=$this->category['name'].(!empty($this->title)?" - ".$this->title:"");
	$this->description=$this->category['description'];
  }

/**
 * Формирование данных доступных в шаблоне детальной страницы материала.
 */

  function ItemPage()
  {
	$this->supportCached();

	if($this->idcat>0)
	{ $this->category['link']=catalog_createCategoryLink($this->category['id'],SECTION);
	  if(A::$OPTIONS['usetags'])
	  $this->category['tags']=A_SearchEngine::getInstance()->convertTags($this->category['tags']);
	  $this->category=A::$OBSERVER->Modifier('fcategory_prepareValues',SECTION,$this->category);
	  $this->Assign("category",$this->category);
	}

	$itemdata=$this->itemdata;

	$itemdata['vote']=$itemdata['cvote']>0?round($itemdata['svote']/$itemdata['cvote'],2):0;

	if(A::$OPTIONS['useimages'])
	{ $itemdata['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$itemdata['id']));
	  $itemdata['idimg']=isset($itemdata['images'][0]['id'])?$itemdata['images'][0]['id']:0;
	}

	if(A::$OPTIONS['usefiles'])
	{ $itemdata['files']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_files WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$this->iditem));
	  foreach($itemdata['files'] as $i=>$data)
	  { $itemdata['files'][$i]['link']=(LANG==DEFAULTLANG?"":"/".LANG)."/getfile/".$data['id']."/".$data['name'];
	    $itemdata['files'][$i]['size']=sizestring($data['size']);
	  }
	  $itemdata['idfile']=isset($itemdata['files'][0]['id'])?$itemdata['files'][0]['id']:0;
	}

    if(A::$OPTIONS['usetags'])
	$itemdata['tags']=A_SearchEngine::getInstance()->convertTags($itemdata['tags']);

	prepareValues(SECTION,$itemdata);
	$itemdata=A::$OBSERVER->Modifier('catalog_prepareValues',SECTION,$itemdata);

    $this->Assign("item",$itemdata);

	if(A::$OPTIONS['usecomments'])
	{ $comments=array();
	  A::$DB->query("SELECT * FROM ".DOMAIN."_comments WHERE idsec=".SECTION_ID." AND iditem={$this->iditem} AND active='Y' ORDER BY date");
	  while($row=A::$DB->fetchRow())
	  { if($row['iduser']>0 && A::$AUTH->section)
	    { if($row['user']=A::$DB->getRowById($row['iduser'],A::$AUTH->section))
	      prepareValues(A::$AUTH->section,$row['user']);
	    }
	    $comments[]=$row;
	  }
	  A::$DB->free();
	  $this->Assign("comments",$comments);

	  $form=array();
 	  $form['name']=!empty($_REQUEST['name'])?$_REQUEST['name']:(A::$AUTH->isLogin()?A::$AUTH->data['name']:"");
	  $form['message']=!empty($_REQUEST['message'])?$_REQUEST['message']:"";
	  $this->Assign("form",$form);

	  $this->Assign("captcha",$captcha=substr(time(),rand(0,6),4));
	  A_Session::set("captcha",md5($captcha));
	}

	if(A::$OPTIONS['usevote'])
    { $this->Assign("isvote",A_Session::get(SECTION."_vote_".$this->iditem,false));
      $this->addCacheParam_Session(SECTION."_vote_".$this->iditem);
    }

	$this->AddNavigation(SECTION_NAME,getSectionLink(SECTION));
	if(isset($this->category))
	$this->AddNavCategories($this->category['id']);

    $this->title=$this->itemdata['name'].(!empty($this->title)?" - ".$this->title:"");
	if(!empty($this->itemdata['keywords']))
	$this->keywords=$this->itemdata['keywords'];
    $this->description=$this->itemdata['description'];
  }
}

if(A::$CACHE->page)
A::$CACHE->page->restore();

A::$MAINFRAME = new CatalogModule;