<?php
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AComponents
 */
/**************************************************************************/

require_once("system/framework/tree.php");

/**
 * Серверная сторона AJAX для компонента дерева категорий.
 */

class A_CategoriesTreeRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  { switch($action)
	{ case "getaddcatform": $this->getAddForm(); break;
	  case "geteditcatform": $this->getEditForm(); break;
	  case "getmovecatform": $this->getMoveCatForm(); break;
	  case "getbranch": $this->getBranch(); break;
	  case "expandbranch": $this->expandBranch(); break;
	  case "setsortbranch": $this->setSortBranch(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму добавления.
 */

  function getAddForm()
  {
    $form = new A_Form("objcomp_categoriestree_add.tpl");
	$form->data['id']=$_POST['id'];
	$form->data['idker']=$_POST['idker'];
	$form->data['level']=$_POST['level'];
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования.
 */

  function getEditForm()
  {
    $form = new A_Form("objcomp_categoriestree_edit.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION."_categories");
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Формирует полный список категорий.
 */

  function getCategories(&$values,$id,$noid)
  {
    A::$DB->query("SELECT id,idker,name,level,sort FROM ".SECTION."_categories WHERE idker=$id AND id<>$noid");
	while($row=A::$DB->fetchRow())
	{ $row['level_sort']=sprintf("%03d_%03d",$row['level'],$row['sort']);
	  $values[]=$row;
      $this->getCategories($values,$row['id'],$noid);
	}
	A::$DB->free();
  }

/**
 * Обработчик действия: Отдает форму перемещения.
 */

  function getMoveCatForm()
  {
    $form = new A_Form("objcomp_categoriestree_move.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],SECTION."_categories");
	$form->data['categories']=array();
	$this->getCategories($form->data['categories'],0,$form->data['id']);
	$form->data['categories']=array_multisort_key($form->data['categories'],"level_sort");
	$this->RESULT['title']="Перемещение категории &laquo;".$form->data['name']."&raquo;";
	if(count($form->data['categories'])>0)
	$this->RESULT['html']=$form->getContent();
	else
	$this->RESULT['html']=AddLabel("Нет вариантов перемещения.");
  }

/**
 * Обработчик действия: Отдает ветку дерева (автомат).
 */

  function getBranch()
  {
    $id=intval(mb_substr($_POST['id'],6));
	$_GET['idcat']=(integer)$_POST['idcat'];
	$_POST['tab']=preg_replace("/[^a-zA-Z0-9]/i","",$_POST['tab']);
	$row=A::$DB->getRow("SELECT * FROM ".SECTION."_categories WHERE id=$id");
    $tree = new A_ExpandTreeJ();
	$tree->tab="cat";
	$tree->jfun="tc_togglePlusJ";
	$tree->expfun="tc_expandbranch";
	$tree->editfun="geteditcatform";
	$tree->delfun="delcat";
    $tree->tmpltitle="<a href='admin.php?mode=sections&item=".ITEM."&idcat={\$row['id']}&tab={$_POST['tab']}' title='Выбрать'>{\$row['name']}</a>";
    $tree->fedit=true;
	$tree->fsort=true;
	$tree->fdel=true;
    $tree->LoadLevel(SECTION."_categories",$id,"sort");
	$this->RESULT['html']=$tree->getContent();
  }

/**
 * Формирует путь узлов по первому и последнему элементу.
 */

  function GetTreeId($kerid,$finid)
  {
    $id=A::$DB->getOne("SELECT idker FROM ".SECTION."_categories WHERE id=$finid");
    if($id==$kerid || $id==0)
    return $finid;
    else
    return $this->GetTreeId($kerid,$id);
  }

/**
 * Обработчик действия: Отдает ветку дерева (ручной).
 */

  function expandBranch()
  {
    $id=$this->GetTreeId((integer)$_POST['idker'],(integer)$_POST['finid']);
	$_GET['idcat']=(integer)$_POST['finid'];
	$_POST['tab']=preg_replace("/[^a-zA-Z0-9]/i","",$_POST['tab']);
	$row=A::$DB->getRow("SELECT * FROM ".SECTION."_categories WHERE id=$id");
	$tree = new A_ExpandTreeJ();
	$tree->tab="cat";
	$tree->jfun="tc_togglePlusJ";
	$tree->expfun="tc_expandbranch";
	$tree->editfun="geteditcatform";
	$tree->delfun="delcat";
    $tree->tmpltitle="<a href='admin.php?mode=sections&item=".ITEM."&idcat={\$row['id']}&tab={$_POST['tab']}' title='Выбрать'>{\$row['name']}</a>";
    $tree->fedit=true;
	$tree->fsort=true;
	$tree->fdel=true;
    $tree->LoadLevel(SECTION."_categories",$id,"sort");
	$this->RESULT['id']=$id;
	$this->RESULT['html']=$tree->getContent();
  }

  function setSortBranch()
  {
	$sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
	$i=1;
	foreach($sort as $id)
	A::$DB->Update(SECTION."_categories",array('sort'=>$i++),"id=".(integer)$id);
  }
}

A::$REQUEST = new A_CategoriesTreeRequest;