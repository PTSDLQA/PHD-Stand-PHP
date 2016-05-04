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
 * Серверная сторона AJAX для компонента редактирования шаблонов.
 */

class A_TplEditor_Request extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
	 switch($action)
     { case "edittpl": $this->edittpl(); break;
	   case "savetpl": $this->savetpl(); break;
     }
  }

/**
 * Обработчик действия: Отдает форму редактирования файла шаблона.
 */

  function edittpl()
  {
	if(preg_match("/^([a-zA-Z0-9\/_-]+)\.(tpl|xml)$/i",$_POST['path'],$matches))
	$_POST['path']=$matches[1].".".$matches[2];
	else
	return;

	$form = new A_Form("objcomp_tpleditor_edit.tpl");
	$form->data['path']=$_POST['path'];
	$form->data['text']=@file_get_contents("templates/mysite/".$_POST['path']);
    $this->RESULT['height']=650;

	$form->data['tpls']=array();

	if(preg_match_all("/\{block id=\"([a-z0-9-_]+)\"/i",$form->data['text'],$matches))
	foreach($matches[1] as $block)
	if($block=A::$DB->getRow("SELECT * FROM mysite_blocks WHERE name=?",$block))
	{ $params=!empty($block['params'])?unserialize($block['params']):array();
	  if(!empty($params['template']))
	  $form->data['tpls']['blocks/'.$params['template']]='> blocks/'.$params['template'];
	}

	if(strpos($_POST['path'],'blocks/')===0)
	{ $blocks=array();
	  A::$DB->query("SELECT * FROM mysite_blocks");
	  while($block=A::$DB->fetchRow())
	  { $params=!empty($block['params'])?unserialize($block['params']):array();
	    if(!empty($params['template']) && $params['template']==basename($_POST['path']))
	    { if(!empty($block['name']))
		  $blocks[]=$block['name'];
		  if(!empty($block['frame']))
		  $form->data['tpls']['frames/'.$block['frame']]='< frames/'.$block['frame'];
		}
	  }
	  A::$DB->free();
	  if(!empty($blocks))
	  foreach($blocks as $block)
	  { $files=scandir('templates/mysite');
	    sort($files);
	    foreach($files as $file)
	    if(preg_match("/\.tpl$/i",$file))
	    { $content=@file_get_contents('templates/mysite/'.$file);
		  if(strpos($content,"{block id=\"{$block}\"")!==false)
		  $form->data['tpls'][$file]='< '.$file;
		}
	  }
	}
	elseif(strpos($_POST['path'],'frames/')===0)
	{ A::$DB->query("SELECT * FROM mysite_blocks WHERE frame=?",basename($_POST['path']));
	  while($block=A::$DB->fetchRow())
      { $params=!empty($block['params'])?unserialize($block['params']):array();
	    if(!empty($params['template']))
	    $form->data['tpls']['blocks/'.$params['template']]='> blocks/'.$params['template'];
	  }
	  A::$DB->free();
	}
	else
	{ if(preg_match_all("/\{include file=\"([a-z0-9.-_]+)\"/i",$form->data['text'],$matches))
	  foreach($matches[1] as $file)
	  $form->data['tpls'][$file]='> '.$file;

	  $files=scandir('templates/mysite');
	  sort($files);
	  foreach($files as $file)
	  if(preg_match("/\.tpl$/i",$file))
	  { $content=@file_get_contents('templates/mysite/'.$file);
	    if(strpos($content,"{include file=\"".basename($_POST['path'])."\"")!==false)
	    $form->data['tpls'][$file]='< '.$file;
	  }
    }

	$this->RESULT['title']="Редактор файла '".basename($_POST['path'])."'";
	$this->RESULT['html']=$form->getContent();
  }

/**
 * Обработчик действия: Сохранение файла.
 */

  function savetpl()
  {
	if(preg_match("/^([a-zA-Z0-9\/_-]+)\.(tpl|xml)$/i",$_POST['path'],$matches))
	$_POST['path']=$matches[1].".".$matches[2];
	else
	return;

	$this->RESULT['result']=file_put_contents("templates/mysite/".$_POST['path'],trim($_POST['text']));
	A::$OBSERVER->Event('FileManager_UpdateFile',"templates/mysite/".$_POST['path']);
  }
}

A::$REQUEST = new A_TplEditor_Request;