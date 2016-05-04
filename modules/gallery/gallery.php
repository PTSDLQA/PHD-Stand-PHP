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
 * Модуль "Фотогалерея".
 *
 * <a href="http://wiki.a-cms.ru/modules/gallery">Руководство</a>.
 */

class GalleryModule extends A_MainFrame
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
 * Идентификатор активного альбома.
 */

  public $idalb=0;

/**
 * Данные активного альбома.
 */

  public $album;

/**
 * Маршрутизатор URL.
 *
 * @param array $uri Элементы полного пути URL.
 */

  function Router($uri)
  {
	foreach($uri as $param)
	if(preg_match("/^(.+)\.html$/",$param,$match))
    { if($this->album=A::$DB->getRow("SELECT * FROM ".SECTION."_albums WHERE idcat={$this->idcat} AND urlname='{$match[1]}' AND active='Y'"))
	  $this->idalb=$this->album['id'];
	  else
	  A::NotFound();
	  break;
    }
	else
    { if($this->category=A::$DB->getRow("
	  SELECT * FROM ".SECTION."_categories WHERE idker={$this->idcat} AND urlname='{$param}' AND active='Y'"))
      $this->idcat=$this->category['id'];
	  else
      A::NotFound();
    }

	if($this->idalb>0)
	$this->page="album";
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
 * Обработчик действия: Оценивание альбома.
 */

  function AddVote()
  {
	if(!A_Session::get(SECTION."_vote_".$this->idalb,false))
	{ if($vote=(integer)$_REQUEST['vote'])
	  { A::$DB->execute("UPDATE ".SECTION."_albums SET cvote=cvote+1,svote=svote+{$vote} WHERE id=".$this->idalb);
	    A_Session::set(SECTION."_vote_".$this->idalb,true);
	    A::goUrl(gallery_createItemLink($this->idalb,SECTION));
	  }
	}

	return false;
  }

/**
 * Обработчик действия: Комментирование альбома.
 */

  function AddComment()
  {
	if(!getAccess("comment")) return false;

	if(empty($_REQUEST['captcha']) || md5(strtolower($_REQUEST['captcha']))!=A_Session::get('captcha'))
	{ $this->errors['captcha']=true;
	  return false;
	}
	A_Session::unregister('captcha');

    $data=array();
    $data['date']=time();
	$data['idsec']=SECTION_ID;
	$data['iduser']=A::$AUTH->id;
	$data['iditem']=$this->idalb;
	$data['name']=strip_tags($_REQUEST['name']);
    $data['bbcode']=$_REQUEST['message'];
    $data['message']=parse_bbcode($data['bbcode']);
    $data['active']=A::$OPTIONS['cactive']?'N':'Y';

	if(empty($data['name']) || empty($data['message']))
	return false;

    if($id=A::$DB->Insert(DOMAIN."_comments",$data))
	{
	  $count=A::$DB->getCount(DOMAIN."_comments","idsec=".SECTION_ID." AND iditem={$this->idalb}");
	  A::$DB->execute("UPDATE ".SECTION."_albums SET comments={$count} WHERE id={$this->idalb}");

	  $link=gallery_createItemLink($this->idalb,SECTION);

	  if(!empty(A::$OPTIONS['cemail']))
	  { if(!empty(A::$OPTIONS['commenttpl']))
		{ $item=A::$DB->getRowById($this->idalb,SECTION."_albums");
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
	{ default:
	  case "main": $this->MainPage(); break;
	  case "category": $this->CategoryPage(); break;
	  case "album": $this->AlbumPage(); break;
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
	    $this->AddNavigation($row['name'],gallery_createCategoryLink($row['id'],SECTION));
	  }
	}
  }

/**
 * Формирование данных доступных в шаблоне главной страницы раздела.
 */

  function MainPage()
  {
    $this->supportCached();
    $this->addCacheParam_Get('page');

	$levels=A::$DB->getOne("SELECT MAX(level) FROM ".SECTION."_categories WHERE active='Y'");
	$this->Assign("levels",$levels);

	$categories=array();
    A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker=0 AND active='Y' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $row['link']=gallery_createCategoryLink($row['id'],SECTION);
	  $row['subcategories']=array();
      A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker={$row['id']} AND active='Y' ORDER BY sort");
	  while($subrow=A::$DB->fetchRow())
	  { $subrow['link']=gallery_createCategoryLink($subrow['id'],SECTION);
	    $row['subcategories'][]=$subrow;
	  }
	  A::$DB->free();
	  $categories[]=$row;
    }
	A::$DB->free();
	$this->Assign("categories",$categories);

	$rows=(integer)!empty($_GET['rows'])?(integer)$_GET['rows']:A::$OPTIONS['mrows'];
	$irows=(integer)!empty($_GET['irows'])?(integer)$_GET['irows']:A::$OPTIONS['irows'];
	$sort=escape_order_string(!empty(A::$OPTIONS['mysort'])?A::$OPTIONS['mysort']:A::$OPTIONS['sort']);

	$this->Assign("rows",$rows);
	$this->Assign("irows",$irows);
	$this->Assign("sort",$sort);

	$where=$this->frontfilter();

	$albums=array();
	$pager = new A_Pager($rows);
	if(A::$OPTIONS['mainmode']==0)
	$pager->query("SELECT *,svote/cvote AS vote FROM ".SECTION."_albums WHERE idcat=0 AND active='Y'{$where} ORDER BY {$sort}");
	else
	$pager->query("SELECT *,svote/cvote AS vote FROM ".SECTION."_albums WHERE active='Y'{$where} ORDER BY {$sort}");

 	while($row=$pager->fetchRow())
    { $row['link']=gallery_createItemLink($row['id'],SECTION);
      $row['vote']=round($row['vote'],2);
	  if(A::$OPTIONS['mainmode']==1)
	  $row['category']=getTreePath(SECTION."_categories",$row['idcat']);
	  $row['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$row['id']));
	  if(A::$OPTIONS['usetags'])
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  prepareValues(SECTION,$row);
	  $row=A::$OBSERVER->Modifier('gallery_prepareValues',SECTION,$row);
      $albums[]=$row;
    }
    $pager->free();

	$this->Assign("albums",$albums);
	$this->Assign("albums_pager",$pager);

	$pager = new A_Pager($irows);
	$images=$pager->setItems(!empty($albums)?$albums[0]['images']:array());
	$this->Assign("images",$images);
	$this->Assign("images_pager",$pager);

	$this->AddNavigation(SECTION_NAME);
  }

/**
 * Формирование данных доступных в шаблоне страницы категории.
 */

  function CategoryPage()
  {
    $this->supportCached();
    $this->addCacheParam_Get('page');

    if(A::$OPTIONS['usetags'])
	$this->category['tags']=A_SearchEngine::getInstance()->convertTags($this->category['tags']);
	$this->Assign("category",$this->category);

	$categories=array();
    A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker={$this->idcat} AND active='Y' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $row['link']=gallery_createCategoryLink($row['id'],SECTION);
	  $row['subcategories']=array();
      A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker={$row['id']} AND active='Y' ORDER BY sort");
	  while($subrow=A::$DB->fetchRow())
	  { $subrow['link']=gallery_createCategoryLink($subrow['id'],SECTION);
	    $row['subcategories'][]=$subrow;
	  }
	  A::$DB->free();
	  $categories[]=$row;
    }
	A::$DB->free();
	$this->Assign("categories",$categories);

	$rows=(integer)!empty($_GET['rows'])?(integer)$_GET['rows']:A::$OPTIONS['crows'];
	$irows=(integer)!empty($_GET['irows'])?(integer)$_GET['irows']:A::$OPTIONS['irows'];
	$sort=escape_order_string(!empty(A::$OPTIONS['mysort'])?A::$OPTIONS['mysort']:A::$OPTIONS['sort']);

	$this->Assign("rows",$rows);
	$this->Assign("irows",$irows);
	$this->Assign("sort",$sort);

	$where=$this->frontfilter();

	$albums=array();
	$pager = new A_Pager($rows);
	$pager->query("SELECT *,svote/cvote AS vote FROM ".SECTION."_albums	WHERE idcat={$this->idcat} AND active='Y'{$where} ORDER BY {$sort}");
	while($row=$pager->fetchRow())
    { $row['link']=gallery_createItemLink($row['id'],SECTION);
      $row['category']=getTreePath(SECTION."_categories",$row['idcat']);
      $row['vote']=round($row['vote'],2);
	  $row['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$row['id']));
	  if(A::$OPTIONS['usetags'])
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  prepareValues(SECTION,$row);
	  $row=A::$OBSERVER->Modifier('gallery_prepareValues',SECTION,$row);
      $albums[]=$row;
    }
    $pager->free();

	$this->Assign("albums",$albums);
	$this->Assign("albums_pager",$pager);

	$pager = new A_Pager($irows);
	$images=$pager->setItems(!empty($albums)?$albums[0]['images']:array());
	$this->Assign("images",$images);
	$this->Assign("images_pager",$pager);

	$this->AddNavigation(SECTION_NAME,getSectionLink(SECTION));
	$this->AddNavCategories($this->category['idker']);

	$this->title=$this->category['name'].(!empty($this->title)?" - ".$this->title:"");
	$this->description=$this->category['description'];
  }

/**
 * Формирование данных доступных в шаблоне страницы альбома.
 */

  function AlbumPage()
  {
	$this->supportCached();
	$this->addCacheParam_Get('page');

	if($this->idcat>0)
	{ $this->category['link']=gallery_createCategoryLink($this->category['id'],SECTION);
	  if(A::$OPTIONS['usetags'])
	  $this->category['tags']=A_SearchEngine::getInstance()->convertTags($this->category['tags']);
	  $this->Assign("category",$this->category);
	}

	$album=$this->album;
	$album['vote']=$album['cvote']>0?round($album['svote']/$album['cvote'],2):0;
	$album['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$album['id']));

    if(A::$OPTIONS['usetags'])
	$album['tags']=A_SearchEngine::getInstance()->convertTags($album['tags']);

	prepareValues(SECTION,$album);
	$album=A::$OBSERVER->Modifier('gallery_prepareValues',SECTION,$album);

    $this->Assign("album",$album);

	$irows=(integer)!empty($_GET['irows'])?(integer)$_GET['irows']:A::$OPTIONS['irows'];
	$this->Assign("irows",$irows);

    $pager = new A_Pager($irows);
	$images=$pager->setItems($album['images']);
	$this->Assign("images",$images);
	$this->Assign("images_pager",$pager);

	$sort=escape_order_string(!empty(A::$OPTIONS['mysort'])?A::$OPTIONS['mysort']:A::$OPTIONS['sort']);
	$albums=A::$DB->getCol("
	SELECT id FROM ".SECTION."_albums
	WHERE active='Y' AND idcat={$album['idcat']} ORDER BY $sort");
    $i=array_search($album['id'],$albums);
    if($i!==false)
    { $previd=isset($albums[$i-1])?$albums[$i-1]:0;
      $nextid=isset($albums[$i+1])?$albums[$i+1]:0;
      if($previd)
      $this->Assign("prevalbum",gallery_createItemLink($previd,SECTION));
      if($nextid)
      $this->Assign("nextalbum",gallery_createItemLink($nextid,SECTION));
    }

	if(A::$OPTIONS['usecomments'])
	{ $comments=array();
	  A::$DB->query("SELECT * FROM ".DOMAIN."_comments WHERE idsec=".SECTION_ID." AND iditem={$this->idalb} AND active='Y' ORDER BY date");
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
    { $this->Assign("isvote",A_Session::get(SECTION."_vote_".$this->idalb,false));
      $this->addCacheParam_Session(SECTION."_vote_".$this->idalb);
    }

	$this->AddNavigation(SECTION_NAME,getSectionLink(SECTION));
	if(isset($this->category))
	$this->AddNavCategories($this->category['id']);

    $this->title=$this->album['name'].(!empty($this->title)?" - ".$this->title:"");
    $this->description=$this->album['description'];
  }
}

if(A::$CACHE->page)
A::$CACHE->page->restore();

A::$MAINFRAME = new GalleryModule;