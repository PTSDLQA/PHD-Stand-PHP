<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Modules
 */
/**************************************************************************/

/**
 * Модуль "Обратная связь".
 *
 * <a href="http://wiki.a-cms.ru/modules/feedback">Руководство</a>.
 */

class FeedbackModule extends A_MainFrame
{
/**
 * Маршрутизатор URL.
 *
 * @param array $uri Элементы полного пути URL.
 */

  function Router($uri)
  {
	if(count($uri)==1)
	{ if(reset($uri)=="message.html")
	  $this->page="message";
	  else
	  A::NotFound();
	}
	elseif(count($uri)==0)
	$this->page="main";
	else
    A::NotFound();
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
	{ case "send": $res=$this->sendMessage(); break;
	}
  }

/**
 * Обработчик действия: Отправка сообщения.
 */

  function sendMessage()
  {
	if(empty($_REQUEST['captcha']) || md5(strtolower($_REQUEST['captcha']))!=A_Session::get('captcha'))
	{ $this->errors['captcha']=true;
	  return false;
	}
	A_Session::unregister('captcha');

	$mail = new A_Mail(A::$OPTIONS['template'],"html");
	if(!empty($_REQUEST['email']))
	$mail->setFrom($_REQUEST['email'],!empty($_REQUEST['name'])?$_REQUEST['name']:'');
	$mail->Assign("data",$_REQUEST);

    $fields=array();
    A::$DB->query("SELECT * FROM ".DOMAIN."_fields WHERE item='".SECTION."' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { if($row['type']=="select"||$row['type']=="mselect")
      { $row['options']=loadList($row['property']);
		if($row['type']=="mselect")
        { $row['value']=array();
          $values=isset($_REQUEST[$row['field']])?$_REQUEST[$row['field']]:array();
		  foreach($values as $value)
		  $row['value'][]=isset($row['options'][$value])?(is_array($row['options'][$value])?$row['options'][$value]['name']:$row['options'][$value]):"";
		  $row['value']=implode(", ",$row['value']);
		}
		else
		{ $row['value']=isset($_REQUEST[$row['field']])?(integer)$_REQUEST[$row['field']]:0;
		  $row['value']=isset($row['options'][$row['value']])?$row['options'][$row['value']]:"";
		  if(is_array($row['value']))
		  { $row['data']=$row['value'];
		    $row['value']=!empty($row['data']['name'])?$row['data']['name']:"";
		  }
		}
      }
	  elseif($row['type']=="file")
	  { if(isset($_FILES[$row['field']]['tmp_name']) && is_file($_FILES[$row['field']]['tmp_name']))
	    $mail->addAttachment($_FILES[$row['field']]['tmp_name'],$_FILES[$row['field']]['name'],$_FILES[$row['field']]['type']);
	  }
      else
      $row['value']=isset($_REQUEST[$row['field']])?strip_tags($_REQUEST[$row['field']]):"";
	  if($row['type']=="float")
      $row['value']=round($row['value'],2);
      $row['name']=$row['name_'.LANG];
      $fields[$row['field']]=$row;
    }
	A::$DB->free();
    $mail->Assign("fields",$fields);

	if(isset($fields['subject']))
	$mail->setSubject($fields['subject']['value']);

	if(isset($_REQUEST['mailto']) && isset($fields['mailto']['options'][$_REQUEST['mailto']]['email']))
	$mail->send($fields['mailto']['options'][$_REQUEST['mailto']]['email']);
	elseif(!empty(A::$OPTIONS['email']))
	$mail->send(A::$OPTIONS['email']);

	$data=array('date'=>time(),'message'=>$mail->getContent(),'data'=>serialize($fields));
	if(A::$AUTH->isLogin())
	$data['iduser']=A::$AUTH->id;

	if($id=A::$DB->Insert(SECTION."_arch",$data))
	{ A_Session::set(SECTION."_id",$id);
	  A::goUrl(getSectionLink(SECTION)."message.html");
	}
	else
	return false;
  }

/**
 * Формирование данных доступных в шаблоне активного типа.
 */

  function createData()
  {
	switch($this->page)
	{ case "main": $this->MainPage(); break;
	  case "message": $this->MessagePage(); break;
	}
  }

/**
 * Формирование данных доступных в шаблоне главной страницы раздела.
 */

  function MainPage()
  {
    $this->Assign("form",$_REQUEST);
	$this->prepareAddForm();
	$this->Assign("content",getTextOption(SECTION,'content'));
	$this->Assign("captcha",$captcha=substr(time(),rand(0,6),4));
	A_Session::set("captcha",md5($captcha));

	$this->AddNavigation(SECTION_NAME);
  }

/**
 * Формирование данных доступных в шаблоне страницы сообщения.
 */

  function MessagePage()
  {
    $fields=array();
	if($id=A_Session::get(SECTION."_id",0))
    { if($arch=A::$DB->getRowById($id,SECTION."_arch"))
      $fields=!empty($arch['data'])?unserialize($arch['data']):array();
	}
	$this->Assign("fields",$fields);

	$this->AddNavigation(SECTION_NAME,getSectionLink(SECTION));
  }
}

A::$MAINFRAME = new FeedbackModule;