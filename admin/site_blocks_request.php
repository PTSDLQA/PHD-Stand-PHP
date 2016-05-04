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

class SiteBlocks_Request extends A_Request
{
  function Action($action)
  {
    switch($action)
	{ case "getaddblockform": $this->getAddBlockForm(); break;
	  case "geteditblockform": $this->getEditBlockForm(); break;
	  case "getshowoptions": $this->getShowOptions(); break;
	  case "setsort": $this->setSort(); break;
	}
  }

  function in_extensions($owner,$extensions)
  {
    foreach($extensions as $extname)
	if(preg_match("/^".$owner."/i",$extname))
	return true;
	return false;
  }

  function getAddBlockForm()
  {
    $form = new A_Form("site_blocks_add.tpl");

	$extensions=array();
	A::$DB->query("SELECT DISTINCT module FROM mysite_sections");
	while($row=A::$DB->fetchRow())
	$extensions[]=$row['module'];
	A::$DB->free();

	A::$DB->query("SELECT * FROM _extensions WHERE type='block' ORDER BY sort,id");
    while($row=A::$DB->fetchRow())
    if(!empty($row['owner']))
	{ $owners=explode(",",$row['owner']);
	  foreach($owners as $owner)
	  if($this->in_extensions($owner,$extensions))
	  { $form->data['blocks'][$row['name']]=$row['caption'];
	    break;
	  }
	}
	else
  	$form->data['blocks'][$row['name']]=$row['caption'];
	A::$DB->free();

	$form->data['slanguages']=array('ru'=>'Русский');
	$form->data['lang']='ru';

	$form->data['sections']=array();
	$form->data['items']=array();
	A::$DB->query("SELECT * FROM mysite_sections ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $section="mysite_ru_".$row['name'];
	  $row['pages']=array();
	  A::$DB->query("SELECT * FROM mysite_templates WHERE idsec={$row['id']} ORDER BY id");
	  while($row2=A::$DB->fetchRow())
	  $row['pages'][]=array('page'=>$row2['name'],'caption'=>$row2['caption']);
	  A::$DB->free();
	  $form->data['sections'][]=$row;

	  $item="mysite_ru_".$row['name'];
	  $form->data['items'][$item]=$row['caption'];
	}

	$this->RESULT['html']=$form->getContent();
  }

  function getEditBlockForm()
  {
    $form = new A_Form("site_blocks_edit.tpl");
	$form->data=A::$DB->getRowById($_POST['id'],"mysite_blocks");

	$sections=empty($form->data['show'])?array():unserialize($form->data['show']);

	$extensions=array();
	A::$DB->query("SELECT DISTINCT module FROM mysite_sections");
	while($row=A::$DB->fetchRow())
	$extensions[]=$row['module'];
	A::$DB->free();

	A::$DB->query("SELECT * FROM _extensions WHERE type='block' ORDER BY sort,id");
    while($row=A::$DB->fetchRow())
    if(!empty($row['owner']))
	{ $owners=explode(",",$row['owner']);
	  foreach($owners as $owner)
	  if($this->in_extensions($owner,$extensions))
	  { $form->data['blocks'][$row['name']]=$row['caption'];
	    break;
	  }
	}
	else
  	$form->data['blocks'][$row['name']]=$row['caption'];
	A::$DB->free();

	$form->data['slanguages']=array('ru'=>'Русский');

	$form->data['showall']=count($sections)==0;
	$form->data['sections']=array();
	$form->data['items']=array();
	A::$DB->query("SELECT * FROM mysite_sections ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $section="mysite_ru_".$row['name'];
	  $row['pages']=array();
	  $row['checked']=isset($sections[$row['id']]);
	  A::$DB->query("SELECT * FROM mysite_templates WHERE idsec={$row['id']} ORDER BY id");
	  while($row2=A::$DB->fetchRow())
	  $row['pages'][]=array('page'=>$row2['name'],'caption'=>$row2['caption'],'checked'=>isset($sections[$row['id']]) && in_array($row2['name'],$sections[$row['id']]));
	  A::$DB->free();
	  $form->data['sections'][]=$row;

	  $item="mysite_ru_".$row['name'];
	  $form->data['items'][$item]=$row['caption'];
	}

	$this->RESULT['html']=$form->getContent();
	$this->RESULT['block']=$form->data['block'];
  }

  function getShowOptions()
  {
    $form = new A_Form("site_blocks_showoptions.tpl");

	if($_POST['id']>0)
	{ $form->data=A::$DB->getRowById($_POST['id'],"mysite_blocks");
	  $sections=empty($form->data['show'])?array():unserialize($form->data['show']);
	}
	else
	$sections=array();

	$form->data['showall']=count($sections)==0;
	$form->data['sections']=array();
	A::$DB->query("SELECT * FROM mysite_sections ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $section="mysite_ru_".$row['name'];
	  $row['pages']=array();
	  $row['checked']=isset($sections[$row['id']]);
	  A::$DB->query("SELECT * FROM mysite_templates WHERE idsec={$row['id']} ORDER BY id");
	  while($row2=A::$DB->fetchRow())
	  $row['pages'][]=array('page'=>$row2['name'],'caption'=>$row2['caption'],'checked'=>isset($sections[$row['id']]) && in_array($row2['name'],$sections[$row['id']]));
	  A::$DB->free();
	  $form->data['sections'][]=$row;
	}

	$this->RESULT['html']=$form->getContent();
  }

  function setSort()
  {
    $sort=!empty($_POST['sort'])?explode(",",$_POST['sort']):array();
	$i=1;
	foreach($sort as $id)
	A::$DB->Update("mysite_blocks",array('sort'=>$i++),"id=".(integer)$id);
  }
}

A::$REQUEST = new SiteBlocks_Request;