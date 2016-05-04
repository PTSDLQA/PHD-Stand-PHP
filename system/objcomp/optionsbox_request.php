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

/**
 * Серверная сторона AJAX для компонента "редактор опций".
 */

class A_OptionsBoxRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
	{ case "geteditoptform": $this->getEditForm(); break;
	  case "saveopt": $this->SaveOpt(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму редактирования значений опции.
 */

  function getEditForm()
  {
   	$form = new A_Form("objcomp_optionsbox_edit.tpl");

	$form->data=A::$DB->getRowById($_POST['id'],"mysite_options");
	if($form->data['type']=="select" && !empty($form->data['options']))
	$form->data['selectvars']=unserialize($form->data['options']);
	elseif($form->data['type']=="date")
    $form->data['startyear']=date("Y",!empty($form->data['value'])?$form->data['value']:time())-3;
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Сохранение значения опции.
 */

  function SaveOpt()
  {
	if($row=A::$DB->getRowById($_POST['id'],"mysite_options"))
	switch($row['type'])
	{ case "bool":
	    $_POST['value']=(integer)$_POST['value'];
		$this->RESULT['newvalue']=$_POST['value']==1?AddImage("/templates/admin/images/checked.gif",16,16,"Включено"):AddImage("/templates/admin/images/unchecked.gif",16,16,"Выключено");
		break;
	  case "select":
	    $_POST['value']=strip_tags(trim($_POST['value']));
		if(!empty($row['options']))
		{ $selectvars=unserialize($row['options']);
		  $this->RESULT['newvalue']=isset($selectvars[$_POST['value']])?$selectvars[$_POST['value']]:"";
		}
		else
		$this->RESULT['newvalue']="";
		break;
	  case "date":
	    $_POST['value']=(integer)$_POST['value'];
		$this->RESULT['newvalue']=date("d.m.Y",$_POST['value']);
		break;
	  case 'mailtpl':
		$_POST['value']=strip_tags(trim($_POST['value']));
		if(!empty($_POST['value']))
		$this->RESULT['newvalue']="<a href=\"javascript:edittpl('mails/{$_POST['value']}')\" title=\"Редактировать шаблон\">".htmlspecialchars($_POST['value'])."</a>";
		else
		$this->RESULT['newvalue']="";
		break;
	  case 'othertpl':
		$_POST['value']=strip_tags(trim($_POST['value']));
		if(!empty($_POST['value']))
		$this->RESULT['newvalue']="<a href=\"javascript:edittpl('others/{$_POST['value']}')\" title=\"Редактировать шаблон\">".htmlspecialchars($_POST['value'])."</a>";
		else
		$this->RESULT['newvalue']="";
		break;
	  case 'int':
	    $_POST['value']=(integer)$_POST['value'];
		$this->RESULT['newvalue']=$_POST['value'];
		break;
	  case 'float':
	    $_POST['value']=(float)$_POST['value'];
		$this->RESULT['newvalue']=$_POST['value'];
		break;
	  default:
	    $_POST['value']=trim($_POST['value']);
		$this->RESULT['newvalue']=htmlspecialchars($_POST['value']);
		break;
	}

	if($row['superonly']=='N' || A::$AUTH->isSuperAdmin())
	A::$DB->Update("mysite_options",array('value'=>$_POST['value']),"id=".(integer)$_POST['id']);

	$row['value']=$_POST['value'];
	A::$OBSERVER->Event('UpdateOption',$row['item'],$row);

	$this->RESULT['refresh']=!empty($row['refresh']) && $row['refresh']=='Y';
  }
}

A::$REQUEST = new A_OptionsBoxRequest;