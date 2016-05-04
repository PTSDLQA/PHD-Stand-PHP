<?php
/** \file system/framework/auth.php
 * Система авторизации.
 */
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AFramework
 */
/**************************************************************************/

/**
 * Интерфейс классов авторизации.
 */

interface iAuth
{
/**
 * Авторизация.
 */

  function Login();

/**
 * Выход.
 */

  function Logout();

/**
 * Проверка авторизации.
 */

  function isLogin();

/**
 * Проверка авторизации c дополнительным контрольным кодом.
 */

  function isLoginCode();

}

/**
 * Класс реализует систему авторизации администратора.
 */

class A_Auth_Admin implements iAuth
{
/**
 * Числовой идентификатор администратора.
 */

  public $id=0;

/**
 * Массив с данными администратора.
 */

  public $data;

/**
 * Уникальный код авторизации.
 */

  public $authcode;

/**
 * Конструктор.
 */

  function __construct()
  {
	$AUTH_ID = A_Session::get('admin_auth_id',0);
	$AUTH_PASS = A_Session::get('admin_auth_pass','');

	if($this->data=A::$DB->getRowById($AUTH_ID,"_auth"))
	{
	  $privatecode=!empty($GLOBALS['A_PRIVATECODE'])?$GLOBALS['A_PRIVATECODE']:'';

	  if($AUTH_PASS==md5($this->data['authcode'].$this->data['password'].$privatecode))
	  { $this->id=$AUTH_ID;
	    $this->authcode=$this->data['authcode'];
	    $this->data['access']=array();
	    if($this->data['dauth']<time())
	    A::$DB->execute("UPDATE _auth SET dauth=".(time()+600)." WHERE id=".(integer)$AUTH_ID);
	  }
	}
  }

/**
 * Авторизация, данные берутся из $_REQUEST.
 * login - логин администратора.
 * password - пароль администратора.
 */

  function Login()
  {
	$_REQUEST['login']=preg_replace("/[^a-zA-Z0-9-_]/i","",$_REQUEST['login']);

	if(!empty($_REQUEST['login']) && !empty($_REQUEST['password']))
    { if($row=A::$DB->getRow("SELECT * FROM _auth WHERE login=? AND password=? AND active='Y'",array($_REQUEST['login'],md5($_REQUEST['password']))))
      { $privatecode=!empty($GLOBALS['A_PRIVATECODE'])?$GLOBALS['A_PRIVATECODE']:'';
	    $authcode=md5(time());

		A_Session::set('admin_auth_id',$row['id']);
		A_Session::set('admin_auth_pass',md5($authcode.$row['password'].$privatecode));

		A::$DB->execute("UPDATE _auth SET authcode='$authcode' WHERE id=".$row['id']);
      }
    }
    A::goUrl("/admin.php");
  }

/**
 * Выход.
 */

  function Logout()
  {
    A_Session::unregister('admin_auth_id');
	A_Session::unregister('admin_auth_pass');

	A::goUrl('/');
  }

/**
 * Проверка авторизации.
 */

  function isLogin()
  {
    return $this->id>0;
  }

/**
 * Проверка авторизации c дополнительным контрольным кодом.
 */

  function isLoginCode()
  {
    return $this->id>0 && !empty($_REQUEST['authcode']) && $this->data['authcode']==$_REQUEST['authcode'];
  }

/**
 * Проверка прав администратора (полный доступ к сайту).
 */

  function isAdmin()
  {
    return $this->id>0;
  }

/**
 * Проверка прав администратора (полный доступ к системе).
 */

  function isSuperAdmin()
  {
    return $this->id>0;
  }

/**
 * Проверка активности эксперного режима.
 */

  function isExpert()
  {
    return $this->id>0;
  }
}

/**
 * Класс реализует систему авторизации пользователя.
 * Является "заглушкой" в случае если не используется модуль "Пользователи" со своей системой авторизации.
 */

class A_Auth_User implements iAuth
{
/**
 * Числовой идентификатор пользователя.
 */

  public $id=0;

/**
 * Массив с данными пользователя.
 */

  public $data=array();

/**
 * Уникальный код авторизации.
 */

  public $authcode;

/**
 * Полный идентификатор раздела.
 */

  public $section;


/**
 * Авторизация.
 */

  function Login()
  {
  }

/**
 * Выход.
 */

  function Logout()
  {
  }

/**
 * Проверка авторизации.
 */

  function isLogin()
  {
    return $this->id>0;
  }

/**
 * Проверка авторизации c дополнительным контрольным кодом.
 */

  function isLoginCode()
  {
    return $this->id>0 && !empty($_REQUEST['authcode']) && $this->data['authcode']==$_REQUEST['authcode'];
  }
}

/**
 * Контейнер объектов авторизации.
 */

class A_Auth
{
  private static $instance1;
  private static $instance2;

/**
 * Возвращает объект авторизации. В зависимости от расположения (панель или сайт) возвращает объект администратора или пользователя.
 * @return object Объект авторизации.
 */

  function getInstance()
  {
    if(!self::$instance1)
	{ if(A_MODE==A_MODE_FRONT)
      self::$instance1 = new A_Auth_User();
	  else
	  self::$instance1 = new A_Auth_Admin();
	}
    return self::$instance1;
  }

/**
 * Возвращает объект авторизации администратора (может использоваться на сайте).
 * @return object Объект авторизации.
 */

  function getInstanceAdmin()
  {
    if(!self::$instance2)
	self::$instance2 = new A_Auth_Admin();
	return self::$instance2;
  }

/**
 * Устанавливает объект авторизации.
 *
 * @param object $obj Объект авторизации.
 */

  function setInstance($obj)
  {
    self::$instance1 = $obj;
	A::$AUTH = $obj;
  }
}