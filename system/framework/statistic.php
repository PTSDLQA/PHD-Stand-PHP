<?php
/** \file system/framework/statistic.php
 * Статистика раздела или дополнения.
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
 * Интерфейс блока статистики раздела или дополнения.
 */

interface A_iStatistic
{
/**
 * Переопределяемый метод формирования данных доступных в шаблоне.
 */

  function createData();
}

/**
 * Базовый класс для блоков статистики разделов и дополнений.
 * Используются только в панели управления.
 */


class A_Statistic extends Smarty implements A_iStatistic
{
/**
 * Тип статистики: раздел или дополнение.
 */

  public $mode;

/**
 * Идентификатор модуля или плагина.
 */

  public $extension;

/**
 * Полный строковой идентификатор дополнения.
 */

  public $structure;

/**
 * Полный строковой идентификатор раздела.
 */

  public $section;

/**
 * Конструктор.
 *
 * @param string $item Полный строковой идентификатор раздела или дополнения.
 * @param string $extension Идентификатор модуля или плагина.
 */

  function __construct($item,$extension='')
  {

	$this->section=$item;
	$this->structure=$item;
	$this->extension=$extension;

	if(empty($this->mode))
	{ $parse=parseSection($item);
	  $this->template="statistic_module_{$extension}.tpl";
	  $this->mode='module';
	}
	else
    $this->template="statistic_{$this->mode}_{$extension}.tpl";

    $this->template_dir=SMARTY_TEMPLATES."/others/";
	$this->compile_dir=SMARTY_COMPILE."/others/";
  }

/**
 * Переопределяемый метод формирования данных доступных в шаблоне.
 */

  function createData()
  {
  }

/**
 * Метод возвращает сгенерированное содержимое блока статистики.
 *
 * @return string Содержимое блока статистики.
 */

  function getContent()
  {
    $this->createData();

	$path1=$this->template_dir.$this->template;
	switch($this->mode)
	{ case 'module': $path2='modules/'.$this->extension.'/templates/admin/others/'.$this->template; break;
	  case 'plugin': $path2='plugins/'.$this->extension.'/templates/admin/others/'.$this->template; break;
	}
	if(!empty($path2) && is_file($path2) && (!is_file($path1) || filemtime($path2)>filemtime($path1)))
	copyfile($path2,$path1,true);

    return $this->fetch($this->template);
  }
}