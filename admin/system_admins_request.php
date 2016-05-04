<?php
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package APanel
 */
/**************************************************************************/

class SystemAdmins_Request extends A_Request
{
  function Action($action)
  {
    switch($action)
	{ case "getaddadminform": $this->getAddAdminForm(); break;
	  case "geteditadminform": $this->getEditAdminForm(); break;
	}
  }

  function getAddAdminForm()
  {
    $form = new A_Form("system_admins_add.tpl");

	$this->RESULT['html']=$form->getContent();
  }

  function getEditAdminForm()
  {
    $form = new A_Form("system_admins_edit.tpl");

	$form->data=A::$DB->getRowById($_POST['id'],"_auth");

	$this->RESULT['html']=$form->getContent();
  }
}

A::$REQUEST = new SystemAdmins_Request;