<?php
/** \file system/framework/cache.php
 * Система кэширования.
 */
/**
 * @project Astra.CMS Free Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AFramework
 */
/**************************************************************************/

/**
 * Общая система кэширования.
 * (Доступно только в полной версии)
 */

class A_Cache
{
  private static $instance;

/**
 * Объект системы кэширования БД.
 */

  public $db;

/**
 * Объект системы кэширования страниц.
 */

  public $page;

/**
 * Объект системы кэширования блоков.
 */

  public $block;

/**
 * Конструктор.
 */

  function __construct()
  {

  }

/**
 * Возвращает объект системы кэширования.
 * @return object Объект системы кэширования.
 */

  function getInstance()
  {
    if(!self::$instance)
    self::$instance = new A_Cache;
    return self::$instance;
  }

/**
 * Сбрасывает весь кэш связанный с таблицей БД.
 * @param string $table Таблица БД.
 */

  function resetTable($table)
  {
  }

/**
 * Сбрасывает весь кэш связанный с разделом.
 * @param string $section Полный строковой идентификатор раздела.
 */

  function resetSection($section)
  {
  }

/**
 * Сбрасывает весь кэш связанный с сайтом.
 * @param string $domain Идентификатор сайта.
 */

  function resetDomain($domain)
  {
  }

/**
 * Сбрасывает весь кэш.
 */

  function resetAll()
  {
  }
}