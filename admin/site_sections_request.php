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

class SiteSections_Request extends A_Request
{
  function Action($action)
  {
    switch($action)
	{ case "getaddsectionform": $this->getAddSectionForm(); break;
	  case "geteditsectionform": $this->getEditSectionForm(); break;
	  case "getnewname": $this->getNewName(); break;
	  case "setsort": $this->setSort(); break;
	}
  }

  function getAddSectionForm()
  {
    $form = new A_Form("site_sections_add.tpl");

	A::$DB->query("SELECT * FROM _extensions WHERE type='module' ORDER BY sort,id");
    while($row=A::$DB->fetchRow())
	{ if($row['multiple']=='N')
	  { if(A::$DB->existsRow("SELECT id FROM mysite_sections WHERE module='{$row['name']}'"))
	    continue;
	  }
	  if($xml=loadXML('modules/'.$row['name'].'/sql-xml/install.xml',true))
	  { if(!empty($xml['hidden']))
	    continue;
	  }
	  $form->data['modules'][$row['name']]=$row['caption'];
	}
	A::$DB->free();

	$form->data['slanguages']=array('ru'=>'Русский');
	$form->data['languages']=array();
	$row=array('name'=>'ru');
	$row['_caption']['field']="caption_ru";
	$row['_title']['field']="title_ru";
	$form->data['languages'][]=$row;

	$this->RESULT['html']=$form->getContent();
  }

  function getEditSectionForm()
  {
    $form = new A_Form("site_sections_edit.tpl");

    $form->data=A::$DB->getRowById($_POST['id'],"mysite_sections");

	$form->data['slanguages']=array('ru'=>'Русский');
	$form->data['languages']=array();
	$row=array('name'=>'ru');
	$row['_caption']['field']="caption_ru";
	$row['_caption']['value']=$form->data["caption_ru"];
	$row['_title']['field']="title_ru";
	$row['_title']['value']=$form->data["title_ru"];
	$form->data['languages'][]=$row;

	$this->RESULT['html']=$form->getContent();
  }

  function getNewName()
  {
    $base=!empty($_POST['module'])?preg_replace("/[^a-zA-Z0-9]/i","",$_POST['module']):"newsection";
    if(!A::$DB->existsRow("SELECT id FROM mysite_sections WHERE name=?",$base))
    $this->RESULT['name']=$base;
    else
	for($i=1;$i<50;$i++)
    { $name=$base.$i;
      if(!A::$DB->existsRow("SELECT id FROM mysite_sections WHERE name=?",$name))
      { $this->RESULT['name']=$name;
        break;
      }
    }
  }

  function setSort()
  {
    $sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
	$i=1;
	foreach($sort as $id)
	A::$DB->Update("mysite_sections",array('sort'=>$i++),"id=".(integer)$id);
  }
}

A::$REQUEST = new SiteSections_Request;