<?php
/** \file system/framework/tree.php
 * Дерево HTML.
 */
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AFramework
 */
/**************************************************************************/

require_once("system/framework/html.php");

/**
 * Узел дерева.
 */

class A_Expand extends A_HTMLContent
{
/**
 * Дочерние элементы.
 */

  public $items=array();

/**
 * Идентификатор узла.
 */

  public $id;

/**
 * Идентификатор родительского узла.
 */

  public $idker;

/**
 * Уровень узла.
 */

  public $level;

/**
 * Название.
 */

  public $title;

/**
 * Общее количество дочерних элементов.
 */

  public $count=0;

/**
 * Числовой идентификатор изображения.
 */

  public $idimg=0;

/**
 * Флаг активности.
 */

  public $active;

/**
 * Чекбокс для узла.
 */

  public $check;

/**
 * Иконка редактирования для узла.
 */

  public $bedit;

/**
 * Иконки ручной сортировки для узла.
 */

  public $bsort;

/**
 * Иконка удаления для узла.
 */

  public $bdel;

/**
 * Элемент разворачивания подуровней для узла.
 */

  public $bexpand=true;

/**
 * Флаг узла как элемента дерева.
 */

  public $ftree;

/**
 * JavaScript обработчик развертывания узла.
 */

  public $jfun="togglePlus";

/**
 * Ссылка с названия узла.
 */

  public $blink;

/**
 * Конструктор.
 *
 * @param $title Название.
 * @param $id Идентификатор.
 * @param $ftree=false Элемент дерева.
 */

  function __construct($title,$id,$ftree=false)
  {
    $this->id=$id;
	$this->title=$title;
	$this->ftree=$ftree;
  }

/**
 * Устанавливает статус узла (развернутый или нет).
 *
 * @param $visible
 */

  function SetStatus($status)
  {
    if($status)
	$this->AddJScript("expandobj('$this->id');","code");
	else
	$this->AddJScript("collapseobj('$this->id');","code");
  }

/**
 * Формирование данных шаблона.
 */

  function createData()
  {
	foreach($this->items as $item)
    $this->AddContent($item->getContent());
	$smarty = new Smarty();
	$smarty->template_dir=SMARTY_TEMPLATES."/others/";
	$smarty->compile_dir=SMARTY_COMPILE."/others/";
	$smarty->Assign("bexpand",$this->bexpand && (!$this->ftree || count($this->items)>0));
	$smarty->Assign_by_ref("id",$this->id);
	$idcat=mb_substr($this->id,6);
	$smarty->Assign_by_ref("idcat",$idcat);
	$smarty->Assign("selected",isset($_GET['idcat']) && $_GET['idcat']==$idcat);
	$smarty->Assign("idcat",$idcat);
	$smarty->Assign("seo",getStructureByPlugin('seo'));
	$smarty->Assign_by_ref("idker",$this->idker);
	$smarty->Assign_by_ref("level",$this->level);
	$smarty->Assign("idpic","{$this->id}_bullet");
	$smarty->Assign("jfun","{$this->jfun}('$this->id')");
	$smarty->Assign_by_ref("count",$this->count);
	$smarty->Assign_by_ref("idimg",$this->idimg);
	$smarty->Assign_by_ref("active",$this->active);
	$smarty->Assign_by_ref("check",$this->check);
	$smarty->Assign_by_ref("title",$this->title);
	$smarty->Assign_by_ref("bedit",$this->bedit);
	$smarty->Assign_by_ref("bsort",$this->bsort);
	$smarty->Assign_by_ref("bdel",$this->bdel);
	$smarty->Assign_by_ref("blink",$this->blink);
	$smarty->Assign("content",$this->content);
	$this->content=$smarty->fetch("default_expand.tpl");
  }

/**
 * Возвращает сгенерированное содержимое узла.
 *
 * @return string HTML.
 */

  function getContent()
  { $this->createData();
    return $this->content;
  }
}

/**
 * Дерево элементов.
 */

class A_ExpandTree extends A_HTMLContent
{
/**
 * Корневые элементы.
 */

  public $items=array();

/**
 * Список ссылок на элементы.
 */

  public $itemsptr=array();
  public $tmpltitle;
  public $bexpand=true;
  public $check=false;
  public $fsort=false;
  public $fdel=false;
  public $editfun;
  public $delfun;
  public $tab=0;

/**
 * Конструктор.
 */

  function __construct()
  {
	if(!empty(A::$MAINFRAME))
    A::$MAINFRAME->AddJScript("/system/jscripts/tree.js");
  }

/**
 * Загрузка элементов дерева из БД.
 *
 * @param string $table Таблица БД со структурой дерева.
 * @param string $sort='sort' Поле для сортировки.
 * @param string $prefid='dbetb_' Префикс идентификаторов элементов (Идентификатор дерева).
 */

  function LoadTree($table,$sort="sort",$prefid="dbetb_")
  {
	if(!empty($_REQUEST['treeaction']) && !empty($_REQUEST['treeid']) && !empty($_REQUEST['id']) && $_REQUEST['treeid']==$prefid)
    switch($_REQUEST['treeaction'])
    { case "up":  $this->UpItem($table); break;
	  case "down":  $this->DownItem($table); break;
	}

	A::$DB->query("SELECT * FROM $table ORDER BY level,$sort");
    while($row=A::$DB->fetchRow())
	{ if(!empty($this->tmpltitle))
   	  { $row['id']=(integer)$row['id'];
	    $row['name']=preg_replace("/['\";]/i","",$row['name']);
		eval("\$tname=\"$this->tmpltitle\";");
   	  }
	  else
	  $tname=$row['name'];
	  if($row['level']==0)
	  { $this->items[$row['id']] = new A_Expand($tname,$prefid.$row['id'],true);
	    $this->itemsptr[$row['id']]=&$this->items[$row['id']];
		$this->items[$row['id']]->bexpand=$this->bexpand;
		$this->items[$row['id']]->check=$this->check;
		$this->items[$row['id']]->idker=$row['idker'];
		$this->items[$row['id']]->level=$row['level'];
		if(!empty($row['citems']))
		$this->items[$row['id']]->count=$row['citems'];
		$this->items[$row['id']]->idimg=$row['idimg'];
		$this->items[$row['id']]->active=$row['active']=='Y';
		if($this->fedit)
		$this->items[$row['id']]->bedit=AddImageButton("/templates/admin/images/edit.gif","{$this->editfun}({$row['id']})","Редактировать",16,16);
		if($this->fdel)
		$this->items[$row['id']]->bdel=AddImageButton("/templates/admin/images/del.gif","{$this->delfun}({$row['id']})","Удалить",16,16);
	  }
	  else
	  { $this->itemsptr[$row['idker']]->items[$row['id']] = new A_Expand($tname,$prefid.$row['id'],true);
		$this->itemsptr[$row['id']]=&$this->itemsptr[$row['idker']]->items[$row['id']];
        $this->itemsptr[$row['idker']]->items[$row['id']]->bexpand=$this->bexpand;
		$this->itemsptr[$row['idker']]->items[$row['id']]->check=$this->check;
		$this->itemsptr[$row['idker']]->items[$row['id']]->idker=$row['idker'];
		$this->itemsptr[$row['idker']]->items[$row['id']]->level=$row['level'];
		if(!empty($row['citems']))
		$this->itemsptr[$row['idker']]->items[$row['id']]->count=$row['citems'];
		$this->itemsptr[$row['idker']]->items[$row['id']]->idimg=$row['idimg'];
		$this->itemsptr[$row['idker']]->items[$row['id']]->active=$this->itemsptr[$row['idker']]->active && $row['active']=='Y';
 	 	if($this->fedit)
		$this->itemsptr[$row['idker']]->items[$row['id']]->bedit=AddImageButton("/templates/admin/images/edit.gif","{$this->editfun}({$row['id']})","Редактировать",16);
		if($this->fdel)
		$this->itemsptr[$row['idker']]->items[$row['id']]->bdel=AddImageButton("/templates/admin/images/del.gif","{$this->delfun}({$row['id']})","Удалить",16,16);

	  }
	}
	A::$DB->free();

	if($this->fsort)
	{ A::$DB->query("SELECT * FROM $table ORDER BY idker,$sort");
      $ker=$id=-1;
	  $count=0;
	  while($row=A::$DB->fetchRow())
	  { if($row['idker']!=$ker)
	    { $this->itemsptr[$row['id']]->bsort=AddImageButtonLink("/templates/admin/images/down.gif",$this->createLink("down",$prefid,$row['id']),"Вниз",16,16);
	      if($id>0)
	      { if($count>1)
		    $this->itemsptr[$id]->bsort=AddImageButtonLink("/templates/admin/images/up.gif",$this->createLink("up",$prefid,$id),"Вверх",16,16);
		    else
		    $this->itemsptr[$id]->bsort="";
		  }
		  $count=0;
	    }
	    else
	    $this->itemsptr[$row['id']]->bsort=AddImageButtonLink("/templates/admin/images/up.gif",$this->createLink("up",$prefid,$row['id']),"Вверх",16,16)."&nbsp;".AddImageButtonLink("/templates/admin/images/down.gif",$this->createLink("down",$prefid,$row['id']),"Вниз",16,16);
	    $ker=$row['idker'];
	    $id=$row['id'];
	    $count++;
	  }
	  A::$DB->free();

	  if($id>0 && $count>1)
	  $this->itemsptr[$id]->bsort=AddImageButtonLink("/templates/admin/images/up.gif",$this->createLink("up",$prefid,$id),"Вверх",16,16);
	  else
	  $this->itemsptr[$id]->bsort="";
	}
  }

/**
 * Перемещение элемента на позицию выше.
 *
 * @param string $table Таблица БД со структурой дерева.
 */

  function UpItem($table)
  {
	if($row=A::$DB->getRowById($_REQUEST['id'],SECTION."_categories"))
    { if($prevrow=A::$DB->getRow("SELECT id,sort FROM ".SECTION."_categories WHERE idker={$row['idker']} AND sort<{$row['sort']} ORDER BY sort DESC LIMIT 0,1"))
	  { A::$DB->execute("UPDATE ".SECTION."_categories SET sort={$row['sort']} WHERE id={$prevrow['id']}");
	    A::$DB->execute("UPDATE ".SECTION."_categories SET sort={$prevrow['sort']} WHERE id={$row['id']}");
	    A::goUrl('admin.php?mode='.MODE.'&item='.ITEM.'&tab=cat&idcat='.$row['idker']);
	  }
	}
  }

/**
 * Перемещение элемента на позицию ниже.
 *
 * @param string $table Таблица БД со структурой дерева.
 */

  function DownItem($table)
  {
	if($row=A::$DB->getRowById($_REQUEST['id'],SECTION."_categories"))
	{ if($nextrow=A::$DB->getRow("SELECT id,sort FROM ".SECTION."_categories WHERE idker={$row['idker']} AND sort>{$row['sort']} ORDER BY sort LIMIT 0,1"))
	  { A::$DB->execute("UPDATE ".SECTION."_categories SET sort={$row['sort']} WHERE id={$nextrow['id']}");
	    A::$DB->execute("UPDATE ".SECTION."_categories SET sort={$nextrow['sort']} WHERE id={$row['id']}");
	    A::goUrl('admin.php?mode='.MODE.'&item='.ITEM.'&tab=cat&idcat='.$row['idker']);
	  }
	}
  }

/**
 * Формирование ссылки действия.
 *
 * @param string $treeaction Идентификатор действия.
 * @param string $treeid Идентификатор дерева.
 * @param string $id Идентификатор элемента.
 * @return string Ссылка.
 */

  function createLink($treeaction,$treeid,$id)
  {
    $query="";
    foreach($_REQUEST as $name=>$value)
	if($name!="treeaction" && $name!="treeid" && $name!="id" && $name!="tab")
	{ if(!empty($query))
	  $query.="&";
	  if(!empty($value))
	  $query.="$name=$value";
	}
	if(!empty($this->tab))
	$query.="&tab=$this->tab";
	return "admin.php?$query&treeaction=$treeaction&treeid=$treeid&id=$id";

  }

/**
 * Разворачивает вложенные элементы до указанного.
 *
 * @param string $id Идентификатор элемента.
 * @param string $table Таблица БД со структурой дерева.
 */

  function Expanded($id,$table)
  {
    while($row=A::$DB->getRow("SELECT idker FROM $table WHERE id=$id"))
  	{ $this->itemsptr[$id]->SetStatus(true);
	  $id=$row['idker'];
	}
  }

/**
 * Формирование HTML кода всех элементов.
 *
 * @param string HTML.
 */

  function createData()
  {
    foreach($this->items as $item)
    if(method_exists($item,'getContent'))
    $this->AddContent($item->getContent());
  }
}

/**
 * Дерево элементов c AJAX подгрузкой ветвей.
 */

class A_ExpandTreeJ extends A_ExpandTree
{
/**
 * JavaScript обработчик развертывания узла.
 */

  public $jfun="togglePlusJ";

/**
 * JavaScript обработчик развертывания узлов до указанного элемента.
 */

  public $expfun="expandbranch";

/**
 * Загрузка элементов ветки дерева.
 *
 * @param string $table Таблица БД со структурой дерева.
 * @param integer $idker=0 Идентфикатор родительского узла.
 * @param string $sort='sort' Поле для сортировки элементов.
 * @param string $prefid='dbetbj' Идентификатор дерева.
 */

  function LoadLevel($table,$idker=0,$sort="sort",$prefid="dbetbj")
  {
	if(!empty($_REQUEST['treeaction']) && !empty($_REQUEST['treeid']) && !empty($_REQUEST['id']) && $_REQUEST['treeid']==$prefid)
    switch($_REQUEST['treeaction'])
    { case "up":  $this->UpItem($table); break;
	  case "down":  $this->DownItem($table); break;
	}

	if($idker>0)
	$active=A::$DB->getOne("SELECT active FROM $table WHERE id=$idker")=='Y';
	else
	$active=true;

    A::$DB->query("SELECT * FROM $table WHERE idker=$idker ORDER BY $sort");
	if(A::$DB->NumRows()==0) return;
	$ker=-1;
	$id=0;
	$count=0;

    while($row=A::$DB->fetchRow())
	{ if(!empty($this->tmpltitle))
   	  { $row['id']=(integer)$row['id'];
	    $row['name']=preg_replace("/['\";]/i","",$row['name']);
		eval("\$tname=\"$this->tmpltitle\";");
	  }
	  else
	  $tname=$row['name'];
	  if($rcount=A::$DB->getOne("SELECT COUNT(id) AS rcount FROM $table WHERE idker={$row['id']}"))
	  $this->items[$row['id']] = new A_Expand($tname,$prefid.$row['id'],false);
	  else
	  $this->items[$row['id']] = new A_Expand($tname,$prefid.$row['id'],true);
	  $this->itemsptr[$row['id']]=&$this->items[$row['id']];
	  $this->items[$row['id']]->check=$this->check;
	  $this->items[$row['id']]->idker=$row['idker'];
	  $this->items[$row['id']]->level=$row['level'];
	  if(!empty($row['citems']))
	  $this->items[$row['id']]->count=$row['citems'];
	  $this->items[$row['id']]->idimg=$row['idimg'];
	  $this->items[$row['id']]->active=$active && $row['active']=='Y';
	  $this->items[$row['id']]->jfun=$this->jfun;
	  if(function_exists(MODULE."_createCategoryLink"))
	  $this->items[$row['id']]->blink=call_user_func(MODULE."_createCategoryLink",$row['id'],SECTION);
	  if($this->fedit)
	  $this->items[$row['id']]->bedit=AddImageButton("/templates/admin/images/edit.gif","{$this->editfun}({$row['id']})","Редактировать",16,16);
	  if($this->fdel)
	  $this->items[$row['id']]->bdel=AddImageButton("/templates/admin/images/del.gif","{$this->delfun}({$row['id']})","Удалить",16,16);
	  if($this->fsort)
	  { if($ker!=$row['idker'])
	    $this->items[$row['id']]->bsort=AddImageButton("/templates/admin/images/down.gif","downitem('$prefid',{$row['id']})","Вниз",16,16);
		else
		$this->itemsptr[$row['id']]->bsort=AddImageButton("/templates/admin/images/up.gif","upitem('$prefid',{$row['id']})","Вверх",16,16).AddImageButton("/templates/admin/images/down.gif","downitem('$prefid',{$row['id']})","Вниз",16,16);
	  }
	  $ker=$row['idker'];
	  $id=$row['id'];
	  $count++;
	}
	A::$DB->free();

	if($this->fsort)
	{ if($id>0 && $count>1)
	  $this->itemsptr[$id]->bsort=AddImageButton("/templates/admin/images/up.gif","upitem('$prefid',$id)","Вниз",16,16);
	  else
	  $this->itemsptr[$id]->bsort="&nbsp;";
	}
  }

/**
 * Развертывание веток дерева до указанного элемента.
 *
 * @param integer $id Идентификатор элемента.
 */

  function Expanded($id)
  {
    $this->AddJScript($this->expfun."(0,$id)","code");
  }
}