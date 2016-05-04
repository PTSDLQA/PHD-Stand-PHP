<?php
/** \file system/framework/navigation.php
 * Строка навигации.
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
 * Класс строки навигации (хлебные крошки).
 */

class A_Navigation extends Smarty
{
/**
 * Шаблон.
 */

  public $template;

/**
 * Массив элементов строки навигации.
 */

  public $navigation=array();

/**
 * Конструктор.
 */

  function __construct()
  {
    $this->template_dir=SMARTY_TEMPLATES."/others/";
	$this->compile_dir=SMARTY_COMPILE."/others/";

	$this->template="navigation.tpl";
  }

/**
 * Добавление элемента в строку.
 *
 * @param string $name Название.
 * @param string $link='' Ссылка.
 */

  function Add($name,$link='')
  {
    $this->navigation[]=array("name"=>$name,"link"=>$link);
  }


/**
 * Метод возвращает сгенерированный HTML код строки навигации.
 *
 * @param string $template=null Шаблон, если не указано то navigation.tpl.
 * @return string HTML код строки навигации.
 */

  function getContent($template=null)
  {
	if($this->navigation)
	{ $this->Assign_by_ref("navigation",$this->navigation);
	  $this->Assign_by_ref("system",A::getSystem());
      $this->Assign_by_ref("auth",A::$AUTH);
	  return $this->fetch(!empty($template)?$template:$this->template);
	}
	else
	return "";
  }
}