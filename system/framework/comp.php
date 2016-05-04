<?php
/** \file system/framework/comp.php
 * Специальный компонент.
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
 * Интерфейс специального компонента в панели управления.
 * Компоненты используются только в панели управления для часто используемых функций.
 */

interface A_iComponent
{

/**
 * Метод формирования данных доступных в шаблоне.
 */

  function createData();
}

/**
 * Базовый класс для всех специальных компонентов в панели управления.
 */

class A_Component extends Smarty implements A_iComponent
{
/**
 * Файл шаблона.
 */

  public $template;


/**
 * Конструктор.
 *
 * @param string $template Шаблон компонента.
 */

  function __construct($template)
  {
	$this->template=$template;

    $this->template_dir=SMARTY_TEMPLATES."/objcomp/";
	$this->compile_dir=SMARTY_COMPILE."/objcomp/";

	if(!empty($_REQUEST['obj_action']) && !empty($_REQUEST['authcode']) && $_REQUEST['authcode']==A::$AUTH->authcode)
	$this->Action($_REQUEST['obj_action']);
  }

/**
 * Переопределяемый метод маршрутизатора действий.
 *
 * @param string $action Идентификатор действия.
 */

  function Action($action)
  {
  }

/**
 * Переопределяемый метод формирования данных доступных в шаблоне.
 */

  function createData()
  {
  }

/**
 * Метод возвращает сгенерированное содержимое компонента.
 *
 * @return string Содержимое компонента.
 */

  function getContent()
  {
	$this->Assign_by_ref("system",A::getSystem());
    $this->Assign_by_ref("auth",A::$AUTH);

    $this->createData();

	return $this->fetch($this->template);
  }
}