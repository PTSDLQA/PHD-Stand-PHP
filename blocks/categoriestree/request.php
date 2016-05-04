<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Blocks
 */
/**************************************************************************/

class categoriestree_BlockRequest extends A_Request
{

  function Action($action)
  {
    switch($action)
    { case "add": $this->Add(); break;
	  case "edit": $this->Edit(); break;
	}
  }

  function Add()
  {
	$form = new A_Form("block_categoriestree_add.tpl");
	$tables=A::$DB->getTables();
	$form->data['sections']=array();
	A::$DB->query("SELECT * FROM ".DOMAIN."_sections ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $section=DOMAIN."_".$row['lang']."_".$row['name'];
	  $usecats=$row['module']!='shoplite'?A::$DB->existsRow("SELECT id FROM ".DOMAIN."_options WHERE item='{$section}' AND var='usecats'"):false;
	  if(in_array($section."_categories",$tables) && (!$usecats || getOption($section,'usecats')))
	  $form->data['sections'][$row['id']]=$row['caption'];
	}
	A::$DB->free();
	$this->RESULT['html']=$form->getContent();
  }

  function Edit()
  {
	$form = new A_Form("block_categoriestree_edit.tpl");
    $block=A::$DB->getRowById($_POST['id'],DOMAIN."_blocks");
	$form->data=!empty($block['params'])?unserialize($block['params']):array();
	$tables=A::$DB->getTables();
	$form->data['sections']=array();
	A::$DB->query("SELECT * FROM ".DOMAIN."_sections");
	while($row=A::$DB->fetchRow())
	{ $section=DOMAIN."_".$row['lang']."_".$row['name'];
	  $usecats=$row['module']!='shoplite'?A::$DB->existsRow("SELECT id FROM ".DOMAIN."_options WHERE item='{$section}' AND var='usecats'"):false;
	  if(in_array($section."_categories",$tables) && (!$usecats || getOption($section,'usecats')))
	  $form->data['sections'][$row['id']]=$row['caption'];
	}
	A::$DB->free();
	$this->RESULT['html']=$form->getContent();
  }
}

A::$REQUEST = new categoriestree_BlockRequest;