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
 * Серверная сторона AJAX настройки блока "Список произвольных ссылок".
 *
 * <a href="http://wiki.a-cms.ru/blocks/links">Руководство</a>.
 */

class links_BlockRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "add": $this->Add(); break;
	  case "edit": $this->Edit(); break;
	}
  }

/**
 * Обработчик действия: Отдает форму добавления.
 */

  function Add()
  {
	$form = new A_Form("block_links_add.tpl");
	$form->data['links']=array(array(),array(),array(),array(),array());
	$form->data['alllinks']=$this->getAllLinks();
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Отдает форму редактирования.
 */

  function Edit()
  {
	$form = new A_Form("block_links_edit.tpl");
	$block=A::$DB->getRowById($_POST['id'],DOMAIN."_blocks");
	$form->data=!empty($block['params'])?unserialize($block['params']):array();

	$links=array();
	foreach($form->data['links'] as $row)
	{ $row2['sub']=false;
	  $links[]=$row;
	  foreach($row['sublinks'] as $row2)
	  { $row2['sub']=true;
	    $links[]=$row2;
	  }
	}
	$form->data['links']=$links;
	$form->data['count']=count($links);
	$form->data['alllinks']=$this->getAllLinks();
	$this->RESULT['html']=$form->getContent();
  }

  private function getAllLinks()
  { $links=array();
	if($sections=A::$DB->getAll("SELECT * FROM ".DOMAIN."_sections WHERE module='pages' ORDER BY sort"))
	foreach($sections as $srow)
	{ $section=DOMAIN.'_'.$srow['lang'].'_'.$srow['name'];
	  A::$DB->query("SELECT * FROM {$section} WHERE idker=0 ORDER BY sort");
	  while($row=A::$DB->fetchRow())
	  { $link='/';
	    if($srow['lang']!=DEFAULTLANG)
	    $link.=$srow['lang']!='all'?$srow['lang'].'/':(A::$LANG!=DEFAULTLANG?A::$LANG.'/':'');
	    $link.=$srow['name']!=A::$OPTIONS['mainsection']?$srow['urlname'].'/':'';
	    if($row['type']=='page')
	    { if($row['urlname']=='index')
	      $links[$link]='* '.$row['name'];
		  else
	      $links[$link.$row['urlname'].'.html']='* '.$row['name'];
	    }
	    else
	    $links[$link.$row['urlname'].'/']='* '.$row['name'];
	  }
	  A::$DB->free();
	}
	A::$DB->query("SELECT * FROM ".DOMAIN."_sections WHERE module<>'users' AND module<>'voting' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	if($row['name']!=A::$OPTIONS['mainsection'])
	{ $link='/';
	  if($row['lang']!=DEFAULTLANG)
	  $link.=$row['lang']!='all'?$row['lang'].'/':(A::$LANG!=DEFAULTLANG?A::$LANG.'/':'');
	  $links[$link.$row['urlname'].'/']='# '.$row['caption'];
	}
	A::$DB->free();
	return $links;
  }
}

A::$REQUEST = new links_BlockRequest;