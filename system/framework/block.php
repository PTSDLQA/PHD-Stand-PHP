<?php
/** \file system/framework/block.php
 * Блок.
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
 * Интерфейс блока.
 */

interface A_iBlock
{

/**
 * Переопределяемый метод формирования данных доступных в шаблоне.
 */

  function createData();
}

/**
 * Базовый класс для всех типов блоков.
 * Блоки используются только на сайте.
 */

class A_Block extends Smarty implements A_iBlock
{

/**
 * Строковой идентификатор блока.
 */

  public $id;

/**
 * Тип блока.
 */

  public $block;

/**
 * Файл шаблона.
 */

  public $template;

/**
 * Строковой идентификатор раздела-источника.
 */

  public $section;

/**
 * Ссылка на главную страницу раздела-источника.
 */

  public $sectionlink;

/**
 * Числовой идентификатор раздела.
 */

  public $section_id;

/**
 * Строковой идентификатор дополнения-источника.
 */

  public $structure;

/**
 * Числовой идентификатор дополнения-источника.
 */

  public $structure_id;

/**
 * Массив значений опций раздела-источника.
 */

  public $options=array();

/**
 * Массив значений параметров блока.
 */

  public $params=array();

/**
 * Параметры кэширования блока.
 */

  public $cache_params=array();

/**
 * Конструктор.
 *
 * @param array $params=array() Список параметров блока.
 * @param string $block='' Тип блока.
 * @param string $id='' Строковой идентификатор блока.
 */

  final function __construct($params=array(),$block='',$id='')
  {
	$this->id=$id;
	$this->block=$block;
	$this->params=$params;

	if(!empty($params['template']))
	$this->template=$params['template'];

    $this->template_dir=SMARTY_TEMPLATES."/blocks/";
	$this->compile_dir=SMARTY_COMPILE."/blocks/";

	A::$OBSERVER->Event('CreateBlock',$this->block,array('object'=>&$this));
  }

/**
 * Метод инициализирующий кэширование блока. Должен вызываться в самом начале метода createData.
 *
 * @param boolean $furi=false Зависит ли содержимое блока от url.
 */

  function supportCached($furi=false)
  {
    if(empty($this->cache_params))
	$this->cache_params=array('uri'=>$furi,'get'=>array(),'session'=>array());
  }

/**
 * Метод устанавливает get параметр от значения которого зависит содержимое блока.
 *
 * @param string $param Имя параметра.
 */

  function addCacheParam_Get($param)
  {
    $this->cache_params['get'][]=$param;
  }

/**
 * Метод устанавливает переменную сессии от значения которой зависит содержимое блока.
 *
 * @param string $param Имя переменной сессии.
 */

  function addCacheParam_Session($param)
  {
    $this->cache_params['session'][]=$param;
  }

/**
 * Переопределяемый метод формирования данных доступных в шаблоне.
 */

  function createData()
  {
  }

/**
 * Метод возвращает сгенерированное содержимое блока.
 *
 * @param array $params=array() Массив с замещаемыми параметрами блока.
 * @return string Содержимое блока.
 */

  function getContent($params=array())
  {
	foreach($params as $key=>$value)
	$this->params[$key]=$value;

	if(!empty($this->params['idsec']))
	{ $this->section=getSectionById($this->params['idsec']);
	  $this->section_id=$this->params['idsec'];
	  $this->sectionlink=getSectionLink($this->section);
	  $this->options=getOptions($this->section);
	  $this->Assign("section",$this->section);
	  $this->Assign("sectionlink",$this->sectionlink);
	}
	if(!empty($this->params['idstr']))
	{ $this->structure=getStructureById($this->params['idstr']);
	  $this->structure_id=$this->params['idstr'];
	  $this->options=getOptions($this->structure);
	}

	$this->Assign_by_ref("block",$this->block);
	$this->Assign_by_ref("id",$this->id);
	$this->Assign_by_ref("system",A::getSystem());
    $this->Assign_by_ref("auth",A::$AUTH);
    $this->Assign_by_ref("options",$this->options);

	$this->createData();

	A::$OBSERVER->Event('ShowBlock',$this->block,array('object'=>&$this));

	$this->Assign_by_ref("blocks",A::$MAINFRAME->blocks);
    $this->Assign_by_ref("parent",A::$MAINFRAME->_tpl_vars);

	if(is_file($this->template_dir.$this->template))
	return $this->fetch($this->template);
	else
	return !empty($this->params['content'])?$this->params['content']:"";
  }
}