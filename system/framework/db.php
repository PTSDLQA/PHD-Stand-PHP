<?php
/** \file system/framework/db.php
 * Работа с БД MySQL.
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
 * Исключение при работе с БД.
 */

class A_DBException extends LogicException {}

/**
 * Исключение при подключении к БД.
 */

class A_DBExceptionConnect extends A_DBException {}

/**
 * Исключение при запросе к БД.
 */

class A_DBExceptionQuery extends A_DBException
{
  private $query, $errno, $error;

  function __construct($query,$errno,$error)
  {
     $this->query = $query;
     $this->errno = $errno;
     $this->error = $error;
  }

  function query() { return $this->query; }
  function errno() { return $this->errno; }
  function error() { return $this->error; }

  public function __toString()
  {
    return 'DB Error: '.$this->errno.' "'.$this->error().'" SQL="'.$this->query.'"';
  }
}

/**
 * Исключение при формировании запроса.
 */

class A_DBExceptionData extends A_DBException
{
  protected $message = 'DB Data Exception';
}

/**
 * Исключение при формировании запроса (Лишние параметры).
 */

class A_DBExceptionDataMuch extends A_DBExceptionData
{
  protected $message = 'It is too much data';
}

/**
 * Исключение при формировании запроса (Не хватает параметров).
 */

class A_DBExceptionDataNotEnough extends A_DBExceptionData
{
  protected $message = 'It is not enough data';
}

/**
 * Класс реализует основные функции для работы с БД MySQL.
 * Поддерживаются параметризированные запросы.
 */

class A_DB
{
  private static $instance;

  private $mqph;

  private $connection;

/**
 * Стек всех результатов запросов.
 */

  public $results=array();

/**
 * Объект кэширования БД.
 */

  public $cache;

/**
 * Флаг состояния кэширования (true/false).
 */

  public $caching;

/**
 * Количество запросов к БД.
 */

  public $qcounter=0;

  function __construct()
  {
    $this->connect();
  }

/**
 * Возвращает объект для работы с БД.
 * @return object Объект для работы с БД.
 */

  function getInstance()
  {
    if(!self::$instance)
    self::$instance = new A_DB;
    return self::$instance;
  }

/**
 * Подключение к БД.
 *
 * @param string Идентификатор сайта.
 */

  function connect()
  {
	if(!$this->connection)
	{ $base=$GLOBALS['A_DBCONFIG'];
	  $this->connection = new mysqli($base['host'],$base['user'],$base['password'],$base['name']);
	  if(mysqli_connect_errno())
	  throw new A_DBExceptionConnect(mysqli_connect_error(),mysqli_connect_errno());
	  $this->connection->query("SET NAMES 'utf8'");
	}
  }

  function real_escape_string($string)
  {
	if(!$this->connection)
	return mysql_escape_string($string);
	else
	return $this->connection->real_escape_string($string);
  }

/**
 * Выполнение SQL запроса.
 *
 * @param string $sql Строка запроса.
 * @param array $params=array() Параметры запроса.
 * @return true/false или MySQLi_RESULT
 */

  function execute($sql,$params=array())
  {
    $sql=$params?$this->makeSQL($sql,$params):$sql;
	$result=$this->connection->query($sql,MYSQLI_STORE_RESULT);
    if($this->connection->errno)
	throw new A_DBExceptionQuery($sql,$this->connection->errno,$this->connection->error);
	$this->qcounter++;
	return $result;
  }

/**
 * Формирование SQL запроса.
 *
 * @param string $pattern Шаблон запроса.
 * @param array $params Параметры запроса.
 * @return string строка SQL запроса.
 */

  function makeSQL($pattern,$params)
  {
	$this->mqph = !is_array($params)?array($params):$params;
    $sql = @preg_replace_callback('/\?([int?casv]?[ina]?);?/u',array($this,'_makeSQL'),$pattern);
    if(count($this->mqph)>0)
	throw new A_DBExceptionDataMuch();
    return $sql;
  }

  private function _makeSQL($ph)
  {
    if($ph[1] == '?')
	return '?';

    if(count($this->mqph)==0)
	throw new A_DBExceptionDataNotEnough();

    $el=array_shift($this->mqph);
    switch($ph[1])
	{ case ('i'): return (integer)$el;
      case ('t'): return '`'.$el.'`';
      case ('c'): return '`'.$el.'`';
      case ('n'): return is_null($el)?'NULL':('"'.$this->real_escape_string($el).'"');
      case ('ni'):
      case ('in'): return is_null($el)?'NULL':(integer)$el;
      case ('a'):
        foreach($el as &$e)
		$e='"'.$this->real_escape_string($e).'"';
        return implode(',',$el);
      case ('ai'):
      case ('ia'):
        foreach($el as &$e)
		$e=(integer)$e;
        return implode(',',$el);
      case ('s'):
        $set=array();
        foreach($el as $k=>$v)
		{ if($v !== null)
		  $set[]='`'.$k.'`="'.$this->real_escape_string($v).'"';
          else
		  $set[]='`'.$k.'`=NULL';
        }
        return implode(',',$set);
      case ('v'):
       	$valueses=array();
        foreach($el as $v)
		{ $values=array();
          foreach($v as $d)
		  { if($d!==null)
		    $values[]='"'.$this->real_escape_string($d).'"';
            else
			$values[]='NULL';
          }
          $valueses[]='('.implode(',',$values).')';
        }
        return implode(',',$valueses);
    }
    return '"'.$this->real_escape_string($el).'"';
  }

/**
 * Проверка на существование записи.
 *
 * @param string $sql Строка SQL запроса
 * @param array $params=array() Параметры запроса.
 * @return boolean true если хоть одна запись существует.
 */

  function existsRow($sql,$params=array())
  {
	return $this->execute($sql,$params)->num_rows>0;
  }

/**
 * Возвращает первую запись по результату SQL запроса.
 *
 * @param string $sql Строка SQL запроса
 * @param array $params=array() Параметры запроса.
 * @return array() Ассоцированный массив с данными записи.
 */

  function getRow($sql,$params=array())
  {
	return $this->execute($sql,$params)->fetch_assoc();
  }

/**
 * Извлекает запись из таблицы БД по значению id.
 *
 * @param integer $id Уникальный идентификатор записи в таблице.
 * @param string $table Таблица БД.
 * @return array() Ассоцированный массив с данными записи.
 */

  function getRowById($id,$table)
  {
	$sql="SELECT * FROM `$table` WHERE id=".(integer)$id;
	return $this->execute($sql)->fetch_assoc();
  }

/**
 * Возвращает значение первого поля в первой записи по результату SQL запроса.
 *
 * @param string $sql Строка SQL запроса
 * @param array $params=array() Параметры запроса.
 * @return mixed Значение.
 */

  function getOne($sql,$params=array())
  {
    $result=$this->execute($sql,$params)->fetch_row();
    return $result?array_shift($result):false;
  }


/**
 * Возвращает асоциированный массив, ключом в котором является значение первого поля в записях по результату SQL запроса.
 * Если в записях только два поля, то второе является значением элемента массива, если больше, то значением элемента становится массив из оставшихся полей.
 *
 * @param string $sql Строка SQL запроса
 * @param array $params=array() Параметры запроса.
 * @return array Ассоциированный массив.
 */

  function getAssoc($sql,$params=array())
  {
	$data=array();
	$result=$this->execute($sql,$params);
	if($result->field_count==2)
	{ while($row=$result->fetch_row())
	  $data[$row[0]]=$row[1];
	}
	elseif($result->field_count>2)
	{ while($row=$result->fetch_assoc())
	  $data[array_shift($row)]=$row;
	}
	$result->free();
    return $data;
  }

/**
 * Возвращает массив, элементами в которого являются значения первого поля в записях по результату SQL запроса.
 *
 * @param string $sql Строка SQL запроса
 * @param array $params=array() Параметры запроса.
 * @return array Ассоциированный массив.
 */

  function getCol($sql,$params=array())
  {
	$data=array();
	$result=$this->execute($sql,$params);
	if($result->field_count>0)
	while($row=$result->fetch_row())
	$data[]=$row[0];
	$result->free();
    return $data;
  }

/**
 * Возвращает массив всех записей по результату SQL запроса.
 *
 * @param string $sql Строка SQL запроса
 * @param array $params=array() Параметры запроса.
 * @return array Массив записей, каждая из которых - асоциированный массив значений полей.
 */

  function getAll($sql,$params=array())
  {
    $data=array();
	$result=$this->execute($sql,$params);
	if($result->field_count>0)
	while($row=$result->fetch_assoc())
	$data[]=$row;
	$result->free();
    return $data;
  }

/**
 * Возвращает количество найденных записей по результату SQL запроса.
 *
 * @param string $table Таблица БД.
 * @param string $where Условие выборки.
 * @param array $params=array() Параметры запроса.
 * @return integer Количество найденных записей.
 */

  function getCount($table,$where='',$params=array())
  {
    $sql="SELECT COUNT(*) FROM `$table`".(!empty($where)?" WHERE $where":"");
    return array_shift($this->execute($sql,!is_array($params)?array($params):$params)->fetch_row());
  }

/**
 * Выполняет SQL запрос и сохраняет результат в стеке для последующего извлечения с помощью fetchRow().
 *
 * @param string $sql Строка SQL запроса
 * @param array $params=array() Параметры запроса.
 * @return integer Количество записей в результате.
 */

  function query($sql,$params=array())
  {
    array_push($this->results,$result=$this->execute($sql,$params));
	return $result->num_rows;
  }

/**
 * Выполняет SQL запрос с заданным лимитом выборки и сохраняет результат в стеке для последующего извлечения с помощью fetchRow().
 *
 * @param string $sql Строка SQL запроса
 * @param integer $b Номер первой записи в выборке.
 * @param integer $c Количество записей.
 * @param array $params=array() Параметры запроса.
 * @return integer Общее количество найденных записей.
 */

  function queryLimit($sql,$b,$c,$params=array())
  {
    $b=(integer)$b;
    $c=(integer)$c;
	$sql=preg_replace("/^[\s]*SELECT/i","SELECT SQL_CALC_FOUND_ROWS",$sql)." LIMIT $b,$c";
	array_push($this->results,$this->execute($sql,$params));
	$result=$this->connection->query("SELECT FOUND_ROWS() as __total_rows")->fetch_row();
	return $result?array_shift($result):0;
  }

/**
 * Извлечение очередной записи из последнего результата в стеке.
 *
 * @return array Ассоциированный массив или false.
 */

  function fetchRow()
  {
	if($result=end($this->results))
	return $result->fetch_assoc();
	else
	return false;
  }

/**
 * Количество записей у последнего результата в стеке.
 *
 * @return array Ассоциированный массив.
 */

  function numRows()
  {
	if($result=end($this->results))
	return $result->num_rows;
	else
	return 0;
  }

/**
 * Удаление последнего результата из стека.
 */

  function free()
  {
	array_pop($this->results);
  }

/**
 * Добавление новой записи в таблицу БД.
 *
 * @param string $table Таблица БД.
 * @param array $data Асоциированный массив со значениями полей.
 * @return integer Уникальный идентификатор новой записи или false в случае неудачи.
 */

  function Insert($table,$data)
  {
	if(empty($data)) return false;
	$fields=array_keys($data);
	$values=array_values($data);
	$sql="INSERT INTO `$table` (".implode(",",array_fill(0,count($data),'?c')).") VALUES (".implode(",",array_fill(0,count($data),'?')).")";
	$this->execute($sql,array_merge($fields,$values));
	if($this->connection->affected_rows>0)
	return $this->connection->insert_id;
	else
	return false;
  }

/**
 * Обновление записей в таблице БД.
 *
 * @param string $table Таблица БД.
 * @param array $data Асоциированный массив со значениями полей.
 * @param array $where Условие в SQL запросе.
 * @param array $params=array() Параметры запроса.
 * @return integer Количество измененных записей.
 */

  function Update($table,$data,$where="",$params=array())
  {
    if(empty($data)) return false;
    $rparams=array();
	foreach($data as $field=>$value)
	{ $rparams[]=$field;
	  $rparams[]=$value;
	}
	$sql="UPDATE `$table` SET ".implode(",",array_fill(0,count($data),'?c=?'));
	if(!empty($where))
	$sql.=" WHERE $where";
	$this->execute($sql,array_merge($rparams,!is_array($params)?array($params):$params));
	return $this->connection->affected_rows;
  }

/**
 * Обновление записей в таблице БД, если обновлять нечего - добавляется новая запись.
 *
 * @param string $table Таблица БД.
 * @param array $data Асоциированный массив со значениями полей.
 * @param array $where Условие в SQL запросе.
 * @param array $params=array() Параметры запроса.
 * @return integer Уникальный идентификатор новой записи или 0 в случае обновления.
 */

  function Replace($table,$data,$where="",$params=array())
  {
    if(empty($data)) return false;
	$this->Update($table,$data,$where,$params);
	if($this->connection->affected_rows==0 && !isset($data['id']))
	return $this->Insert($table,$data);
	else
	return 0;
  }

/**
 * Удаление записей из таблицы БД.
 *
 * @param string $table Таблица БД.
 * @param array $where Условие в SQL запросе.
 * @param array $params=array() Параметры запроса.
 * @return integer Количество удаленных записей.
 */

  function Delete($table,$where="",$params=array())
  {
	$sql="DELETE FROM `$table`";
	if(!empty($where))
	$sql.=" WHERE $where";
    $this->execute($sql,$params);
	return $this->connection->affected_rows;
  }

/**
 * Возвращает количество записей, к которым был применен последний запрос.
 *
 * @return integer Количество записей, к которым был применен последний запрос.
 */

  function affectedRows()
  {
    return $this->connection->affected_rows;
  }

/**
 * Возвращает список таблиц БД принадлежащих сайту.
 *
 * @return array Список таблиц БД принадлежащих сайту.
 */

  function getTables()
  {
	$data=array();
	$result=$this->connection->query($sql="SHOW TABLES");
	if($this->connection->errno)
	throw new A_DBExceptionQuery($sql,$this->connection->errno,$this->connection->error);
	if($result->field_count>0)
	while($row=$result->fetch_row())
	$data[]=$row[0];
	$result->free();
    return $data;
  }

/**
 * Возвращает список полей в таблице БД.
 *
 * @param string $table Таблица БД.
 * @return array Список список полей в заданной таблице.
 */

  function getFields($table)
  {
	$result=$this->connection->query($sql="SELECT * FROM `$table` WHERE id=0");
	if($this->connection->errno)
	throw new A_DBExceptionQuery($sql,$this->connection->errno,$this->connection->error);
    $finfo=$result->fetch_fields();
	foreach($finfo as $i=>$info)
	$fields[$i]=$info->name;
	$result->free();
	return $fields;
  }

/**
 * Возвращает список индексов в таблице БД.
 *
 * @param string $table Таблица БД.
 * @return array Список индексов в заданной таблице.
 */

  function getIndex($table)
  {
    $index=array();
    $result=$this->connection->query($sql="SHOW KEYS FROM `$table`");
    if($this->connection->errno)
	throw new A_DBExceptionQuery($sql,$this->connection->errno,$this->connection->error);
    while($row=$result->fetch_assoc())
	$index[]=$row['Key_name'];
	$result->free();
	return $index;
  }

/**
 * Выполняет последовательность SQL запросов.
 *
 * @param string $sql Текст SQL скрипта.
 */

  function execSQL($sql)
  {
	$sql=mb_convert_encoding($sql,"Windows-1251","UTF-8");
	$sql=rtrim($sql,"\n\r");
    $sql_len=strlen($sql);
    $char='';
    $string_start='';
    $in_string=false;
    $nothing=true;
    $time0=time();
    for($i=0;$i<$sql_len;++$i)
	{ $char = $sql[$i];
      if($in_string)
	  { while(1)
		{ $i = strpos($sql, $string_start, $i);
          if(!$i)
		  { $this->connection->query($_sql=mb_convert_encoding($sql,"UTF-8","Windows-1251"));
		    if($this->connection->errno)
	        throw new A_DBExceptionQuery($_sql,$this->connection->errno,$this->connection->error);
		  }
          elseif($string_start == '`' || $sql[$i-1] != '\\')
		  { $string_start = '';
            $in_string = false;
            break;
          }
          else
		  { $j = 2;
            $escaped_backslash = false;
            while($i-$j > 0 && $sql[$i-$j] == '\\')
			{ $escaped_backslash = !$escaped_backslash;
              $j++;
            }
            if($escaped_backslash)
			{ $string_start  = '';
              $in_string = false;
              break;
            }
            else
			$i++;
          }
        }
      }
      elseif(($char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') || $char == '#' || ($char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*'))
	  { $i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
        if($i === false) break;
        if($char == '/') $i++;
      }
	  elseif($char == ';')
	  { $this->connection->query($_sql=mb_convert_encoding(substr($sql, 0, $i),"UTF-8","Windows-1251"));
	    if($this->connection->errno)
	    throw new A_DBExceptionQuery($_sql,$this->connection->errno,$this->connection->error);
        $nothing = true;
        $sql = ltrim(substr($sql, min($i + 1, $sql_len)));
        $sql_len = strlen($sql);
        if ($sql_len)
		$i = -1;
        else
		return;
      }
      elseif(($char == '"') || ($char == '\'') || ($char == '`'))
	  { $in_string = true;
        $nothing = false;
        $string_start = $char;
      }
      elseif ($nothing)
	  $nothing = false;
    }
    if(!empty($sql) && preg_match('/@[^[:space:]]+@/',$sql))
    { $this->connection->query($_sql=mb_convert_encoding($sql,"UTF-8","Windows-1251"));
      if($this->connection->errno)
	  throw new A_DBExceptionQuery($_sql,$this->connection->errno,$this->connection->error);
    }
  }

/**
 * Выполняет последовательность SQL запросов из файла.
 *
 * @param string $file Файл содержащий SQL скрипт.
 */

  function execSQLFile($file)
  {
    if($sql=@file_get_contents($file))
    $this->execSQL($sql);
  }
}