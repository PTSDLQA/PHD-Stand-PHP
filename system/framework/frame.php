<?php
/** \file system/framework/frame.php
 * Контейнер HTML (Обрамление).
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
 * Класс контейнера (обертки) для блоков или форм.
 */

class A_Frame extends Smarty
{

/**
 * Шаблон контейнера.
 */

  public $template;

/**
 * Заголовок.
 */

  public $title;

/**
 * Вложенный объект блока или формы.
 */

  public $object;

/**
 * Конструктор.
 *
 * @param string $template Шаблон контейнера.
 * @param string $title Заголовок.
 * @param string $object Вложенный объект блока или формы.
 */

  function __construct($template,$title,$object)
  {
    if(A_MODE==A_MODE_FRONT)
	{ $this->template_dir=SMARTY_TEMPLATES."/frames/";
	  $this->compile_dir=SMARTY_COMPILE."/frames/";
	}
	else
	{ $this->template_dir=SMARTY_TEMPLATES."/others/";
	  $this->compile_dir=SMARTY_COMPILE."/others/";
	}

	$this->template=$template;
	$this->title=$title;
	$this->object=$object;

	A::$OBSERVER->Event('CreateFrame',$template,array('object'=>&$this));
  }

/**
 * Метод возвращает сгенерированный код контейнера вместе с вложенным объектом.
 *
 * @return string HTML код контейнера вместе с вложенным объектом.
 */

  function getContent($params=array())
  {
	A::$OBSERVER->Event('ShowFrame',$this->template,array('object'=>&$this));

	if(is_file($this->template_dir.$this->template))
	{ $this->Assign_by_ref("title",$this->title);
	  $this->Assign_by_ref("object",$this->object);
	  $this->Assign_by_ref("system",A::getSystem());
      $this->Assign_by_ref("auth",A::$AUTH);
	  $this->Assign("content",$this->object->getContent($params));
	  return $this->fetch($this->template);
	}
	else
	return $this->object->getContent($params);
  }
}