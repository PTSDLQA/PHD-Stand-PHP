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
 * Компонент "редактор дополнительных полей".
 */

class A_FieldsEditor extends A_Component
{
/**
 * Идентификатор таба, на котором находится компонент.
 */

  public $tab;

/**
 * Таблица БД.
 */

  public $table;

/**
 * Флаг доступности опции "Обязательно для заполнения".
 */

  public $usefill;

/**
 * Флаг доступности опции "Поиск по полю для администратора".
 */

  public $usesearch;

/**
 * Флаг доступности опции "Не использовать во внешних формах".
 */

  public $usenofront;

/**
 * Конструктор.
 *
 * @param string $table Таблица БД.
 * @param string $tab='fields' Идентификатор таба, на котором находится компонент.
 * @param boolean $usefill=false Флаг доступности опции "Обязательно для заполнения".
 * @param boolean $usesearch=false Флаг доступности опции "Поиск по полю для администратора".
 * @param boolean $usenofront=false Флаг доступности опции "Не использовать во внешних формах".
 */

  function __construct($table,$tab="fields",$usefill=false,$usesearch=false,$usenofront=false)
  {
	$this->table=$table;
	$this->tab=$tab;
	$this->usefill=$usefill;
	$this->usesearch=$usesearch;
	$this->usenofront=$usenofront;

	$item=MODE=='sections'?SECTION:STRUCTURE;

    A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/fieldseditor.js");
	A::$MAINFRAME->AddJScript("var fields_usefill=".($usefill?"true":"false").";","code");
	A::$MAINFRAME->AddJScript("var fields_usesearch=".($usesearch?"true":"false").";","code");
	A::$MAINFRAME->AddJScript("var fields_usenofront=".($usenofront?"true":"false").";","code");

    parent::__construct("fieldseditor.tpl");
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
  	{ case "fld_add": $res=$this->AddField(); break;
	  case "fld_edit": $res=$this->EditField(); break;
	  case "fld_del": $res=$this->DelField(); break;
	}
	if($res)
	{ $link="admin.php?mode=".MODE."&item=".ITEM;
	  if(is_array($this->tab))
	  { foreach($this->tab as $p=>$v)
	    $link.="&$p=$v";
	  }
	  else
	  $link.="&tab=".$this->tab;
	  if($res!==true) $link.="&message=$res";
	  A::goUrl($link);
	}
  }

/**
 * Обработчик действия: Добавление поля.
 *
 * @return boolean
 */

  function AddField()
  {
    $item=MODE=='sections'?SECTION:STRUCTURE;

	$_REQUEST['field']=substr(strtolower(preg_replace("/[^a-zA-Z0-9_]+/i","",$_REQUEST['field'])),0,20);
	if(empty($_REQUEST['field'])) return false;

    $fields=A::$DB->getFields($this->table);
	if(in_array($_REQUEST['field'],$fields))
	return "doublefield";

    $_REQUEST['item']=$item;
    $_REQUEST['fill']=isset($_REQUEST['fill'])?"Y":"N";
	$_REQUEST['search']=isset($_REQUEST['search'])?"Y":"N";
	$_REQUEST['nofront']=isset($_REQUEST['nofront'])?"Y":"N";
	$_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM mysite_fields WHERE item='{$item}'")+1;

	$dataset = new A_DataSet("mysite_fields");
	$dataset->fields=array("item","field","type","fill","search","nofront","sort");

    $dataset->fields[]='name_ru';
	$_REQUEST['name_ru']=trim(strip_tags($_REQUEST['name_ru']));

	switch($_REQUEST['type'])
	{ case "string":
	    if(empty($_REQUEST['length']) || !is_numeric($_REQUEST['length']))
		$_REQUEST['property']=50;
		else
		$_REQUEST['property']=$_REQUEST['length'];
		$dataset->fields[]="property";
		break;
	  case "bool":
		$_REQUEST['property']=!empty($_REQUEST['booldef'])?$_REQUEST['booldef']:0;
		$dataset->fields[]="property";
		break;
	  case "text":
	    if(empty($_REQUEST['rows']) || !is_numeric($_REQUEST['rows']))
		$_REQUEST['property']=5;
		else
		$_REQUEST['property']=$_REQUEST['rows'];
		$dataset->fields[]="property";
		break;
	  case "format":
	    if(empty($_REQUEST['height']) || !is_numeric($_REQUEST['height']))
		$_REQUEST['property']=200;
		else
		$_REQUEST['property']=$_REQUEST['height'];
		$dataset->fields[]="property";
		break;
	}

	if($_REQUEST['id']=$dataset->Insert())
	{ $field=$_REQUEST['field'];
	  switch($_REQUEST['type'])
	  { case "string":
	      $length=$_REQUEST['property'];
		  A::$DB->execute("ALTER TABLE `{$this->table}` ADD `{$field}` VARCHAR($length) DEFAULT NULL");
		  break;
		case "int":
		case "date":
		  A::$DB->execute("ALTER TABLE `{$this->table}` ADD `{$field}` INT(11) DEFAULT '0' NOT NULL");
		  A::$DB->execute("ALTER TABLE `{$this->table}` ADD INDEX (`{$field}`)");
		  break;
		case "image":
		case "file":
	      A::$DB->execute("ALTER TABLE `{$this->table}` ADD `{$field}` INT(11) DEFAULT '0' NOT NULL");
		  break;
		case "float":
		  A::$DB->execute("ALTER TABLE `{$this->table}` ADD `{$field}` DECIMAL(10,2) DEFAULT '0' NOT NULL");
		  A::$DB->execute("ALTER TABLE `{$this->table}` ADD INDEX (`{$field}`)");
		  break;
		case "bool":
	      A::$DB->execute("ALTER TABLE `{$this->table}` ADD `{$field}` ENUM('Y','N') DEFAULT 'N' NOT NULL");
	      A::$DB->execute("ALTER TABLE `{$this->table}` ADD INDEX (`{$field}`)");
		  break;
		case "text":
		case "format":
	      A::$DB->execute("ALTER TABLE `{$this->table}` ADD `{$field}` TEXT DEFAULT NULL");
		  break;
	  }

	  A::$OBSERVER->Event('AddField',$_REQUEST['item'],$_REQUEST);
	  return true;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Изменение поля.
 *
 * @return boolean
 */

  function EditField()
  {
    $item=MODE=='sections'?SECTION:STRUCTURE;

	$_REQUEST['field']=substr(strtolower(preg_replace("/[^a-zA-Z0-9_]+/i","",$_REQUEST['field'])),0,20);
	if(empty($_REQUEST['field'])) return false;

    $row=A::$DB->getRowById($_REQUEST['id'],"mysite_fields");

	$fields=A::$DB->getFields($this->table);
	if($_REQUEST['field']!=$row['field'] && in_array($_REQUEST['field'],$fields))
	return "doublefield";

    $_REQUEST['fill']=isset($_REQUEST['fill'])?"Y":"N";
	$_REQUEST['search']=isset($_REQUEST['search'])?"Y":"N";
	$_REQUEST['nofront']=isset($_REQUEST['nofront'])?"Y":"N";

	$dataset = new A_DataSet("mysite_fields");
	$dataset->fields=array("field","name","type","fill","search","nofront");

    $dataset->fields[]='name_ru';
	$_REQUEST['name_ru']=trim(strip_tags($_REQUEST['name_ru']));

	switch($_REQUEST['type'])
	{ case "string":
	    if(empty($_REQUEST['length']) || !is_numeric($_REQUEST['length']))
		$_REQUEST['property']=50;
		else
		$_REQUEST['property']=$_REQUEST['length'];
		$dataset->fields[]="property";
		break;
	  case "bool":
		$_REQUEST['property']=!empty($_REQUEST['booldef'])?$_REQUEST['booldef']:0;
		$dataset->fields[]="property";
		break;
	  case "text":
	    if(empty($_REQUEST['rows']) || !is_numeric($_REQUEST['rows']))
		$_REQUEST['property']=5;
		else
		$_REQUEST['property']=$_REQUEST['rows'];
		$dataset->fields[]="property";
		break;
	  case "format":
	    if(empty($_REQUEST['height']) || !is_numeric($_REQUEST['height']))
		$_REQUEST['property']=200;
		else
		$_REQUEST['property']=$_REQUEST['height'];
		$dataset->fields[]="property";
		break;
	}
	if($row=$dataset->Update())
	{ $field=$_REQUEST['field'];
	  if($row['type']!=$_REQUEST['type'] && $this->existsindex($this->table,$row['field']))
	  A::$DB->execute("ALTER TABLE `{$this->table}` DROP INDEX `{$row['field']}`");
	  switch($_REQUEST['type'])
	  { case "string":
	      $length=$_REQUEST['property'];
		  A::$DB->execute("ALTER TABLE `{$this->table}` CHANGE `{$row['field']}` `{$field}` VARCHAR($length) DEFAULT NULL");
		  break;
		case "int":
		case "date":
		  A::$DB->execute("ALTER TABLE `{$this->table}` CHANGE `{$row['field']}` `{$field}` INT(11) DEFAULT '0' NOT NULL");
		  A::$DB->execute("ALTER TABLE `{$this->table}` ADD INDEX (`{$field}`)");
		  break;
		case "image":
		case "file":
	      A::$DB->execute("ALTER TABLE `{$this->table}` CHANGE `{$row['field']}` `{$field}` INT(11) DEFAULT '0' NOT NULL");
		  break;
		case "float":
		  A::$DB->execute("ALTER TABLE `{$this->table}` CHANGE `{$row['field']}` `{$field}` DECIMAL(10,2) DEFAULT '0' NOT NULL");
		  A::$DB->execute("ALTER TABLE `{$this->table}` ADD INDEX (`{$field}`)");
		  break;
		case "bool":
	      A::$DB->execute("ALTER TABLE `{$this->table}` CHANGE `{$row['field']}` `{$field}` ENUM('Y','N') DEFAULT 'N' NOT NULL");
	      A::$DB->execute("ALTER TABLE `{$this->table}` ADD INDEX (`{$field}`)");
		  break;
		case "text":
		case "format":
	      A::$DB->execute("ALTER TABLE `{$this->table}` CHANGE `{$row['field']}` `{$field}` TEXT DEFAULT NULL");
		  break;
	  }

	  A::$OBSERVER->Event('UpdateField',$row['item'],$_REQUEST);
	  return true;
	}
	else
	return false;
  }

/**
 * Проверка существования индекса в таблице.
 *
 * @param string $table Таблица БД.
 * @param string $key Индекс.
 * @return boolean
 */

  function existsindex($table,$key)
  {
    $index=A::$DB->getIndex($table);
    return in_array($key,$index);
  }

/**
 * Обработчик действия: Удаление поля.
 *
 * @return boolean
 */

  function DelField()
  {
    $item=MODE=='sections'?SECTION:STRUCTURE;

	$dataset = new A_DataSet("mysite_fields");
	if($row=$dataset->Delete())
	{ A::$DB->execute("ALTER TABLE `{$this->table}` DROP `{$row['field']}`");

	  A::$OBSERVER->Event('DeleteField',$item,$row);
	  return true;
	}
	else
	return false;
  }

/**
 * Формирование данных шаблона.
 */

  function createData()
  {
    $item=MODE=='sections'?SECTION:STRUCTURE;

	$types=array(
	'string'=>'Строка',
	'int'=>'Целое число',
	'float'=>'Дробное число',
	'bool'=>'Логический (Да/Нет)',
	'date'=>'Дата',
	'text'=>'Текст',
	'format'=>'Форматированный текст',
	'select'=>'Значение из списка',
	'mselect'=>'Множество значений из списка',
	'image'=>'Изображение',
	'file'=>'Файл');

	$ptabs="";
	if(is_array($this->tab))
    { foreach($this->tab as $p=>$v)
	    $ptabs.="&$p=$v";
	  }
	  else
	  $ptabs.="&tab=".$this->tab;

	$fields=array();
	A::$DB->query("SELECT * FROM mysite_fields WHERE item='$item' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $row['type']=isset($types[$row['type']])?$types[$row['type']]:"";
	  $row['name']=$row['name_ru'];
	  $fields[]=$row;
	}

	$this->Assign("fields",$fields);
	$this->Assign("usefill",$this->usefill);
	$this->Assign("usesearch",$this->usesearch);
	$this->Assign("usenofront",$this->usenofront);

	if(isset($_REQUEST['message']))
	$this->Assign("message",$_REQUEST['message']);
  }
}