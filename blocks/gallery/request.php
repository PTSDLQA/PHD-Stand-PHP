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
 * Серверная сторона AJAX настройки блока "Список фото из галереи".
 *
 * <a href="http://wiki.a-cms.ru/blocks/gallery">Руководство</a>.
 */

class gallery_BlockRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "add": $this->Add(); break;
	  case "edit": $this->Edit(); break;
	  case "getalbums": $this->getAlbums(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму добавления.
 */

  function Add()
  {
	$form = new A_Form("block_gallery_add.tpl");
	$form->data['sections']=A::$DB->getAssoc("SELECT id,caption FROM ".DOMAIN."_sections WHERE module='gallery' ORDER BY sort");
	$section=getSectionById(key($form->data['sections']));
	$form->data['categories']=array();
	$this->getCategories($section,$form->data['categories'],0);
	$form->data['albums']=A::$DB->getAssoc("SELECT id,name FROM {$section}_albums WHERE idcat=0 ORDER BY name");
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования.
 */

  function Edit()
  {
	$form = new A_Form("block_gallery_edit.tpl");
	$block=A::$DB->getRowById($_POST['id'],DOMAIN."_blocks");
	$form->data=!empty($block['params'])?unserialize($block['params']):array();
	$form->data['sections']=A::$DB->getAssoc("SELECT id,caption FROM ".DOMAIN."_sections WHERE module='gallery' ORDER BY sort");
	$section=getSectionById($form->data['idsec']);
	$form->data['categories']=array();
	$this->getCategories($section,$form->data['categories'],0);
	$idcat=!empty($form->data['idcat'])?(integer)$form->data['idcat']:0;
	$form->data['albums']=A::$DB->getAssoc("SELECT id,name FROM {$section}_albums WHERE idcat=$idcat ORDER BY name");
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает список альбомов.
 */

  function getAlbums()
  {
    $section=getSectionById($_POST['idsec']);
    $idcat=!empty($_POST['idcat'])?(integer)$_POST['idcat']:0;
    $albums=A::$DB->getAssoc("SELECT id,name FROM {$section}_albums WHERE idcat=$idcat ORDER BY name");
	$this->RESULT['ids']=array();
	$this->RESULT['names']=array();
	foreach($albums as $id=>$name)
	{ $this->RESULT['ids'][]=$id;
	  $this->RESULT['names'][]=$name;
	}
  }

  private function getCategories($section,&$values,$id,$owner="")
  {
    A::$DB->query("SELECT * FROM {$section}_categories WHERE idker=$id ORDER BY sort");
	if(A::$DB->numRows())
	{ if(!empty($owner))
	  $owner.=" > ";
	  while($row=A::$DB->fetchRow())
      { $values[$row['id']]=$owner.$row['name'];
        $this->getCategories($section,$values,$row['id'],$owner.$row['name']);
      }
	}
	A::$DB->free();
  }
}

A::$REQUEST = new gallery_BlockRequest;