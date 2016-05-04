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
 * Серверная сторона AJAX настройки блока "Список категорий".
 *
 * <a href="http://wiki.a-cms.ru/blocks/categories">Руководство</a>.
 */

class categories_BlockRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "add": $this->Add(); break;
	  case "edit": $this->Edit(); break;
	  case "getcategories": $this->getCategories(); break;
	  case "getownercategories": $this->getOwnerCategories(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму добавления.
 */

  function Add()
  {
	$form = new A_Form("block_categories_add.tpl");
	$tables=A::$DB->getTables();
	$form->data['sections']=array();
	A::$DB->query("SELECT * FROM ".DOMAIN."_sections ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $section=DOMAIN."_".$row['lang']."_".$row['name'];
	  $usecats=$row['module']!='shoplite'?A::$DB->existsRow("SELECT id FROM ".DOMAIN."_options WHERE item='{$section}' AND var='usecats'"):false;
	  if(in_array($section."_categories",$tables) && (!$usecats || getOption($section,'usecats')))
	  $form->data['sections'][$row['id']]=$row['caption'];
	}
	A::$DB->free();
	$form->data['categories']=array();
	if(!empty($form->data['sections']))
	{ $section=getSectionById(key($form->data['sections']));
	  $this->getTreeList2($section."_categories",$form->data['categories'],0);
	}
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования.
 */

  function Edit()
  {
	$form = new A_Form("block_categories_edit.tpl");
    $block=A::$DB->getRowById($_POST['id'],DOMAIN."_blocks");
	$form->data=!empty($block['params'])?unserialize($block['params']):array();
	$tables=A::$DB->getTables();
	$form->data['sections']=array();
	A::$DB->query("SELECT * FROM ".DOMAIN."_sections");
	while($row=A::$DB->fetchRow())
	{ $section=DOMAIN."_".$row['lang']."_".$row['name'];
	  $usecats=$row['module']!='shoplite'?A::$DB->existsRow("SELECT id FROM ".DOMAIN."_options WHERE item='{$section}' AND var='usecats'"):false;
	  if(in_array($section."_categories",$tables) && (!$usecats || getOption($section,'usecats')))
	  $form->data['sections'][$row['id']]=$row['caption'];
	}
	A::$DB->free();
	$form->data['categories']=array();
	if(!empty($form->data['sections']))
	{ if($section=getSectionById($form->data['idsec']))
	  $this->getTreeList2($section."_categories",$form->data['categories'],0);
	}
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает список категорий.
 */

  function getCategories()
  {
    $this->RESULT['ids']=array();
	$this->RESULT['names']=array();
	$section=getSectionById($_POST['idsec']);
	$tables=A::$DB->getTables();
    if(in_array($section."_categories",$tables))
	{ $categories=array();
	  $this->getTreeList1($section."_categories",$categories,0);
	  foreach($categories as $id=>$name)
	  { $this->RESULT['ids'][]=$id;
	    $this->RESULT['names'][]=$name;
	  }
	}
  }

/**
 * Обработчик действия: Отдает список категорий имеющих подуровни.
 */

  function getOwnerCategories()
  {
    $this->RESULT['ids']=array();
	$this->RESULT['names']=array();
	$section=getSectionById($_POST['idsec']);
	$tables=A::$DB->getTables();
    if(in_array($section."_categories",$tables))
	{ $categories=array();
	  $this->getTreeList2($section."_categories",$categories,0);
	  foreach($categories as $id=>$name)
	  { $this->RESULT['ids'][]=$id;
	    $this->RESULT['names'][]=$name;
	  }
	}
  }

  private function getTreeList1($table,&$values,$id)
  {
    A::$DB->query("SELECT * FROM $table WHERE idker=$id ORDER BY sort");
    if(A::$DB->numRows())
    while($row=A::$DB->fetchRow())
    { $name="";
      for($i=0;$i<$row['level'];$i++)
	  $name.=" > ";
      $values[$row['id']]=$name.$row['name'];
      $this->getTreeList1($table,$values,$row['id']);
    }
	A::$DB->free();
  }

  private function getTreeList2($table,&$values,$id)
  {
    A::$DB->query("SELECT * FROM $table WHERE idker=$id ORDER BY sort");
    if(A::$DB->numRows())
    while($row=A::$DB->fetchRow())
    { $childs=A::$DB->getOne("SELECT COUNT(*) FROM $table WHERE idker=".$row['id']);
	  if($childs>0)
	  { $name="";
        for($i=0;$i<$row['level'];$i++)
	    $name.=" > ";
        $values[$row['id']]=$name.$row['name'];
        $this->getTreeList2($table,$values,$row['id']);
	  }
    }
	A::$DB->free();
  }
}

A::$REQUEST = new categories_BlockRequest;