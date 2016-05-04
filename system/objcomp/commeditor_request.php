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
 * Серверная сторона AJAX для компонента "редактор комментариев".
 */

class A_CommentsEditorRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  { switch($action)
	{ case "getaddcommentform": $this->getAddCommentsForm(); break;
	  case "geteditcommentform": $this->getEditCommentsForm(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму добавления комментария.
 */

  function getAddCommentsForm()
  {
    $form = new A_Form("objcomp_commeditor_add.tpl");
	$form->data['iditemcomm']=$_POST['iditemcomm'];
	$this->RESULT['html'] = $form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования комментария.
 */

  function getEditCommentsForm()
  {
    $form = new A_Form("objcomp_commeditor_edit.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],"mysite_comments");
	$this->RESULT['html'] = $form->getContent();
  }
}

A::$REQUEST = new A_CommentsEditorRequest;