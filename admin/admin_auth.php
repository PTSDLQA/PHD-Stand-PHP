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

class AdminAuth extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("admin_auth.tpl");
  }

  function Action($action)
  {
	switch($action)
	{ case "remember": $this->Remember(); break;
	}
  }

  function Remember()
  {
	if(empty($_REQUEST['login']))
	{ $this->errors['notlogin']=true;
	  return false;
	}

	require_once("system/mail/mail.php");

	if($row=A::$DB->getRow("SELECT * FROM _auth WHERE login=?",$_REQUEST['login']))
    {
      if(empty($row['email']))
	  { $this->errors['notemail']=true;
	    return false;
	  }

	  if(!empty($_GET['code']) && $_GET['code']==md5($row['date'].$row['password']))
	  {
	    $row['password']=mb_substr(md5(time()),0,10);

	    $mail = new htmlMimeMail();
	    $mail->setFrom(A::$OPTIONS['mailsfrom']);

	    $smarty = new Smarty();
	    $smarty->template_dir='templates/admin/mails/';
	    $smarty->compile_dir='templates_c/admin/mails/';
        $smarty->Assign("data",$row);
	    $smarty->Assign("domain",HOSTNAME);

	    $message=preg_replace("/\r/","",$smarty->fetch("remember.tpl"));
	    $subject=preg_match("/^([^\n]*)\n/i",$message,$matches)?$matches[1]:"";
	    $message=preg_replace("/^[^\n]*\n/i","",$message);
	    $mail->setSubject($subject);
	    $mail->setText($message);
        $mail->send(array($row['email']),"mail");

	    A::$DB->execute("UPDATE _auth SET password='".md5($row['password'])."' WHERE id=".$row['id']);

	    A::goUrl("admin.php?message=2");
	  }
	  else
	  {
	    $mail = new htmlMimeMail();
	    $mail->setFrom(A::$OPTIONS['mailsfrom']);

	    $smarty = new Smarty();
	    $smarty->template_dir='templates/admin/mails/';
	    $smarty->compile_dir='templates_c/admin/mails/';
        $smarty->Assign("data",$row);
        $smarty->Assign("link","http://".HOSTNAME."/admin.php?action=remember&login={$row['login']}&code=".md5($row['date'].$row['password']));
	    $smarty->Assign("domain",HOSTNAME);

	    $message=preg_replace("/\r/","",$smarty->fetch("pre.tpl"));
	    $subject=preg_match("/^([^\n]*)\n/i",$message,$matches)?$matches[1]:"";
	    $message=preg_replace("/^[^\n]*\n/i","",$message);
	    $mail->setSubject($subject);
	    $mail->setText($message);
        $mail->send(array($row['email']),"mail");

        A::goUrl("admin.php?message=1");
	  }
	}
	else
	{ $this->errors['notlogin']=true;
	  return false;
	}
  }
}

A::$MAINFRAME = new AdminAuth;