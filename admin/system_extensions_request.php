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

class SystemExtensions_Request extends A_Request
{
  function Action($action)
  {
    switch($action)
	{ case "getuploadform": $this->getUploadForm(); break;
	  case "setsort": $this->setSort(); break;
	}
  }

  function getUploadForm()
  {
    $form = new A_Form("system_extensions_upload.tpl");
	$form->data['tab']=$_POST['tab'];
	$this->RESULT['html']=$form->getContent();
  }

  function setSort()
  {
    $sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
	$i=1;
	foreach($sort as $id)
	A::$DB->Update("_extensions",array('sort'=>$i++),"id=".(integer)$id);
  }
}

A::$REQUEST = new SystemExtensions_Request;