<?php
/** \file system/framework/formats.php
 * Извлечение данных из файлов определенного формата.
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
 * Класс реализует в себе функции массива записей.
 * Под записью понимается ассоциированный массив.
 */

class A_Array implements Iterator, ArrayAccess, Countable
{
/**
 * Массив данных.
 */

  protected $data=array();

/**
 * Текущий индекс записи.
 */

  protected $index=0;

/**
 * Конструктор.
 */

  public function __construct(&$data)
  {
    $this->data=$data;
  }

/**
 * Текущий индекс записи в начало массива.
 */

  public function rewind()
  {
    $this->index = 0;
    return true;
  }

/**
 * Возвращает текущую запись.
 *
 * @return array Текущая запись.
 */

  public function current()
  {
    return $this->getEl();
  }

/**
 * Возвращает текущий индекс.
 *
 * @return integer Текущий индекс.
 */

  public function key()
  {
    return $this->index;
  }

/**
 * Возвращает текущую запись и смещает индекс на следующую.
 *
 * @return array Текущая запись.
 */

  public function next()
  {
    $r=$this->current();
    $this->index++;
    return $r;
  }

/**
 * Проверка размерности массива.
 *
 * @return boolean Текущий индекс не достиг конца массива записей.
 */

  public function valid()
  {
    return $this->index<count($this->data);
  }

/**
 * Возвращает количество записей.
 *
 * @return integer Количество записей.
 */

  public function count()
  {
    return count($this->data);
  }

/**
 * Возвращает значение элемента.
 *
 * @param integer $i Номер записи.
 * @param integer $j=false Номер элемента в записи, если не указано, то возвращается вся запись.
 * @return mixed Значение элемента.
 */

  public function get($i,$j=false)
  {
    if(!isset($this->data[$i]))
	return false;
    if($j===false)
	return $this->data[$i];
    if(!is_array($this->data[$i]))
	return false;
    if(!isset($this->data[$i][$j]))
	return false;
    return $this->data[$i][$j];
  }

/**
 * Возвращает текущую запись.
 *
 * @return array Текущая запись.
 */

  protected function getEl()
  {
	return $this->data[$this->index];
  }

/**
 * Возвращает запись по индексу.
 *
 * @param integer $offset Индекс записи.
 * @return array Запись.
 */

  public function offsetGet($offset)
  {
    return $this->get($offset);
  }

/**
 * Добавляет или замещает существующую запись.
 *
 * @param integer $offset Индекс записи.
 * @param array $value Запись.
 */

  public function offsetSet($offset,$value)
  {
    $this->data[$offset]=$value;
  }

/**
 * Проверка индекса записи.
 *
 * @param integer $offset Индекс записи.
 * @return boolean Запись существует.
 */

  public function offsetExists($offset)
  {
    return isset($this->data[$offset]);
  }

/**
 * Удаление записи по индексу.
 *
 * @param integer $offset Индекс записи.
 */

  public function offsetUnset($offset)
  {
    unset($this->data[$offset]);
  }
}

/**
 * Класс реализует в себе функции массива записей.
 * Массив формируется из данных указанного файла Excel.
 */

class A_ExcelReader extends A_Array
{
/**
 * Конструктор.
 *
 * @param string $file Путь к файлу.
 */

  public function __construct($file)
  {
    if(is_file($file))
    {
      require_once "Structures/DataGrid.php";
      require_once "Structures/DataGrid/DataSource/Excel.php";

	  $datasource = new Structures_DataGrid_DataSource_Excel();
      $datasource->bind($file);
      $datagrid = new Structures_DataGrid();
      $datagrid->bindDataSource($datasource);

      parent::__construct($datagrid->recordSet);
    }
  }

/**
 * Возвращает текущую запись.
 *
 * @return array Текущая запись.
 */

    protected function getEl()
    {
	  $row=array();
	  foreach($this->data[$this->index] as $i=>$value)
	  $row[$i-1]=$value;
	  return $row;
    }
}

/**
 * Класс реализует в себе функции массива записей.
 * Массив формируется из данных указанного файла CSV.
 */

class A_CSVReader extends A_Array
{
/**
 * Конструктор.
 *
 * @param string $file Путь к файлу.
 */

  public function __construct($file)
  {
    if(is_file($file))
    {
      require_once "Structures/DataGrid.php";
	  require_once "Structures/DataGrid/DataSource/CSV.php";

	  $datasource = new Structures_DataGrid_DataSource_CSV();
	  $datasource->bind($sourcefile,array('delimiter'=>';','enclosure' =>'"'));
      $datagrid = new Structures_DataGrid();
      $datagrid->bindDataSource($datasource);

      parent::__construct($datagrid->recordSet);
    }
  }
}