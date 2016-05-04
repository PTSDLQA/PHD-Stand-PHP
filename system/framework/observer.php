<?php
/** \file system/framework/observer.php
 * Система событий.
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
 * Класс реализует механизмы системы событий.
 */

class A_Observer
{
  private static $instance;
  private $events;
  private $modifiers;

/**
 * Конструктор.
 */

  function __construct()
  {
    $this->events=array();
    $this->modifiers=array();
  }

/**
 * Возвращает одиночный объект класса.
 */

  function getInstance()
  {
    if(!self::$instance)
    self::$instance = new A_Observer;
    return self::$instance;
  }

/**
 * Инициирует событие.
 *
 * @param string $event Идентификатор события.
 * @param string $item Идентификатор владельца.
 * @param array $params=null Параметры.
 */

  function Event($event,$item,$params=null)
  {
    if(!empty($this->events[$event=strtolower($event)]))
	foreach($this->events[$event] as $handler)
	call_user_func($handler,$item,$params);
  }

/**
 * Инициирует модификатор.
 *
 * @param string $modifier Идентификатор модификатора.
 * @param string $item Идентификатор владельца.
 * @param array $data Данные для модификации.
 * @return array Обработанные данные.
 */

  function Modifier($modifier,$item,$data)
  {
    if(!empty($this->modifiers[$modifier=strtolower($modifier)]))
	foreach($this->modifiers[$modifier] as $handler)
	$data=call_user_func($handler,$item,$data);
	return $data;
  }

/**
 * Добавляет обработчик события.
 *
 * @param string $event Идентификатор события.
 * @param string $handler Функция обработчик.
 */

  function AddHandler($event,$handler)
  {
    $this->events[strtolower($event)][]=$handler;
  }

/**
 * Добавляет обработчик модификатора.
 *
 * @param string $modifier Идентификатор события.
 * @param string $handler Функция обработчик.
 */

  function AddModifier($modifier,$handler)
  {
    $this->modifiers[strtolower($modifier)][]=$handler;
  }

/**
 * Удаляет все обработчики событий и модификаторов.
 */

  function Clear()
  {
    $this->events=array();
    $this->modifiers=array();
  }
}