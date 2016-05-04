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

require_once("system/framework/comp.php");

/**
 * Компонент "редактор комментариев".
 */

class A_CommentsEditor extends A_Component
{
/**
 * Идентификатор таба, на котором находится компонент.
 */

  public $tab;

/**
 * Таблица БД с записями-владельцами комментариев.
 */

  public $table;

/**
 * Конструктор
 *
 * @param string $table Таблица БД с записями-владельцами комментариев.
 * @param string $tab="comm" Идентификатор таба, на котором находится компонент.
 */

  function __construct($table='',$tab='comm')
  {
	$this->table=$table;
	$this->tab=$tab;

    A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/commeditor.js");

    parent::__construct("commeditor.tpl");
  }

/**
 * Машрутизатор действий.
 *
 * @param string $action Идентификатор действия.
 */

  function Action($action)
  {
    $res=false;
	switch($action)
  	{ case "comm_add": $res=$this->AddComment(); break;
	  case "comm_edit": $res=$this->EditComment(); break;
	  case "comm_del": $res=$this->DelComment(); break;
	  case "comm_delete": $res=$this->Delete(); break;
	  case "comm_active": $res=$this->Active(); break;
	  case "comm_unactive": $res=$this->Unactive(); break;
	}
	if($res)
	{ $link="admin.php?mode=".MODE."&item=".ITEM;
	  foreach($_GET as $name=>$value)
	  if($name!='mode' && $name!='item' && $name!='tab')
	  $link.="&$name=$value";
	  if(is_array($this->tab))
	  { foreach($this->tab as $p=>$v)
	    $link.="&$p=$v";
	  }
	  else
	  $link.="&tab=".$this->tab;
	  A::goUrl($link);
	}
  }

/**
 * Обновление количества комментариев для записи-владельца.
 *
 * @param integer $iditem Идентификатор записи-владельца.
 */

  function updateComments($iditem)
  {
    if($this->table)
	{ $count=A::$DB->getCount("mysite_comments","idsec=".SECTION_ID." AND iditem=$iditem");
	  A::$DB->execute("UPDATE `{$this->table}` SET comments=$count WHERE id=$iditem");
	}
  }

/**
 * Обработчик действия: Добавление комментария.
 *
 * @return boolean
 */

  function AddComment()
  {
    $_REQUEST['date']=time();
	$_REQUEST['idsec']=SECTION_ID;
	$_REQUEST['iditem']=(integer)$_REQUEST['iditem'];
    $_REQUEST['message']=parse_bbcode($_REQUEST['bbcode']);
    $_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';

    $dataset = new A_DataSet("mysite_comments");
    $dataset->fields=array("date","idsec","iditem","name","bbcode","message","active");

    if($_REQUEST['id']=$dataset->Insert())
	{ $this->updateComments((integer)$_REQUEST['iditem']);

	  A::$OBSERVER->Event('AddComment',SECTION,$_REQUEST);
	  return true;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Изменение комментария.
 *
 * @return boolean
 */

  function EditComment()
  {
	$_REQUEST['message']=parse_bbcode($_REQUEST['bbcode']);
	$_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';

    $dataset = new A_DataSet("mysite_comments");
    $dataset->fields=array("name","bbcode","message","active");

    if($row=$dataset->Update())
	{ $this->updateComments($row['iditem']);

	  A::$OBSERVER->Event('UpdateComment',SECTION,$_REQUEST);
	  return true;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Удаление комментария.
 *
 * @param integer $id=0 Идентификатор комментария.
 * @return boolean
 */

  function DelComment($id=0)
  {
    if($id>0)
	$_REQUEST['id']=$id;

    $dataset = new A_DataSet("mysite_comments");
    if($row=$dataset->Delete())
	{ $this->updateComments($row['iditem']);

	  A::$OBSERVER->Event('DeleteComment',SECTION,$_REQUEST);
	  return true;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Удаление группы комментариев.
 *
 * @return boolean
 */

  function Delete()
  {
    if(isset($_REQUEST['checkcomm']))
	foreach($_REQUEST['checkcomm'] as $id)
	$this->DelComment($id);
	return true;
  }

  function Active()
  {
    if(isset($_REQUEST['checkcomm']))
	foreach($_REQUEST['checkcomm'] as $id)
	A::$DB->Update("mysite_comments",array('active'=>'Y'),"id=".(integer)$id);
	return true;
  }

  function Unactive()
  {
    if(isset($_REQUEST['checkcomm']))
	foreach($_REQUEST['checkcomm'] as $id)
	A::$DB->Update("mysite_comments",array('active'=>'N'),"id=".(integer)$id);
	return true;
  }

/**
 * Формирование данных шаблона.
 */

  function createData()
  {
    if(!empty($_GET['iditemcomm']) && $this->table)
	{ $this->Assign("item",A::$DB->getRowById($_GET['iditemcomm'],$this->table));
	  $where=" AND iditem=".(integer)$_GET['iditemcomm'];
	}
	else
	$where="";

	$comments=array();
	$pager = new A_Pager(10);
	$pager->tab=$this->tab;
	$pager->query("SELECT *	FROM mysite_comments WHERE idsec=".SECTION_ID."$where ORDER BY date DESC");
	while($row=$pager->fetchRow())
	{ if($this->table)
	  $row['itemname']=A::$DB->getOne("SELECT name FROM `{$this->table}` WHERE id=".$row['iditem']);
	  $comments[]=$row;
	}
	$pager->free();

    $this->Assign("owneritems",!empty($this->table));
	$this->Assign("comments",$comments);
	$this->Assign("comments_pager",$pager);

	$this->Assign("tab",$this->tab);
  }
}