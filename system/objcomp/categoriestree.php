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
require_once("system/framework/comp.php");

/**
 * Компонент дерева категорий.
 */

class A_CategoriesTree extends A_Component
{
/**
 * Идентификатор таба, на котором находится компонент.
 */

  public $tab;

/**
 * Объект дерева.
 */

  public $treebox;

/**
 * Конструктор.
 *
 * @param string $tab='cat' Идентификатор таба, на котором находится компонент.
 */

  function __construct($tab='cat')
  {
    $this->tab=$tab;

	$this->treebox = new A_ExpandTreeJ();
	$this->treebox->tab='cat';
	$this->treebox->jfun="tc_togglePlusJ";
	$this->treebox->expfun="tc_expandbranch";
	$this->treebox->editfun="geteditcatform";
	$this->treebox->delfun="delcat";
    $this->treebox->tmpltitle="<a href='admin.php?mode=sections&item=".ITEM."&idcat={\$row['id']}&tab=$tab' title='Выбрать'>{\$row['name']}</a>";
    $this->treebox->fedit=true;
	$this->treebox->fsort=true;
	$this->treebox->fdel=true;
    $this->treebox->LoadLevel(SECTION."_categories");

    A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/categoriestree.js");
	A::$MAINFRAME->AddJVar("cur_idcat",!empty($_GET['idcat'])?$_GET['idcat']:0);
    A::$MAINFRAME->AddJVar("cat_tab",$tab);

    parent::__construct("categoriestree.tpl");
  }

/**
 * Маршрутизатор действий.
 *
 * @param string $action Идентификатор действия.
 */

  function Action($action)
  {
    $res=false;
	switch($action)
  	{ case "ct_add": $res=$this->AddCategory(); break;
      case "ct_edit": $res=$this->EditCategory(); break;
      case "ct_del": $res=$this->DelCategory(); break;
	  case "ct_move": $res=$this->Move(); break;
	}
	if($res)
	{ $link="admin.php?mode=sections&item=".ITEM."&tab=cat";
	  if(!empty($_GET['idcat'])) $link.="&idcat=".$_GET['idcat'];
	  A::goUrl($link);
	}
  }

/**
 * Обработчик действия: Добавление категории.
 *
 * @return boolean
 */

  function AddCategory()
  {
    if(isset($_REQUEST['parent_id']))
	{ if($row=A::$DB->getRowById($_REQUEST['parent_id'],SECTION."_categories"))
	  { $_REQUEST['id']=$row['id'];
	    $_REQUEST['idker']=$row['idker'];
		$_REQUEST['level']=$row['level'];
		$_REQUEST['subitem']=1;
	  }
	  else
	  $_REQUEST['id']=-1;
	}

    if(isset($_REQUEST['id']) && $_REQUEST['id']==-1)
    { $_REQUEST['idker']=0;
      $_REQUEST['level']=0;
    }
    elseif(isset($_REQUEST['subitem']))
    { $_REQUEST['idker']=$_REQUEST['id'];
      $_REQUEST['level']++;
    }

    $_GET['idcat']=$_REQUEST['idker'];
    $_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".SECTION."_categories WHERE idker=".(integer)$_REQUEST['idker'])+1;
    $_REQUEST['active']=isset($_REQUEST['active'])?"Y":"N";
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION."_categories","idker=".(integer)$_REQUEST['idker']);

    $dataset = new A_DataSet(SECTION."_categories");
    $dataset->fields=array("idker","name","urlname","level","sort","description","active");
    if($_REQUEST['id']=$dataset->Insert())
	{
	  if($_REQUEST['idimg']=UploadImage("catimage",$_REQUEST['name']))
	  A::$DB->Update(SECTION."_categories",array('idimg'=>$_REQUEST['idimg']),"id=?i",$_REQUEST['id']);

	  A::$OBSERVER->Event('AddCategory',SECTION,$_REQUEST);
	  return true;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Изменение категории.
 *
 * @return boolean
 */

  function EditCategory()
  {
	$row=A::$DB->getRowById($_REQUEST['id'],SECTION."_categories");

    $_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION."_categories","idker=".$row['idker']." AND id<>".$row['id']);
    $_REQUEST['active']=isset($_REQUEST['active'])?"Y":"N";

    $_GET['idcat']=$_REQUEST['id'];

    $dataset = new A_DataSet(SECTION."_categories");
    $dataset->fields=array("name","urlname","description","active");

    if($row=$dataset->Update())
	{
	  if(isset($_REQUEST['imagedel']))
	  { DelRegImage($row['idimg']);
	    A::$DB->Update(SECTION."_categories",array('idimg'=>0),"id=?i",$row['id']);
	  }
	  elseif($_REQUEST['idimg']=UploadImage("catimage",$_REQUEST['name'],$row['idimg']))
	  A::$DB->Update(SECTION."_categories",array('idimg'=>$_REQUEST['idimg']),"id=?i",$row['id']);

	  A::$OBSERVER->Event('UpdateCategory',SECTION,$_REQUEST);

	  if($row['active']!=$_REQUEST['active'])
	  A::$OBSERVER->Event('ActiveCategory',SECTION,$_REQUEST);

	  return true;
	}
	else
	return false;
  }

/**
 * Удаление узла дерева со всеми подкатегориями.
 *
 * @param integer $id Идентификатор узла (категории).
 */

  function DelCategoryBranch($id)
  {
    A::$DB->query("SELECT * FROM ".SECTION."_categories WHERE idker=$id");
    while($row=A::$DB->fetchRow())
    $this->DelCategoryBranch($row['id']);
	A::$DB->free();

	if($row=A::$DB->getRowById($id,SECTION."_categories"))
    { DelRegImage($row['idimg']);
      A::$DB->Delete(SECTION."_categories","id=$id");
	  A::$OBSERVER->Event('DeleteCategory',SECTION,$row);
	}

	return true;
  }

/**
 * Обработчик действия: Удаление категории.
 *
 * @return boolean
 */

  function DelCategory()
  {
	if($row=A::$DB->getRowById($_REQUEST['id'],SECTION."_categories"))
	{ $_GET['idcat']=$row['idker'];
	  $this->DelCategoryBranch((integer)$_REQUEST['id']);
	  A::$OBSERVER->Event('DeleteCategoryBranch',SECTION,$row);
	  return true;
	}
	else
	return false;
  }

/**
 * Обновление значений уровней.
 */

  function ReLevels($id,$level)
  {
    A::$DB->query("SELECT id FROM ".SECTION."_categories WHERE idker=$id");
    while($row=A::$DB->fetchRow())
    { A::$DB->execute("UPDATE ".SECTION."_categories SET level=$level WHERE id=".$row['id']);
	  $this->ReLevels($row['id'],$level+1);
	}
	A::$DB->free();
  }

/**
 * Обработчик действия: Перемещение категории.
 *
 * @return boolean
 */

  function Move()
  {
    $row=A::$DB->getRowById($_REQUEST['id'],SECTION."_categories");
	if(!$row || $row['idker']==$_REQUEST['idto']) return false;

    $update=array();
	if(!empty($_REQUEST['idto']))
	$update['level']=A::$DB->getOne("SELECT level FROM ".SECTION."_categories WHERE id=".(integer)$_REQUEST['idto'])+1;
	else
	$update['level']=0;
	$update['idker']=$row['idto']=(integer)$_REQUEST['idto'];
	$update['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".SECTION."_categories WHERE idker=".(integer)$_REQUEST['idto'])+1;
	$update['urlname']=getURLName($row['name'],$row['urlname'],SECTION."_categories","idker=".(integer)$_REQUEST['idto']);
	A::$DB->Update(SECTION."_categories",$update,"id=".$row['id']);
	$this->ReLevels($row['id'],$update['level']+1);

    A::$OBSERVER->Event('MoveCategory',SECTION,$row);

	return true;
  }

/**
 * Формирование данных шаблона.
 */

  function createData()
  {
    if(!empty($_GET['idcat']))
	{ $this->treebox->Expanded((integer)$_GET['idcat'],SECTION."_categories");
	  $row=A::$DB->getRowById($_GET['idcat'],SECTION."_categories");
	  $this->Assign("category",$row);
	  $title=$row['name'];
	}
	else
	$title="";

	$frame = new A_Frame("default_titlebox.tpl",$title,$this->treebox);
	$this->Assign("treebox",$frame);
  }
}