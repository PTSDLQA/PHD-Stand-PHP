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

class SystemAdmins extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("system_admins.tpl");

	$this->AddJScript("/admin/jscripts/system_admins.js");
  }

  function Action($action)
  {
    $res=false;
	switch($action)
	{ case "add": $res=$this->Add(); break;
	  case "edit": $res=$this->Edit(); break;
	  case "del": $res=$this->Del(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=system&item=admins",array('page'));
  }

  function Add()
  {
  	if(A::$DB->existsRow("SELECT * FROM _auth WHERE login=?",$_REQUEST['login']))
	{ $this->errors['doublelogin']=true;
	  return false;
	}

    $_REQUEST['date']=time();
	$_REQUEST['password']=md5($_REQUEST['password']);
	$_REQUEST['active']=isset($_REQUEST['active'])?'Y':'N';

	$dataset = new A_DataSet("_auth");
	$dataset->fields=array("date","name","login","password","email","active");
	return $dataset->Insert();
  }

  function Edit()
  {
	if(A::$DB->existsRow("SELECT * FROM _auth WHERE login=? AND id<>".(integer)$_REQUEST['id'],$_REQUEST['login']))
	{ $this->errors['doublelogin']=true;
	  return false;
	}

	$_REQUEST['active']=isset($_REQUEST['active'])?'Y':(A::$AUTH->id!=(integer)$_REQUEST['id']?'N':'Y');

    $dataset = new A_DataSet("_auth");
	$dataset->fields=array("name","login","email","active");
    if(mb_strlen($_REQUEST['password'])>=4)
    { $_REQUEST['password']=md5($_REQUEST['password']);
	  $dataset->fields[]="password";
	}
	return $dataset->Update();
  }

  function Del()
  {
    if($_REQUEST['id']!=A::$AUTH->id)
	{
      A::$DB->execute("DELETE FROM _auth WHERE id=".(integer)$_REQUEST['id']);
	  return true;
	}
	else
	return false;
  }

  function createData()
  {
	$this->Assign("caption","Администраторы");

    $admins=array();
    $pager = new A_Pager(20);
	$pager->query("SELECT * FROM _auth ORDER BY id");
    while($row=$pager->fetchRow())
    $admins[]=$row;
    $pager->free();

    $this->Assign("admins",$admins);
    $this->Assign("admins_pager",$pager);
  }
}

A::$MAINFRAME = new SystemAdmins;