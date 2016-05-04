<?php
/** \file system/framework/request.php
 * Серверная сторона AJAX.
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

require_once("system/jshttprequest/jshttprequest.php");

/**
 * Интерфейс обработчиков действий серверной стороны AJAX.
 */

interface A_iRequest
{
/**
 * Переопределяемый метод маршрутизатора действий.
 *
 * @param string $action Идентификатор действия.
 */

  function Action($action);
}

/**
 * Обработчик действий серверной стороны AJAX.
 */

class A_Request implements A_iRequest
{
/**
 * Массив в который заносятся сформированные данные.
 */

  protected $RESULT;

/**
 * Конструктор.
 */

  function __construct()
  { global $_RESULT;

	$this->RESULT =& $_RESULT;
  }


/**
 * Переопределяемый метод маршрутизатора действий.
 *
 * @param string $action Идентификатор действия.
 */

  function Action($action)
  {
  }
}

/**
 * Объект JsHttpRequest.
 */

$A_JSHTTPREQUEST = new JsHttpRequest('utf-8');