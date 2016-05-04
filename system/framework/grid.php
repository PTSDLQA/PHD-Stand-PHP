<?php
/** \file system/framework/grid.php
 * Таблица HTML.
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
 * Класс таблицы.
 */

class A_Grid extends Smarty
{
/**
 * Класс таблицы.
 */

  public $template;

/**
 * Заголовок таблицы.
 */

  public $title;

/**
 * Количество столбцов.
 */

  public $cols;

/**
 * Количество строк.
 */

  public $rows;

/**
 * Список названий столбцов.
 */

  public $headers=array();

/**
 * Список названий css классов строк.
 */

  public $class=array();

/**
 * Содержимое ячеек (двумерный массив).
 */

  public $cells=array();

/**
 * Список с шириной каждого стобца.
 */

  public $width=array();

/**
 * Список с выравниванием каждого стобца.
 */

  public $align=array();

/**
 * Конструктор.
 *
 * @param integer $cols Количество столбцов в таблице.
 * @param string $template='default_grid.tpl' Шаблон таблицы.
 */

  function __construct($cols,$template='default_grid.tpl')
  {
    $this->template_dir=SMARTY_TEMPLATES."/others/";
	$this->compile_dir=SMARTY_COMPILE."/others/";

	$this->template=$template;

	$this->cols=$cols;
	$this->rows=0;

	$this->width=array_pad(array(),$cols,"");
	$this->align=array_pad(array(),$cols,"left");
  }

/**
 * Устанавливает заголовок.
 *
 * @param string $title Заголовок.
 */

  function SetTitle($title)
  {
    $this->title=$title;
  }

/**
 * Добавление строки в таблицу.
 *
 * @param array $row Массив с данными ячеек.
 * @param string $class CSS класс для строки.
 */

  function AddRow($row,$class="")
  {
    for($i=0;$i<count($row);$i++)
	if($row[$i]=="")
	$row[$i]="&nbsp;";
	array_push($this->cells,array_pad($row,$this->cols,"&nbsp;"));
    array_push($this->class,$class);
	$this->rows++;
  }

/**
 * Устанавливает значение для ячейки.
 *
 * @param integer $i Индекс строки.
 * @param integer $j Индекс столбца.
 * @param mixed $cell Значение для ячейки.
 */

  function SetCell($i,$j,$cell)
  {
    if(isset($this->cells[$i][$j]))
    $this->cells[$i][$j]=$cell;
  }

/**
 * Метод формирования дополнительных данных доступных в шаблоне.
 */

  function createData()
  {
  }

/**
 * Метод возвращает сгенерированный код контейнера вместе с вложенным объектом.
 *
 * @param string $template=null Шаблон, если не указано, то используется указанный при создании.
 * @return string HTML код таблицы.
 */

  function getContent($template=null)
  {
    $this->createData();

    $this->Assign_by_ref("title",$this->title);
	$this->Assign_by_ref("cols",$this->cols);
	$this->Assign_by_ref("rows",$this->rows);
    $this->Assign_by_ref("headers",$this->headers);
	$this->Assign_by_ref("width",$this->width);
	$this->Assign_by_ref("align",$this->align);
	$this->Assign_by_ref("cells",$this->cells);
	$this->Assign_by_ref("class",$this->class);
	$this->Assign_by_ref("system",A::getSystem());
    $this->Assign_by_ref("auth",A::$AUTH);

	return $this->fetch($template?$template:$this->template);
  }
}