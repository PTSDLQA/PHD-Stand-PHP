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

class SitePages_Request extends A_Request
{
  function Action($action)
  {
    switch($action)
    { case "geteditpageform": $this->getEditForm(); break;
	}
  }

  function getEditForm()
  {
    $form = new A_Form("site_pages_edit.tpl");
	$form->data=A::$DB->getRowById($_REQUEST['id'],"mysite_templates");
	$caption=$form->data['caption'];
	$section=A::$DB->getOne("SELECT caption FROM mysite_sections WHERE id=".$form->data['idsec']);
	$this->RESULT['title']="$section - $caption";
	$this->RESULT['html']=$form->getContent();
  }
}

A::$REQUEST = new SitePages_Request;