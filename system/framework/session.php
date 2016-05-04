<?php
/** \file system/framework/session.php
 * Система сессии.
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
 * Класс реализует механизмы работы с данными сессии.
 */

class A_Session
{
/**
 * Инициализация сессии.
 *
 * @param string $name='SessionID' Название.
 * @param string $id=null Идентификатор.
 */

  function start($name='SessionID',$id=null)
  {
    A_Session::name($name);

	if(is_null(A_Session::detectID()))
	A_Session::id($id?$id:uniqid(dechex(rand())));

    if(defined('A_MODE') && A_MODE==1)
	ini_set("session.gc_maxlifetime", 3600);

	@session_start();
  }

/**
 * Уничтожение сессии.
 */

  function destroy()
  {
    session_unset();
    session_destroy();
  }

/**
 * Удаление данных сессии.
 */

  function clear()
  {
    session_unset();
  }

/**
 * Регистрация переменной.
 *
 * @param string $name Имя переменной.
 */

  function register($name)
  {
    session_register($name);
  }

/**
 * Удаление переменной.
 *
 * @param string $name Имя переменной.
 */

  function unregister($name)
  {
    session_unregister($name);
  }

/**
 * Сохранение значения переменной.
 *
 * @param string $name Имя переменной.
 * @param string $value Значение.
 */

  function set($name,$value)
  {
    $return=(isset($_SESSION[$name]))?$_SESSION[$name]:null;
    if(null === $value)
	unset($_SESSION[$name]);
    else
	$_SESSION[$name]=$value;
	return $return;
  }

/**
 * Извлечение значения переменной.
 *
 * @param string $name Имя переменной.
 * @param string $default=null Значение возвращается если переменной не существует.
 * @return mixed Значение переменной.
 */

  function get($name,$default=null)
  {
    return isset($_SESSION[$name])?$_SESSION[$name]:$default;
  }

/**
 * Проверка существования переменной в сессии.
 *
 * @param string $name Имя переменной.
 * @return boolean Результат проверки.
 */

  function is_set($name)
  {
    return isset($_SESSION[$name]);
  }

  private function name($name=null)
  {
    return isset($name)?session_name($name):session_name();
  }

  private function id($id=null)
  {
    return isset($id)?session_id($id):session_id();
  }

  private function detectID()
  {
    if(A_Session::useCookies())
	{ if(isset($_COOKIE[A_Session::name()]))
	  return $_COOKIE[A_Session::name()];
      else
	  { if(isset($_GET[A_Session::name()]))
		return $_GET[A_Session::name()];
		if(isset($_POST[A_Session::name()]))
		return $_POST[A_Session::name()];
      }
      return null;
    }
  }

  private function useCookies($useCookies=null)
  {
    $return=ini_get('session.use_cookies')?true:false;
    if(isset($useCookies))
	ini_set('session.use_cookies', $useCookies?1:0);
    return $return;
  }
}