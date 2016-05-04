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
 * Панель управления модуля "Обратная связь".
 *
 * <a href="http://wiki.a-cms.ru/modules/feedback">Руководство</a>.
 */

class FeedbackModule_Admin extends A_MainFrame
{
/**
 * Конструктор.
 */

  function __construct()
  {
    parent::__construct("module_feedback.tpl");

	$this->AddJScript("/modules/feedback/admin/feedback.js");
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    $res=false;
    switch($action)
    { case "save": $res=$this->SavePage(); break;
	  case "fld_add": $res=$this->AddField(); break;
	  case "fld_edit": $res=$this->EditField(); break;
	  case "fld_del": $res=$this->DelField(); break;
	  case "delarch": $res=$this->delArch(); break;
	  case "delete": $res=$this->Delete(); break;
    }
    if($res)
	A::goUrl("admin.php?mode=sections&item=".SECTION,array('tab','page'));
  }

/**
 * Обработчик действия: Сохранение текста страницы.
 */

  function SavePage()
  {
	setTextOption(SECTION,'content',$_REQUEST['content']);

    $name=A::$DB->getOne("SELECT caption_".A::$LANG." FROM ".DOMAIN."_sections WHERE id=".SECTION_ID);
    A_SearchEngine::getInstance()->updateIndex(SECTION_ID,0,$name,$_REQUEST['content']);

	return true;
  }

/**
 * Обработчик действия: Добавление поля.
 */

  function AddField()
  {
    $fields=A::$DB->getCol("SELECT field FROM ".DOMAIN."_fields WHERE item='".SECTION."'");
	if(in_array($_REQUEST['field'],$fields))
	{ $this->errors['doublefield']=true;
	  return false;
	}

	$lang=LANG=='all'?DEFAULTLANG:LANG;

    $_REQUEST['item']=SECTION;
    $_REQUEST['fill']=isset($_REQUEST['fill'])?'Y':'N';
	$_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".DOMAIN."_fields WHERE item='".SECTION."'")+1;
    $_REQUEST['name_'.$lang]=$_REQUEST['name'];

 $dataset = new A_DataSet(DOMAIN."_fields");
	$dataset->fields=array("item","field","type","fill","sort","name_".$lang);

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
	  case "select":
	  case "mselect":
	    if(empty($_REQUEST['idvar']))
	    { require_once("system/objcomp/fieldseditor.php");
		  $_REQUEST['idvar']=A_FieldsEditor::createList($_REQUEST['field'],$_REQUEST['name_'.$lang]);
	    }
	    if(empty($_REQUEST['idvar']))
		return false;
	    $_REQUEST['property']=$_REQUEST['idvar'];
		$dataset->fields[]="property";
		break;
	}

	return $dataset->Insert();
  }

/**
 * Обработчик действия: Изменение поля.
 */

  function EditField()
  {
    $row=A::$DB->getRowById($_REQUEST['id'],DOMAIN."_fields");

	$fields=A::$DB->getCol("SELECT field FROM ".DOMAIN."_fields WHERE item='".SECTION."'");
	if($_REQUEST['field']!=$row['field'] && in_array($_REQUEST['field'],$fields))
	{ $this->errors['doublefield']=true;
	  return false;
	}

 $lang=LANG=='all'?DEFAULTLANG:LANG;

    $_REQUEST['fill']=isset($_REQUEST['fill'])?'Y':'N';
 $_REQUEST['name_'.$lang]=$_REQUEST['name'];

	$dataset = new A_DataSet(DOMAIN."_fields");
	$dataset->fields=array("field","type","fill","name_".$lang);

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
	  case "select":
	  case "mselect":
	    if(empty($_REQUEST['idvar']))
	    { require_once("system/objcomp/fieldseditor.php");
		  $_REQUEST['idvar']=A_FieldsEditor::createList($_REQUEST['field'],$_REQUEST['name_'.$lang]);
	    }
	    $_REQUEST['property']=$_REQUEST['idvar'];
		$dataset->fields[]="property";
		break;
	}

	return $dataset->Update();
  }

/**
 * Обработчик действия: Удаление поля.
 */

  function DelField()
  {
	$dataset = new A_DataSet(DOMAIN."_fields");
	return $dataset->Delete();
  }

/**
 * Обработчик действия: Удаление записи архива.
 */

  function DelArch()
  {
	$dataset = new A_DataSet(SECTION."_arch");
	return $dataset->Delete();
  }

/**
 * Обработчик действия: Удаление группы записей архива
 */

  function Delete()
  {
    if(isset($_REQUEST['checkarch']))
	foreach($_REQUEST['checkarch'] as $id)
	A::$DB->Delete(SECTION."_arch","id=".(integer)$id);
	return true;
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$this->Assign("maincontent",getTextOption(SECTION,'content'));

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

	$fields=array();
	A::$DB->query("SELECT * FROM ".DOMAIN."_fields WHERE item='".SECTION."' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $row['name']=$row['name_'.DEFAULTLANG];
	  $row['type']=isset($types[$row['type']])?$types[$row['type']]:"";
	  $fields[]=$row;
	}
	A::$DB->free();

	$this->Assign("fields",$fields);

	$arch=array();
	$pager = new A_Pager(20);
	$pager->tab="arch";
	$pager->query("SELECT * FROM ".SECTION."_arch ORDER BY date DESC");
	while($row=$pager->fetchRow())
	$arch[]=$row;
	$this->Assign("arch",$arch);
	$this->Assign("arch_pager",$pager);

	$this->Assign("optbox",new A_OptionsBox("",array("idgroup"=>1)));
  }
}

A::$MAINFRAME = new FeedbackModule_Admin;