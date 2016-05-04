<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Blocks
 */
/**************************************************************************/

/**
 * Серверная сторона AJAX настройки блока "Список ссылок на страницы".
 *
 * <a href="http://wiki.a-cms.ru/blocks/pages">Руководство</a>.
 */

class pages_BlockRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "add": $this->Add(); break;
	  case "edit": $this->Edit(); break;
	  case "getdirs": $this->getDirs(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму добавления.
 */

  function Add()
  {
	$form = new A_Form("block_pages_add.tpl");
	$form->data['sections']=A::$DB->getAssoc("SELECT id,caption FROM ".DOMAIN."_sections WHERE module='pages' ORDER BY sort");
	$form->data['dirs']=array();
	$section=getSectionById(key($form->data['sections']));
	$this->_getDirs($section,$form->data['dirs']);
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования.
 */

  function Edit()
  {
	$form = new A_Form("block_pages_edit.tpl");
	$block=A::$DB->getRowById($_POST['id'],DOMAIN."_blocks");
	$form->data=!empty($block['params'])?unserialize($block['params']):array();
	$form->data['sections']=A::$DB->getAssoc("SELECT id,caption FROM ".DOMAIN."_sections WHERE module='pages' ORDER BY sort");
	$form->data['dirs']=array();
	$section=getSectionById($form->data['idsec']);
	$this->_getDirs($section,$form->data['dirs']);
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает список подразделов.
 */

  function getDirs()
  {
    $dirs=array();
	$section=getSectionById($_POST['idsec']);
	$this->_getDirs($section,$dirs,0);
	$this->RESULT['ids']=array();
	$this->RESULT['names']=array();
	foreach($dirs as $id=>$name)
	{ $this->RESULT['ids'][]=$id;
	  $this->RESULT['names'][]=$name;
	}
  }

  private function _getDirs($section,&$values,$id=0)
  {
    A::$DB->query("SELECT * FROM {$section} WHERE idker=$id AND type='dir' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $childs=A::$DB->getOne("SELECT COUNT(*) FROM {$section} WHERE idker=".$row['id']);
	  if($childs>0)
	  { $name="";
        for($i=0;$i<$row['level'];$i++)
	    $name.=" > ";
        $values[$row['id']]=$name.$row['name'];
        $this->_getDirs($section,$values,$row['id']);
	  }
    }
	A::$DB->free();
  }
}

A::$REQUEST = new pages_BlockRequest;