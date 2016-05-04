<?php
/** \file system/framework/dataset.php
 * Таблица БД.
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
 * Универсальный класс для работы с записями таблицы БД через массив $_REQUEST.
 * Может автоматически использовать данные о дополнительных полях.
 */

class A_DataSet
{
/**
 * Таблица БД.
 */

  public $table;

/**
 * Текущие данные в записи (Если задан $_REQUEST[id]).
 */

  public $data;

/**
 * Массив полей таблицы для добавления или обновления.
 */

  public $fields=array();

/**
 * Флаг автоматического использования дополнительных полей.
 */

  public $usefeditor;

/**
 * Конструктор.
 *
 * @param string $table Таблица БД.
 * @param boolean $usefeditor=false Автоматическое использование дополнительных полей.
 */

  function __construct($table,$usefeditor=false)
  {
	$this->table=$table;
	$this->usefeditor=$usefeditor && (!defined('MODE') || MODE=='sections' || MODE=='structures');

	if(!empty($_REQUEST['id']))
	$this->data=A::$DB->getRowById($_REQUEST['id'],$this->table);
  }

/**
 * Служебный метод подготовки дополнительных полей при добавлении/обновлении записи таблицы БД.
 */

  protected function addedit_prepare()
  {
    if(A_MODE==0)
    A::$DB->query("SELECT * FROM ".DOMAIN."_fields WHERE item='".ITEM."' AND nofront='N' ORDER BY sort");
	else
	A::$DB->query("SELECT * FROM ".DOMAIN."_fields WHERE item='".ITEM."' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { switch($row['type'])
      { case "int":
          $_REQUEST[$row['field']]=isset($_REQUEST[$row['field']])?(integer)$_REQUEST[$row['field']]:0;
          $this->fields[]=$row['field'];
		  break;
        case "float":
	      $_REQUEST[$row['field']]=isset($_REQUEST[$row['field']])?(float)$_REQUEST[$row['field']]:0;
	      $this->fields[]=$row['field'];
	      break;
        case "bool":
	      $_REQUEST[$row['field']]=isset($_REQUEST[$row['field']])?"Y":"N";
	      $this->fields[]=$row['field'];
	      break;
		case "date":
		  $_REQUEST[$row['field']]=isset($_REQUEST[$row['field']])?(integer)$_REQUEST[$row['field']]:mktime(0,0,0,(integer)$_REQUEST[$row['field'].'Month'],(integer)$_REQUEST[$row['field'].'Day'],(integer)$_REQUEST[$row['field'].'Year']);
		  $this->fields[]=$row['field'];
		  break;
		case "string":
	    case "text":
	    case "format":
	      $_REQUEST[$row['field']]=isset($_REQUEST[$row['field']])?trim($_REQUEST[$row['field']]):"";
	      $this->fields[]=$row['field'];
	      break;
	    case "image":
		  if(empty($this->data))
	      $_REQUEST[$row['field']]=UploadImage($row['field'],!empty($_REQUEST['name'])?$_REQUEST['name']:"");
	      else
	      { $_REQUEST[$row['field']]=UploadImage($row['field'],!empty($_REQUEST['name'])?$_REQUEST['name']:"",$this->data[$row['field']]);
			if(isset($_REQUEST[$row['field'].'_del']))
	        { DelRegImage($this->data[$row['field']]);
	          $_REQUEST[$row['field']]=0;
	        }
	      }
	      $this->fields[]=$row['field'];
	      break;
	    case "file":
	      if(empty($this->data))
	      $_REQUEST[$row['field']]=UploadFile($row['field'],!empty($_REQUEST['name'])?$_REQUEST['name']:"");
	      else
	      { $_REQUEST[$row['field']]=UploadFile($row['field'],!empty($_REQUEST['name'])?$_REQUEST['name']:"",$this->data[$row['field']]);
	        if(isset($_REQUEST[$row['field'].'_del']))
	        { DelRegFile($this->data[$row['field']]);
	          $_REQUEST[$row['field']]=0;
	        }
	      }
	      $this->fields[]=$row['field'];
	      break;
      }
    }
    A::$DB->free();
  }

/**
 * Служебный метод подготовки дополнительных полей при удалении записи таблицы БД.
 */

  protected function delete_prepare()
  {
    if(!empty($this->data) && !empty(A::$REGFILES))
    { A::$DB->query("SELECT * FROM ".DOMAIN."_fields WHERE item='".ITEM."' AND (type='image' OR type='file') ORDER BY sort");
      while($row=A::$DB->fetchRow())
      switch($row['type'])
      { case "image":
          DelRegImage($this->data[$row['field']]);
	      break;
        case "file":
          DelRegFile($this->data[$row['field']]);
	      break;
      }
      A::$DB->free();
    }
  }

/**
 * Добавление новой записи в таблицу. Данные берутся из массива $_REQUEST.
 *
 * @return integer Числовой идентификатор новой записи.
 */

  function Insert()
  {
    if(empty($this->fields)) return false;

	if($this->usefeditor)
	$this->addedit_prepare();

	$data=array();
    foreach($this->fields as $field)
	if(isset($_REQUEST[$field]))
	$data[$field]=$_REQUEST[$field];

    A::$OBSERVER->Event('DataSet_BeforeInsert',$this->table,$data);
    $data['id']=A::$DB->Insert($this->table,$data);
	A::$OBSERVER->Event('DataSet_AfterInsert',$this->table,$data);

	return $data['id'];
  }

/**
 * Обновление записи в таблице. Данные берутся из массива $_REQUEST.
 *
 * @param integer $id=0 Числовой идентификатор записи, если не указано то должен находится в $_REQUEST[id].
 * @return array Массив с данными до обновления записи.
 */

  function Update($id=0)
  {
	if($id>0)
	{ $_REQUEST['id']=$id;
	  $this->data=A::$DB->getRowById($_REQUEST['id'],$this->table);
	}

	if(empty($_REQUEST['id'])) return false;
	if(empty($this->fields)) return false;

	if($this->usefeditor)
	$this->addedit_prepare();

	$data=array();
    foreach($this->fields as $field)
	if(isset($_REQUEST[$field]))
	$data[$field]=$_REQUEST[$field];

    A::$OBSERVER->Event('DataSet_BeforeUpdate',$this->table,$this->data);
    if(A::$DB->Update($this->table,$data,"id=".(integer)$_REQUEST['id']))
    { $data['id']=$_REQUEST['id'];
      A::$OBSERVER->Event('DataSet_AfterUpdate',$this->table,$data);
    }

	return $this->data;
  }

/**
 * Удаление записи из таблицы. Данные берутся из массива $_REQUEST.
 *
 * @param integer $id=0 Числовой идентификатор записи, если не указано то должен находится в $_REQUEST[id].
 * @return array Массив с данными удаленной записи.
 */

  function Delete($id=0)
  {
	if($id>0)
	{ $_REQUEST['id']=$id;
	  $this->data=A::$DB->getRowById($_REQUEST['id'],$this->table);
	}

	if(empty($_REQUEST['id'])) return false;

	if($this->usefeditor)
	$this->delete_prepare();

	if(!$this->data) return false;

    A::$OBSERVER->Event('DataSet_BeforeDelete',$this->table,$this->data);
	A::$DB->Delete($this->table,"id=".(integer)$_REQUEST['id']);
    A::$OBSERVER->Event('DataSet_AfterDelete',$this->table);

	return $this->data;
  }
}