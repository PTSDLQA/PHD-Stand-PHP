<?php
/** \file system/framework/functions.php
 * Глобальные функции.
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
 * Исключение при работе с файлами.
 */

class A_FileException extends LogicException
{
  protected $file;

  function errno() { return 'FILE'; }
  function error() { return 'FFFF'; }

  function __construct($file)
  {
     $this->file = $file;
  }
}

/**
 * Исключение при удалении файла.
 */

class A_FileExceptionDelete extends A_FileException
{
  function __toString()
  {
    return "Файл: $this->file не может быть удален/заменен";
  }
}

/**
 * Исключение при удалении каталога.
 */

class A_DirExceptionDelete extends A_FileException
{
  function __toString()
  {
    return "Каталог: $this->file не может быть удален";
  }
}

/**
 * Исключение при создании каталога.
 */

class A_DirExceptionCreate extends A_FileException
{
  function __toString()
  {
    return "Каталог: $this->file не может быть создан";
  }
}

/**
 * Создание каталога.
 *
 * @param string $dirName Имя каталога.
 * @return boolean Успешность операции.
 */

function mk_dir($dirName)
{
  if(!is_dir($dirName) && !@mkdir($dirName))
  { throw new A_DirExceptionCreate($dirName);
    return false;
  }
  return true;
}

/**
 * Копирование файла.
 *
 * @param string $in Копируемый файл.
 * @param string $to Новый файл.
 * @param boolean $replace Заменить если файл уже существует.
 * @param boolean $newonly Заменить только если файл более старый.
 * @return boolean Успешность операции.
 */

function copyfile($in,$to,$replace=false,$newonly=false)
{
  if(!is_file($in))
  return false;
  if(is_file($to))
  { if(!$replace)
    return false;
    if($newonly)
	{ if($newonly===true && filemtime($to)>filemtime($in))
	  return false;
	  elseif(filemtime($to)>$newonly)
	  return false;
	}
	delfile($to);
  }
  return copy($in,$to);
}

/**
 * Удаление файла.
 *
 * @param string $file Удаляемый файл.
 * @return boolean Успешность операции.
 */

function delfile($file)
{
  if(is_file($file))
  { if(!@unlink($file))
    throw new A_FileExceptionDelete($file);
    return true;
  }
  else
  return false;
}

/**
 * Удаление всех файлов из каталога.
 *
 * @param string $dirname Каталог.
 * @return array Удаленные файлы.
 */

function getFilesByDir($dirName)
{
  $files=array();
  if(!empty($dirName) && is_dir($dirName))
  { $all=scandir($dirName);
    foreach($all as $file)
	if($file!='.' && $file!='..' && is_file("$dirName/$file"))
	$files[]=$file;
  }
  return $files;
}

/**
 * Удаление каталога со всеми файлами.
 *
 * @param string $dirName Удаляемый каталог.
 * @return boolean Успешность операции.
 */

function delDir($dirName)
{
  if(empty($dirName)) return true;
  if(is_dir($dirName))
  { $dir=dir($dirName);
    while($file=$dir->read())
	if($file!='.' && $file!='..')
	{ if(is_dir($dirName.'/'.$file))
	  delDir($dirName.'/'.$file);
      elseif(!@unlink($dirName.'/'.$file))
      throw new A_FileExceptionDelete($file);
    }
    $dir->close();
    if(!@rmdir($dirName))
	throw new A_DirExceptionDelete($dirName);
	return true;
  }
  else
  return false;
}

/**
 * Удаление всего содержимого из каталога.
 *
 * @param string $dirName Удаляемый каталог.
 * @return boolean Успешность операции.
 */

function clearDir($dirName)
{
  if(empty($dirName)) return true;
  if(is_dir($dirName))
  { $dir=dir($dirName);
    while($file=$dir->read())
	if($file!='.' && $file!='..')
	{ if(is_dir($dirName.'/'.$file))
	  delDir($dirName.'/'.$file);
      elseif(!@unlink($dirName.'/'.$file))
	  throw new A_FileExceptionDelete($dirName.'/'.$file);
    }
    $dir->close();
	return true;
  }
  else
  return false;
}

/**
 * Удаление всех файлов из каталога с сохранением структуры каталогов.
 *
 * @param string $dirName Каталог.
 * @param string $files=array() Список имен файлов, которые удалять. Если список пустой, то удаляются все.
 * @return boolean Успешность операции.
 */

function delFilesByDir($dirName,$files=array())
{
  if(empty($dirName)) return true;
  if(is_dir($dirName))
  { $dir=dir($dirName);
    while($file = $dir->read())
	if($file!='.' && $file!='..')
	{ if(is_dir($dirName.'/'.$file))
	  delFilesByDir($dirName.'/'.$file,$files);
      elseif((empty($files) || in_array($file,$files)) && !@unlink($dirName.'/'.$file))
	  throw new A_FileExceptionDelete($dirName.'/'.$file);
    }
    $dir->close();
	return true;
  }
  else
  return false;
}

/**
 * Копирование каталога со всем содержимым.
 *
 * @param string $dirIn Копируемый каталог.
 * @param string $dirTo Новый каталог.
 * @param boolean $replace Заменять существующие файлы.
 * @param boolean $newonly Заменять существующие файлы, только более новыми.
 * @return boolean Успешность операции.
 */

function copyDir($dirIn,$dirTo,$replace=false,$newonly=false)
{
  if(empty($dirIn)) return true;
  if(!is_dir($dirIn)) return false;
  mk_dir($dirTo);
  $dir=dir($dirIn);
  while($file=$dir->read())
  { if($file!='.' && $file!='..')
	{ if(is_dir($dirIn.'/'.$file))
	  { mk_dir($dirTo.'/'.$file);
		copyDir($dirIn.'/'.$file,$dirTo.'/'.$file,$replace,$newonly);
	  }
      copyfile($dirIn.'/'.$file,$dirTo.'/'.$file,$replace,$newonly);
    }
  }
  $dir->close();
  return true;
}

/**
 * Добавление данных в конец файла.
 *
 * @param string $file Файл.
 * @param string $data Дописываемые данные.
 * @return mixed Количество записанных байт или false.
 */

function appendToFile($file,$data)
{
  return file_put_contents($file,$data,FILE_APPEND);
}

/**
 * Замена строки в содержимом файла.
 *
 * @param string $in Заменяемая строка/фрагмент.
 * @param string $to Строка на которую заменяется искомая.
 * @param string $file Файл в котором происходит поиск и замена.
 */

function replaceFile($in,$to,$file)
{
  require_once("File/SearchReplace.php");
  $repl = new File_SearchReplace($in,$to,$file);
  $repl->doReplace();
}

/**
 * Замена строки в содержимом всех файлов каталога.
 *
 * @param string $in Заменяемая строка/фрагмент.
 * @param string $to Строка на которую заменяется искомая.
 * @param string $dir Каталог в файлах которого происходит поиск и замена.
 */

function replaceDir($in,$to,$dir)
{
  require_once("File/SearchReplace.php");
  $repl = new File_SearchReplace($in,$to,'',$dir);
  $repl->doReplace();
}

/**
 * Сравнение двух переменных.
 *
 * @param mixed $a Первая переменная.
 * @param midex $b Вторая переменная.
 * @return integer Величина больше нуля, если первая переменная больше чем вторая.
 */

function varcmp($a,$b)
{
  return is_string($a)||is_string($b)?strcmp($a,$b):$a-$b;
}

/**
 * Сортировка массива записей по ключу в записях.
 *
 * @param array $array Сортируемый массив записей.
 * @param string $key Ключ в записях по которому сортировать, либо массив ключей.
 * @param integer $type=SORT_ASC Способ сортировки.
 * @param string $cmp_func='strcmp' Функция для сравнения значений.
 * @return array Отсортированный массив.
 */

function array_multisort_key($array,$key,$type=SORT_ASC,$cmp_func='varcmp')
{
  if(is_array($key))
  { $k=array_shift($key);
    usort($array,create_function('$a,$b','return '.$cmp_func.'($a["'.$k.'"],$b["'.$k.'"])*'.($type==SORT_ASC?1:-1).';'));
	if(!empty($key))
	{ $newarray=array();
	  $subarray=array();
	  $prev=current($array);
	  $prev=$prev[$k];
	  foreach($array as $row)
	  { if($row[$k]!=$prev)
	    { if(count($subarray)>1)
	      $subarray=array_multisort_key($subarray,$key,$type,$cmp_func);
		  foreach($subarray as $srow)
		  $newarray[]=$srow;
	      $subarray=array();
	    }
		$subarray[]=$row;
	    $prev=$row[$k];
	  }
	  if(count($subarray)>1)
      $subarray=array_multisort_key($subarray,$key,$type,$cmp_func);
	  foreach($subarray as $srow)
	  $newarray[]=$srow;
	  $array=$newarray;
	}
  }
  else
  usort($array,create_function('$a,$b','return '.$cmp_func.'($a["'.$key.'"],$b["'.$key.'"])*'.($type==SORT_ASC?1:-1).';'));
  return $array;
}

/**
 * Разбирает на составляющие полный строковой идентификатор раздела.
 *
 * @param string $section Строковой идентификатор раздела.
 * @return array Массив значений с ключами:
 * domain - Идентификатор сайта.
 * lang - Идентификатор языковой версии (если раздел общий, то текущей языковой версии).
 * name - Короткий строковой идентификатор раздела (указываемый при создании).
 */

function parseSection($section)
{
  $result=array("domain"=>"mysite","lang"=>"","name"=>"");
  if(preg_match("/^([a-zA-Z0-9]+)_([a-zA-Z]+)_([a-zA-Z0-9]+)$/i",$section,$matches))
  { $result['domain']=$matches[1];
    $result['lang']='ru';
    $result['name']=$matches[3];
  }
  return $result;
}

/**
 * Разбирает на составляющие полный строковой идентификатор дополнения.
 *
 * @param string $structure Строковой идентификатор дополнения.
 * @return array Массив значений с ключами:
 * domain - Идентификатор сайта.
 * name - Короткий строковой идентификатор дополнения (указываемый при создании).
 */

function parseStructure($structure)
{
  $result=array("domain"=>"mysite","name"=>"");
  if(preg_match("/^([a-zA-Z0-9]+)_structure_([a-zA-Z0-9]+)$/i",$structure,$matches))
  { $result['domain']=$matches[1];
    $result['name']=$matches[2];
  }
  return $result;
}

/**
 * Возвращает идентификатор сайта, которому принадлежит полный строковой идентификатор раздела/дополнения.
 *
 * @param string $item Строковой идентификатор раздела/дополнения.
 * @return string Идентификатор сайта.
 */

function getDomain($item)
{
  return 'mysite';
}

/**
 * Возвращает домен сайта по его идентификатору.
 *
 * @param string $name Идентификатор сайта.
 * @return string Домен.
 */

function getHostByDomain($name)
{
  return HOSTNAME;
}

/**
 * Возвращает идентификатор языковой версии, которому принадлежит полный строковой идентификатор раздела.
 *
 * @param string $section Строковой идентификатор раздела.
 * @return string Идентификатор языковой версии.
 */

function getLang($section)
{
  return "ru";
}

/**
 * Возвращает короткий идентификатор по полному строковому идентификатору раздела/дополнения.
 *
 * @param string $item Полный строковой идентификатор раздела/дополнения.
 * @return string Короткий строковой идентификатор.
 */

function getName($item)
{
  if(preg_match("/^([a-zA-Z0-9]+)_([a-zA-Z]+)_([a-zA-Z0-9]+)$/i",$item,$matches))
  return $matches[3];
  else
  return false;
}

/**
 * Возвращает полный строковой идентификатор первого существующего раздела на базе указанного модуля.
 *
 * @param string $module Идентификатор модуля.
 * @return string Полный строковой идентификатор раздела.
 */

function getSectionByModule($module)
{ static $cache=array();
  if(isset($cache[$module])) return $cache[$module];
  if($row=A::$DB->getRow("SELECT name,lang FROM mysite_sections WHERE module=? LIMIT 0,1",$module))
  return $cache[$module]="mysite_ru_".$row['name'];
  else
  return $cache[$module]=false;
}

/**
 * Возвращает массив полных строковых идентификаторов существующих разделов на базе указанного модуля.
 *
 * @param string $module Идентификатор модуля.
 * @return array Массив полных строковых идентификаторов разделов.
 */

function getSectionsByModule($module)
{ static $cache=array();
  if(isset($cache[$module])) return $cache[$module];
  $sections=array();
  A::$DB->query("SELECT name,lang FROM mysite_sections WHERE module=?",$module);
  while($row=A::$DB->fetchRow())
  $sections[]="mysite_ru_".$row['name'];
  A::$DB->free();
  return $cache[$module]=$sections;
}

/**
 * Возвращает полный строковой идентификатор раздела по его числовому идентификатору.
 *
 * @param integer $id Числовой идентификатор раздела.
 * @return string Полный строковой идентификатор раздела.
 */

function getSectionById($id)
{ static $cache=array();
  if(isset($cache[$id])) return $cache[$id];
  if($row=A::$DB->getRow("SELECT name,lang FROM mysite_sections WHERE id=".(integer)$id))
  return $cache[$id]="mysite_ru_".$row['name'];
  else
  return $cache[$id]=false;
}

/**
 * Возвращает числовой идентификатор раздела по его полному строковому идентификатору.
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @return integer Числовой идентификатор раздела.
 */

function getSectionId($section)
{ static $cache=array();
  if(isset($cache[$section])) return $cache[$section];
  if(preg_match("/^([a-zA-Z0-9]+)_([a-zA-Z]+)_([a-zA-Z0-9]+)$/i",$section,$matches))
  return $cache[$section]=(integer)A::$DB->getOne("SELECT id FROM {$matches[1]}_sections WHERE name='{$matches[3]}'");
  else
  return $cache[$section]=0;
}

/**
 * Возвращает идентификатор модуля, на базе которого создан раздел.
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @return string Идентификатор модуля.
 */

function getModuleBySection($section)
{ static $cache=array();
  if(isset($cache[$section])) return $cache[$section];
  if(preg_match("/^([a-zA-Z0-9]+)_([a-zA-Z]+)_([a-zA-Z0-9]+)$/i",$section,$matches))
  return $cache[$section]=A::$DB->getOne("SELECT module FROM {$matches[1]}_sections WHERE name='{$matches[3]}'");
  else
  return $cache[$section]=false;
}

/**
 * Возвращает полный строковой идентификатор первого существующего дополнения на базе указанного плагина.
 *
 * @param string $plugin Идентификатор плагина.
 * @return string Полный строковой идентификатор дополнения.
 */

function getStructureByPlugin($plugin)
{
  //-- Только в полной версии.
  return false;
}

/**
 * Возвращает массив полных строковых идентификаторов существующих дополнений на базе указанного плагина.
 *
 * @param string $plugin Идентификатор плагина.
 * @return string Массив полных строковых идентификаторов дополнений.
 */

function getStructuresByPlugin($plugin)
{
  //-- Только в полной версии.
  return array();
}

/**
 * Возвращает полный строковой идентификатор дополнения по его числовому идентификатору.
 *
 * @param integer $id Числовой идентификатор дополнения.
 * @return string Полный строковой идентификатор дополнения.
 */

function getStructureById($id)
{
  //-- Только в полной версии.
  return false;
}

/**
 * Возвращает числовой идентификатор дополнения по его полному строковому идентификатору.
 *
 * @param string $structure Полный строковой идентификатор дополнения.
 * @return integer Числовой идентификатор дополнения.
 */

function getStructureId($structure)
{
  //-- Только в полной версии.
  return 0;
}

/**
 * Возвращает идентификатор плагина, на базе которого создано дополнение.
 *
 * @param string $structure Полный строковой идентификатор дополнения.
 * @return string Идентификатор плагина.
 */

function getPluginByStructure($structure)
{
  //-- Только в полной версии.
  return false;
}

/**
 * Возвращает ссылку на главную страницу раздела.
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @return string Ссылка на главную страницу раздела.
 */

function getSectionLink($section)
{ static $cache=array();
  if(isset($cache[$section])) return $cache[$section];
  $sparse=parseSection($section);
  $link="/";
  if(A::$OPTIONS['mainsection']!=$sparse['name'])
  { if($urlname=A::$DB->getOne("SELECT urlname FROM mysite_sections WHERE id=".getSectionId($section)))
    $link.=$urlname."/";
  }
  return $cache[$section]=$link;
}

/**
 * Возвращает данные обо всех существующих списках (дополнения на базе плагинов: listdata, liststr, listnum и др.).
 *
 * @return array Ассоциированный массив: строковой идентификатор => название.
 */

function getLists()
{
  //-- Только в полной версии.
  return array();
}

/**
 * Загружает и возвращает указанный список.
 *
 * @param string $item Короткий или полный идентификатор списка (дополнения на базе плагинов: listdata, liststr, listnum и др.).
 * @return array Ассоциированный массив: числовой идентификатор => значение или массив.
 */

function loadList($item)
{
  //-- Только в полной версии.
  return false;
}

/**
 * Добавляет значение в указанный список.
 *
 * @param string $item Короткий или полный идентификатор списка (дополнения на базе плагинов: listdata, liststr, listnum и др.).
 * @param string $value Значение.
 * @return boolean Успешность операции.
 */

function addToList($item,$value)
{
  //-- Только в полной версии.
  return false;
}

/**
 * Возвращает значение опции.
 *
 * @param string $item Полный идентификатор раздела или дополнения, в котором находится опция.
 * @param string $var Идентификатор опции.
 * @return mixed Значение опции.
 */

function getOption($item,$var)
{ static $cache=array();
  if(isset($cache[$item][$var])) return $cache[$item][$var];
  return $cache[$item][$var]=A::$DB->getOne("SELECT value FROM mysite_options WHERE item=? AND var=?",array($item,$var));
}

/**
 * Возвращает массив значений всех опций раздела или дополнения.
 *
 * @param string $item Полный идентификатор раздела или дополнения, в котором находится опция.
 * @return array Асоциированный массив: идентификатор опции => значение.
 */

function getOptions($item)
{ static $cache=array();
  if(isset($cache[$item])) return $cache[$item];
  return $cache[$item]=A::$DB->getAssoc("SELECT var,value FROM mysite_options WHERE item=?",$item);
}

/**
 * Устанавливает значение опции.
 *
 * @param string $item Полный идентификатор раздела или дополнения, в котором находится опция.
 * @param string $var Идентификатор опции.
 * @param mixed $value Значение опции.
 */

function setOption($item,$var,$value)
{
  if(defined('ITEM') && $item==ITEM)
  A::$OPTIONS[$var]=$value;
  return A::$DB->Update("mysite_options",array('value'=>$value),"item=? AND var=?",array($item,$var));
}

/**
 * Возвращает альтернативное текстовое значение опции.
 * Кроме простого значения (до 255 символов), каждая опция может хранить и какой-то текст.
 *
 * @param string $item Полный идентификатор раздела или дополнения, в котором находится опция.
 * @param string $var Идентификатор опции.
 * @return mixed Альтернативное текстовое значение опции.
 */

function getTextOption($item,$var)
{
  return A::$DB->getOne("SELECT options FROM mysite_options WHERE item=? AND var=?",array($item,$var));
}

/**
 * Устанавливает альтернативное текстовое значение опции.
 * Кроме простого значения (до 255 символов), каждая опция может хранить и какой-то текст.
 *
 * @param string $item Полный идентификатор раздела или дополнения, в котором находится опция.
 * @param string $var Идентификатор опции.
 * @param string Альтернативное текстовое значение опции.
 */

function setTextOption($item,$var,$value)
{
  return A::$DB->Update("mysite_options",array('options'=>$value),"item=? AND var=?",array($item,$var));
}

/**
 * Возвращает путь к зарегистрированному файлу.
 *
 * @param string $id Числовой идентификатор зарегистрированного файла.
 * @return string Путь к файлу.
 */

function getregfilepath($id)
{
  return A::$DB->getOne("SELECT path FROM mysite_files WHERE id=".(integer)$id);
}

/**
 * Возвращает путь к зарегистрированному файлу.
 *
 * @param string $id Числовой идентификатор зарегистрированного файла.
 * @return string Путь к файлу.
 */

function getregimagepath($id)
{
  return A::$DB->getOne("SELECT path FROM mysite_images WHERE id=".(integer)$id);
}

/**
 * Возвращает полный путь элемента в дереве.
 *
 * @param string $table Таблица БД в которой хранится дерево элементов.
 * @param string $id Числовой идентификатор элемента.
 * @param string $sep='&nbsp;&raquo;&nbsp;' Разделитель.
 * @return string Полный путь элемента.
 */

function getTreePath($table,$id,$sep='&nbsp;&raquo;&nbsp;')
{ static $cache=array();
  if(isset($cache[$table][$id])) return $cache[$table][$id];
  if($row=A::$DB->getRow("SELECT idker,name FROM `$table` WHERE id=".(integer)$id))
  { if($row['idker']>0)
    return $cache[$table][$id]=getTreePath($table,$row['idker'],$sep).$sep.$row['name'];
    else
	return $cache[$table][$id]=$row['name'];
  }
  else
  return $cache[$table][$id]='';
}

/**
 * Возвращает список идентификаторов всех дочерних категорий дерева.
 *
 * @param string $table Таблица БД в которой хранится дерево элементов.
 * @param array &$items Начальный список идентификаторов.
 * @param integer $id Идентификатор элемента.
 */

  function getTreeSubItems($table,$id,&$items)
  {
    $idc=A::$DB->getCol("SELECT id FROM `$table` WHERE idker=".(integer)$id);
    foreach($idc as $_id)
    { $items[]=$_id;
	  getTreeSubItems($table,$_id,$items);
    }
    return $items;
  }

/**
 * Определяет возможность доступа.
 *
 * @param string $aname Операция для которой проверяется доступ.
 * @param string $section=SECTION Полный строкой идентификатор раздела, если не указано то текущий.
 * @param string $default=true Доступ по умолчанию (если не создано дополнение "Группы пользователей")
 * @return boolean Возможность доступа.
 */

function getAccess($aname,$section=SECTION,$default=true)
{
  //-- Только в полной версии.
  return $default;
}

/**
 * Удаляет теги и обрезает текст до указанной длины.
 *
 * @param string $string Исходный текст.
 * @param string $length=80 Длина до которой обрезать.
 * @param string $etc='...' Строка добавляется в конец обрезанного текста.
 * @param string $break_words=false Обрезать слова.
 * @return string Обрезанный текст.
 */

function truncate($string,$length=80,$etc='...',$break_words=false)
{
  $string=strip_tags(str_replace('&nbsp;',' ',$string));
  $string=trim(preg_replace('/[\r\n ]+/i',' ',htmlspecialchars_decode($string)));
  if(mb_strlen($string) > $length)
  { $length-=mb_strlen($etc);
    if(!$break_words)
    $string = preg_replace('/\s+?(\S+)?$/','',mb_substr($string,0,$length+1));
    return mb_substr($string,0,$length).$etc;
  }
  else
  return $string;
}

/**
 * Удаляет теги и убирает крайние пробелы.
 *
 * @param string $string Исходный текст.
 * @return string Обработанный текст.
 */

function strclear($string)
{
  return trim(strip_tags($string));
}

/**
 * Удаляет теги и обрезает каждое слово до указанной длины.
 *
 * @param string $string Исходный текст.
 * @param string $length=30 Длина до которой обрезать слова.
 * @param string $etc='...' Строка добавляется в конец обрезанного слова.
 * @return string Обработанный текст.
 */

function truncatewords($string,$length=30,$etc='...')
{
  $str=explode(" ",strclear($string));
  foreach($str as $i=>$word)
  if(mb_strlen($word)>$length)
  $str[$i]=mb_substr($word,0,$length).$etc;
  return implode(" ",$str);
}

/**
 * Генерирует список наиболее часто встречающихся слов в тексте.
 *
 * @param string $string Исходный текст.
 * @param integer $wlen=5 Минимальная длина слова.
 * @param integer $wcount=50 Количество отбираемых слов.
 * @return string Строка со словами.
 */

function getkeywords($string,$wlen=5,$wcount=25)
{
  $string=strclear($string);
  $string=htmlspecialchars_decode($string);
  $string=preg_replace("/[^a-zA-Zа-яА-Я0-9 ]/iu"," ",$string);
  $string=mb_substr($string,0,10000);
  $words=explode(' ',$string);
  $_words=array();
  foreach($words as $word)
  { $word=trim($word);
    if(mb_strlen($word)>=$wlen)
    $_words[$word]=isset($_words[$word])?$_words[$word]+1:1;
  }
  arsort($_words);
  $_words=array_slice($_words,0,$wcount);
  return implode(', ',array_keys($_words));
}

/**
 * Переводит строку в транслитерацию.
 *
 * @param string $string Исходная строка.
 * @return string Строка в транслитерации.
 */

function translit($string)
{ static $trans=array();
  if(!$trans)
  { $ch1 = "абвгдеёзийклмнопрстуфхцыэ";
    $ch2 = "abvgdeeziyklmnoprstufhcye";
    for($i=0;$i<mb_strlen($ch1);$i++)
    $trans[mb_substr($ch1,$i,1)]=mb_substr($ch2,$i,1);
    $trans["ж"] = "zh"; $trans["ч"] = "ch"; $trans["ш"] = "sh"; $trans["щ"] = "sch";
	$trans["ъ"] = ""; $trans["ь"] = ""; $trans["ю"] = "yu"; $trans["я"] = "ya";
  }
  return preg_replace("/[^a-zA-Z0-9-]/iu","_",str_replace(array_keys($trans),array_values($trans),mb_strtolower(strclear($string))));
}

/**
 * Переводит текст в BBCode в HTML форматирование.
 *
 * @param string $string Исходный текст.
 * @return string Полученный HTML.
 */

function parse_bbcode($string)
{
  require_once("system/libs/bbcode.php");
  $bb = new bbcode(mb_convert_encoding($string,"Windows-1251","UTF-8"));
  return mb_convert_encoding($bb->get_html(),"UTF-8","Windows-1251");
}

/**
 * Возвращает mime тип файла по его расширению.
 *
 * @param string $ext Расширение файла.
 * @return string Mime тип.
 */

function getMimeByExt($ext)
{
  require_once("system/libs/mimetypes.php");
  return isset($mimetypes[$ext])?$mimetypes[$ext]:"application/octet-stream";
}

/**
 * Возвращает mime тип по названию файла.
 *
 * @param string $ext Название файла.
 * @return string Mime тип.
 */

function getMimeByFile($filename)
{
  $path_parts=pathinfo($filename);
  return getMimeByExt(mb_strtolower($path_parts["extension"]));
}

/**
 * Возвращает строку с размером в b,Kb,Mb по размеру в байтах.
 *
 * @param string $size Размер в байтах.
 * @return string Строка с размером в b,Kb,Mb.
 */

function sizestring($size)
{
  return $size>1024*1024?round($size/(1024*1024),2)." Mb":($size>1024?round($size/1024,2)." Kb":$size." b");
}

function clearURLName($urlname,$sep="_")
{
  $urlname=mb_strtolower($urlname);
  $urlname=preg_replace("/([_]{2,}|[-]{2,})/i",$sep,$urlname);
  $urlname=preg_replace("/^[_-]+/i","",$urlname);
  $urlname=preg_replace("/[_-]+$/i","",$urlname);
  return $urlname;
}

/**
 * Возвращает уникальный строковой идентикатор URL для записи в таблице БД.
 * В таблице должно присутствовать поле urlname в котором хранятся такие идентификаторы для записей.
 *
 * @param string $string Название записи.
 * @param string $urlname="" Заданный идентфикатор URL.
 * @param string $table="" Таблица БД.
 * @param string $where="" Условие для SQL выборки (проверка уникальности).
 * @param integer $maxlen=100 Максимальная длина идентификатора.
 * @return string Уникальный идентификатор URL.
 */

function getURLName($string,$urlname="",$table="",$where="",$maxlen=100)
{
  $sep=!empty($GLOBALS['A_URL_SEPARATOR'])?$GLOBALS['A_URL_SEPARATOR']:"_";
  if(empty($table))
  return mb_substr(clearURLName(A::$OPTIONS['transurl']?translit($string):preg_replace("/[^a-zA-Zа-яА-Я0-9-]/iu",$sep,$string),$sep),0,$maxlen);
  if(!empty($where))
  $where.=" AND";
  $urlname=mb_substr(clearURLName(A::$OPTIONS['transurl']?translit($urlname):preg_replace("/[^a-zA-Zа-яА-Я0-9-]/iu",$sep,$urlname),$sep),0,$maxlen);
  if(!empty($urlname) && !A::$DB->existsRow("SELECT id FROM `$table` WHERE $where urlname=?",$urlname))
  return $urlname;
  $string=clearURLName(A::$OPTIONS['transurl']?translit($string):preg_replace("/[^a-zA-Zа-яА-Я0-9-]/iu",$sep,$string),$sep);
  for($wlen=10;$wlen>0;$wlen--)
  { if(mb_strlen($string)>$maxlen)
    { $str=explode($sep,$string);
      foreach($str as $i=>$word)
      if(mb_strlen($word)>$wlen)
      $str[$i]=mb_substr($word,0,$wlen);
      $urlname=mb_substr(implode($sep,$str),0,$maxlen);
    }
	else
	$urlname=$string;
    if(!A::$DB->existsRow("SELECT id FROM `$table` WHERE $where urlname=?",$urlname))
    return $urlname;
  }
  return mb_substr(md5($string.time()),0,$maxlen);
}

/**
 * Устарело, в рамках совместимости.
 *
 */

function getStringId($string,$latname="",$table="",$where="")
{
  $sep=!empty($GLOBALS['A_TRANSLIT_SEPARATOR'])?$GLOBALS['A_TRANSLIT_SEPARATOR']:"_";
  if(empty($table))
  return mb_substr(translit($string),0,30);
  if(!empty($where)) $where.=" AND";
  $latname=mb_substr(translit($latname),0,30);
  if(!empty($latname) && !A::$DB->existsRow("SELECT id FROM $table WHERE $where latname=?",$latname))
  return $latname;
  $string=translit($string);
  for($wlen=10;$wlen>0;$wlen--)
  { if(mb_strlen($string)>30)
    { $str=explode($sep,$string);
      foreach($str as $i=>$word)
      if(mb_strlen($word)>$wlen)
      $str[$i]=mb_substr($word,0,$wlen);
      $latname=mb_substr(implode($sep,$str),0,30);
    }
	else
	$latname=$string;
    if(!A::$DB->existsRow("SELECT id FROM $table WHERE $where latname=?",$latname))
    return $latname;
  }
  return mb_substr(md5($string.time()),0,30);
}

/**
 * Загружает XML данные в массив.
 *
 * @param string $source Текст XML или путь к файлу.
 * @param boolean $isfile Если в первом параметре указан файл, то должно принимать значение true.
 * @return array Массив с данными или false.
 */

function loadXML($source,$isfile=false)
{
  require_once 'XML/Unserializer.php';
  $unserializer = new XML_Unserializer();
  if($isfile)
  { if(!is_file($source))
    return false;
    $xml = @simplexml_load_file($source);
	if(!empty($xml))
    { $unserializer->unserialize($source,true);
      return $unserializer->getUnserializedData();
    }
  }
  else
  { $xml = @simplexml_load_string($source);
	if(!empty($xml))
    { $unserializer->unserialize($source);
      return $unserializer->getUnserializedData();
    }
  }
  return false;
}

/**
 * Формирует XML из массива с данными.
 *
 * @param array $data Массив с данными.
 * @param string $rootname Название корневого элемента.
 * @return string Сформированный XML.
 */

function getXML($data,$rootname="")
{
  require_once 'XML/Serializer.php';
  $options = array(
  XML_SERIALIZER_OPTION_XML_DECL_ENABLED=>true,
  XML_SERIALIZER_OPTION_XML_ENCODING=>"utf-8",
  XML_SERIALIZER_OPTION_INDENT      => "\t",
  XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
  XML_SERIALIZER_OPTION_ROOT_NAME   => $rootname,
  XML_SERIALIZER_OPTION_MODE => XML_SERIALIZER_MODE_SIMPLEXML);
  $serializer = new XML_Serializer($options);
  $serializer->serialize($data);
  return $serializer->getSerializedData();
}

/**
 * Формирует XML из массива с данными и сразу отдает клиенту.
 *
 * @param array $data Массив с данными.
 * @param string $rootname Название корневого элемента.
 */

function outXML($data,$rootname="")
{
  header("Content-type: text/xml; charset=utf-8");
  die(getXML($data,$rootname));
}

/**
 * Удаляет все закэшированные изображения для заданного.
 *
 * @param string $name Имя файла исходного избражения.
 */

function clearCacheImage($name)
{
  $path_parts=pathinfo($name);
  $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
  $name=basename($name,".$ext");
  $files=scandir('cache/images/mysite');
  foreach($files as $file)
  if(strpos($file,$name.'_')===0)
  @unlink('cache/images/mysite/'.$file);
}

/**
 * Регистрирует загруженное изображение.
 *
 * @param string $name Идентфикатор загружаемого файла (значение name в <input type="file" name=...>)
 * @param string $caption Описание изображения.
 * @param integer $id=0 Числовой идентификатор изображения которое будет заменено.
 * @param integer $iditem=0 Идентификатор элемента раздела которому принадлежит изображение.
 * @param integer $sort=0 Порядок среди других принадлежащих элементу.
 * @param string $opt='img' Префикс идентификаторов опций масштабирования при загрузке.
 * @return integer Числовой идентификатор с которым зарегистрировано изображение.
 */

function UploadImage($name,$caption,$id=0,$iditem=0,$sort=0,$opt="img")
{
  if($resize=(integer)getOption(A::$REGFILES,$opt.'_resize'))
  { $x=(integer)getOption(A::$REGFILES,$opt.'_x');
    $y=(integer)getOption(A::$REGFILES,$opt.'_y');
  }
  else
  $x=$y=0;
  return UploadRegImage($name,$caption,$id,$iditem,$sort,$resize,$x,$y);
}

/**
 * Регистрирует загруженное изображение.
 *
 * @param string $name Идентфикатор загружаемого файла (значение name в <input type="file" name=...>)
 * @param string $caption Описание изображения.
 * @param integer $id=0 Числовой идентификатор изображения которое будет заменено.
 * @param integer $iditem=0 Идентификатор элемента раздела которому принадлежит изображение.
 * @param integer $sort=0 Порядок среди других принадлежащих элементу.
 * @param boolean $resize Масштабирование.
 * @param integer $x Масштабирование по ширине.
 * @param integer $y Масштабирование по высоте.
 * @return integer Числовой идентификатор с которым зарегистрировано изображение.
 */

function UploadRegImage($name,$caption,$id=0,$iditem=0,$sort=0,$resize=false,$x=0,$y=0)
{
  $image_ext=array("gif","jpg","jpeg","png");
  if(isset($_FILES[$name]['tmp_name']) && file_exists($_FILES[$name]['tmp_name']))
  { $ext=$basename="";
	if(!escapeFileName($_FILES[$name]['name'],$ext,$basename))
	return $id;
	$basename=translit($basename);
	if(!is_dir("files/mysite/reg_images"))
	mk_dir("files/mysite/reg_images");
	$data=array();
	if(in_array($ext,$image_ext))
	{ if($id>0)
	  delfile(A::$DB->getOne("SELECT path FROM mysite_images WHERE id=$id"));
	  $data['path']="files/mysite/reg_images/{$basename}.{$ext}";
	  if(file_exists($data['path']))
	  { $i=1;
	    do
		{ $data['path']="files/mysite/reg_images/{$basename}_".sprintf("%04d",$i).".{$ext}";
		  $i++;
		} while(file_exists($data['path']));
	  }
	  $data['name']=$basename.".".$ext;
	  $data['mime']=$_FILES[$name]['type'];
	  $data['caption']=$caption;
	  require_once('Image/Transform.php');
	  $it = Image_Transform::factory('GD');
      $it->load($_FILES[$name]['tmp_name']);
	  $fresize=false;
	  if($resize)
	  { if($x>0 && $it->img_x>$x && $y>0 && $it->img_y>$y)
		{ $it->scaleByX($x);
		  if($it->new_y>$y)
		  $it->crop($x,$y,0,0);
		  $fresize=true;
		}
	    elseif($x>0 && $it->img_x>$x)
        { $it->scaleByX($x);
		  $fresize=true;
		}
        elseif($y>0 && $it->img_y>$y)
        { $it->scaleByY($y);
		  $fresize=true;
		}
	    $data['width']=$it->new_x;
	    $data['height']=$it->new_y;
	  }
	  else
	  { $data['width']=$it->img_x;
	    $data['height']=$it->img_y;
	  }
	  if($fresize)
	  $it->save($data['path']);
	  else
	  copyfile($_FILES[$name]['tmp_name'],$data['path']);
	  clearCacheImage($data['path']);
	  $data['idsec']=getSectionId(A::$REGFILES);
	  $data['iditem']=$iditem;
	  $data['sort']=$sort;
	  if($id==0)
	  $data['id']=A::$DB->Insert("mysite_images",$data);
	  else
	  { A::$DB->Update("mysite_images",$data,"id=$id");
	    $data['id']=$id;
	  }
	  A::$OBSERVER->Event('UploadImage',A::$REGFILES,$data);
	  A::$OBSERVER->Event('RegisterImage',A::$REGFILES,$data);
	  return $data['id'];
	}
  }
  return $id;
}

/**
 * Регистрирует заданное изображение.
 *
 * @param string $path Путь к файлу изображения.
 * @param string $caption Описание изображения.
 * @param integer $id=0 Числовой идентификатор изображения которое будет заменено.
 * @param integer $iditem=0 Идентификатор элемента раздела которому принадлежит изображение.
 * @param integer $sort=0 Порядок среди других принадлежащих элементу.
 * @param boolean $resize Масштабирование.
 * @param integer $x Масштабирование по ширине.
 * @param integer $y Масштабирование по высоте.
 * @return integer Числовой идентификатор с которым зарегистрировано изображение.
 */

function RegisterImage($path,$caption,$id=0,$iditem=0,$sort=0,$resize=false,$x=0,$y=0)
{
  $image_ext=array("gif","jpg","jpeg","png");
  if(file_exists($path) && is_file($path))
  { $ext=$basename="";
	if(!escapeFileName($path,$ext,$basename))
	return $id;
	$basename=translit($basename);
	if(!is_dir("files/mysite/reg_images"))
	mk_dir("files/mysite/reg_images");
	$data=array();
	if(in_array($ext,$image_ext))
	{ $data['path']="files/mysite/reg_images/{$basename}.{$ext}";
	  if(file_exists($data['path']))
	  { $i=1;
	    do
		{ $data['path']="files/mysite/reg_images/{$basename}_".sprintf("%04d",$i).".{$ext}";
		  $i++;
		} while(file_exists($data['path']));
	  }
	  $data['name']=$basename.".".$ext;
	  $data['mime']=getMimeByExt($ext);
	  $data['caption']=$caption;
	  require_once('Image/Transform.php');
	  $it = Image_Transform::factory('GD');
      $it->load($path);
	  $fresize=false;
	  if($resize)
	  { if($x>0 && $it->img_x>$x && $y>0 && $it->img_y>$y)
		{ $it->scaleByX($x);
		  if($it->new_y>$y)
		  $it->crop($x,$y,0,0);
		  $fresize=true;
		}
	    elseif($x>0 && $it->img_x>$x)
		{ $it->scaleByX($x);
		  $fresize=true;
		}
        elseif($y>0 && $it->img_y>$y)
        { $it->scaleByY($y);
		  $fresize=true;
		}
	    $data['width']=$it->new_x;
	    $data['height']=$it->new_y;
	  }
	  else
	  { $data['width']=$it->img_x;
	    $data['height']=$it->img_y;
	  }
	  if($fresize)
	  $it->save($data['path']);
	  else
	  copyfile($path,$data['path']);
	  clearCacheImage($data['path']);
	  $data['idsec']=getSectionId(A::$REGFILES);
	  $data['iditem']=$iditem;
	  $data['sort']=$sort;
	  if($id==0)
	  { $data['id']=A::$DB->Insert("mysite_images",$data);
	    A::$OBSERVER->Event('RegisterImage',A::$REGFILES,$data);
		return $data['id'];
	  }
	  else
	  { if(A::$DB->existsRow("SELECT id FROM mysite_images WHERE id=$id"))
		return 0;
		else
		{ $data['id']=$id;
	      A::$DB->Insert("mysite_images",$data);
		  A::$OBSERVER->Event('RegisterImage',A::$REGFILES,$data);
		  return $data['id'];
		}
	  }
	}
  }
  return $id;
}

/**
 * Удаляет зарегистрированное изображение.
 *
 * @param integer $id Числовой идентификатор изображения которое будет удалено.
 */

function DelRegImage($id)
{
  if($row=A::$DB->getRowById($id,"mysite_images"))
  { delfile($row['path']);
    clearCacheImage($row['path']);
    A::$DB->Delete("mysite_images","id=?i",$id);
	A::$OBSERVER->Event('DeleteImage',A::$REGFILES,$row);
  }
}

/**
 * Удаляет все зарегистрированные изображения принадлежащие разделу.
 *
 * @param integer $idsec Числовой идентификатор раздела.
 */

function DelRegSectionImages($idsec)
{
  A::$DB->query("SELECT * FROM mysite_images WHERE idsec=$idsec");
  while($row=A::$DB->fetchRow())
  { delfile($row['path']);
    clearCacheImage($row['path']);
    A::$OBSERVER->Event('DeleteImage',A::$REGFILES,$row);
  }
  A::$DB->free();
  A::$DB->Delete("mysite_images","idsec=?i",$idsec);
}

/**
 * Удаляет все зарегистрированные изображения принадлежащие элементу раздела.
 *
 * @param integer $idsec Числовой идентификатор раздела.
 * @param integer $iditem Числовой идентификатор элемента.
 */

function DelRegSectionItemImages($idsec,$iditem)
{
  A::$DB->query("SELECT * FROM mysite_images WHERE idsec=$idsec AND iditem=$iditem");
  while($row=A::$DB->fetchRow())
  { delfile($row['path']);
    clearCacheImage($row['path']);
    A::$OBSERVER->Event('DeleteImage',A::$REGFILES,$row);
  }
  A::$DB->free();
  A::$DB->Delete("mysite_images","idsec=?i AND iditem=?i",array($idsec,$iditem));
}

/**
 * Устанавливает новое описание для зарегистрированного изображения.
 *
 * @param integer $id Числовой идентификатор изображения.
 * @param string $caption Новое описание.
 */

function RenameRegImage($id,$caption)
{
  return A::$DB->Update("mysite_images",array('caption'=>$caption),"id=?i",$id);
}

/**
 * Регистрирует загруженный файл.
 *
 * @param string $name Идентфикатор загружаемого файла (значение name в <input type="file" name=...>)
 * @param string $caption Описание файла.
 * @param integer $id=0 Числовой идентификатор файла который будет заменен.
 * @param integer $iditem=0 Идентификатор элемента раздела которому принадлежит файл.
 * @param integer $sort=0 Порядок среди других принадлежащих элементу.
 * @return integer Числовой идентификатор с которым зарегистрирован файл.
 */

function UploadFile($name,$caption,$id=0,$iditem=0,$sort=0)
{
  if(isset($_FILES[$name]['tmp_name']) && file_exists($_FILES[$name]['tmp_name']))
  { $ext=$basename="";
	if(!escapeFileName($_FILES[$name]['name'],$ext,$basename))
	return $id;
	if($id>0)
	delfile(A::$DB->getOne("SELECT path FROM mysite_files WHERE id=$id"));
	if(!is_dir("files/mysite/reg_files"))
	mk_dir("files/mysite/reg_files");
	$data=array();
	$data['path']="files/mysite/reg_files/{$basename}.{$ext}";
	if(file_exists($data['path']))
	{ $i=1;
	  do
	  { $data['path']="files/mysite/reg_files/{$basename}_".sprintf("%04d",$i).".{$ext}";
		$i++;
	  } while(file_exists($data['path']));
	}
	$data['name']=$basename.".".$ext;
	$data['mime']=$_FILES[$name]['type'];
	$data['size']=$_FILES[$name]['size'];
	$data['caption']=$caption;
	if(empty($data['mime']) || $data['mime']=='application/octet-stream')
	$data['mime']=getMimeByExt($ext);
	copyfile($_FILES[$name]['tmp_name'],$data['path']);
	$data['idsec']=getSectionId(A::$REGFILES);
	$data['iditem']=$iditem;
	$data['sort']=$sort;
	if($id==0)
	$data['id']=A::$DB->Insert("mysite_files",$data);
	else
	{ A::$DB->Update("mysite_files",$data,"id=$id");
	  $data['id']=$id;
	}
	A::$OBSERVER->Event('UploadFile',A::$REGFILES,$data);
	A::$OBSERVER->Event('RegisterFile',A::$REGFILES,$data);
	return $data['id'];
  }
  return $id;
}

/**
 * Регистрирует заданный файл.
 *
 * @param string $path Путь к файлу.
 * @param string $caption Описание файла.
 * @param integer $id=0 Числовой идентификатор файла который будет заменен.
 * @param integer $iditem=0 Идентификатор элемента раздела которому принадлежит файл.
 * @param integer $sort=0 Порядок среди других принадлежащих элементу.
 * @return integer Числовой идентификатор с которым зарегистрирован файл.
 */

function RegisterFile($path,$caption,$id=0,$iditem=0,$sort=0)
{
  $path=preg_replace("/^\//i","",$path);
  if(!is_file($path)) return 0;
  $data['path']=$path;
  $data['name']=end(explode('/',$path));
  $data['mime']=getMimeByFile($path);
  $data['size']=filesize($path);
  $data['caption']=$caption;
  $data['idsec']=getSectionId(A::$REGFILES);
  $data['iditem']=$iditem;
  $data['sort']=$sort;
  if($id==0)
  { $data['id']=A::$DB->Insert("mysite_files",$data);
    A::$OBSERVER->Event('RegisterFile',A::$REGFILES,$data);
	return $data['id'];
  }
  else
  { if(A::$DB->existsRow("SELECT id FROM mysite_files WHERE id=$id"))
    return 0;
	else
	{ $data['id']=$id;
      A::$DB->Insert("mysite_files",$data);
	  A::$OBSERVER->Event('RegisterFile',A::$REGFILES,$data);
	  return $data['id'];
	}
  }
  return $id;
}

/**
 * Удаляет зарегистрированный файл.
 *
 * @param integer $id Числовой идентификатор файла который будет удален.
 */

function DelRegFile($id)
{ static $files=null;
  if(is_null($files))
  $files=A::$DB->getAssoc("SELECT id,path FROM mysite_files");
  if($row=A::$DB->getRowById($id,"mysite_files"))
  { unset($files[$row['id']]);
    if(!in_array($row['path'],$files))
    delfile($row['path']);
	A::$DB->Delete("mysite_files","id=?i",$id);
	A::$OBSERVER->Event('DeleteFile',A::$REGFILES,$row);
  }
}

/**
 * Удаляет все зарегистрированные файлы принадлежащие разделу.
 *
 * @param integer $idsec Числовой идентификатор раздела.
 */

function DelRegSectionFiles($idsec)
{ static $files=null;
  if(is_null($files))
  $files=A::$DB->getAssoc("SELECT id,path FROM mysite_files");
  A::$DB->query("SELECT * FROM mysite_files WHERE idsec=$idsec");
  while($row=A::$DB->fetchRow())
  { unset($files[$row['id']]);
    if(!in_array($row['path'],$files))
    delfile($row['path']);
    A::$OBSERVER->Event('DeleteFile',A::$REGFILES,$row);
  }
  A::$DB->free();
  A::$DB->Delete("mysite_files","idsec=?i",$idsec);
}

/**
 * Удаляет все зарегистрированные файлы принадлежащие элементу раздела.
 *
 * @param integer $idsec Числовой идентификатор раздела.
 * @param integer $iditem Числовой идентификатор элемента.
 */

function DelRegSectionItemFiles($idsec,$iditem)
{ static $files=null;
  if(is_null($files))
  $files=A::$DB->getAssoc("SELECT id,path FROM mysite_files");
  A::$DB->query("SELECT * FROM mysite_files WHERE idsec=$idsec AND iditem=$iditem");
  while($row=A::$DB->fetchRow())
  { unset($files[$row['id']]);
    if(!in_array($row['path'],$files))
    delfile($row['path']);
    A::$OBSERVER->Event('DeleteFile',A::$REGFILES,$row);
  }
  A::$DB->free();
  A::$DB->Delete("mysite_files","idsec=?i AND iditem=?i",array($idsec,$iditem));
}

/**
 * Устанавливает новое описание для зарегистрированного файла.
 *
 * @param integer $id Числовой идентификатор файла.
 * @param string $caption Новое описание.
 */

function RenameRegFile($id,$caption)
{
  return A::$DB->Update("mysite_files",array('caption'=>$caption),"id=?i",$id);
}

/**
 * Проверяет корректность расширения файла и преобразует имя файла в транслит.
 *
 * @param string $name Имя файла.
 * @param string &$ext Расширение полученного имени файла.
 * @return string Полученное имя файла или false, если у файла некорректное расширение.
 */

function escapeFileName($name,&$ext=null,&$basename=null)
{
  $path_parts=pathinfo($name);
  $ext=preg_replace("/[^a-z0-9]+/i","",strtolower($path_parts['extension']));
  if($ext && preg_match("/^(.+)\.".$ext."$/i",mb_strtolower(end(explode('/',$name))),$matches))
  { $basename=preg_replace("/[^a-zA-Zа-яА-Я0-9.-]/iu","_",$matches[1]);
    return $basename.'.'.$ext;
  }
  else
  return false;
}

/**
 * Создает архив tar.gz и помещает в него содержмое указанного каталога или файла.
 *
 * @param string $file Имя файла архива (Обязательное расширение tar.gz).
 * @param string $path Файл или каталог который помещается в архив.
 * @return boolean Успешность операции.
 */

function createArchive($file,$path)
{
  require_once("Archive/Tar.php");
  $arch = new Archive_Tar($file,true);
  if(is_dir($path))
  $arch->addModify(array($path),'',$path);
  else
  { $parts=pathinfo($path);
    $arch->addModify(array($path),'',$parts['dirname']);
  }
  return is_file($file);
}

/**
 * Создает архив tar.gz, помещает в него содержмое указанного каталога/файла и сразу отдает его клиенту.
 *
 * @param string $file Имя файла архива (Обязательное расширение tar.gz).
 * @param string $path Файл или каталог который помещается в архив.
 */

function outArchive($file,$path)
{
  require_once('HTTP/Download.php');
  if(createArchive($file,$path))
  { $params=array('file'=>$file,'contenttype'=>'application/gzip','contentdisposition'=>array(HTTP_DOWNLOAD_ATTACHMENT,basename($file)));
    HTTP_Download::staticSend($params,false);
  }
}

/**
 * Извлекает содержимое из архива tar.gz.
 *
 * @param string $file Имя файла архива.
 * @param string $path Каталог в который будет извлечено содержимое архива.
 */

function extractArchive($file,$path,$ext='gz')
{
  if($ext=='gz')
  { require_once("Archive/Tar.php");
    $arch = new Archive_Tar($file,true);
    return $arch->extractModify($path,"");
  }
  elseif($ext=='zip' && extension_loaded('zip'))
  { $zip = new ZipArchive();
    $zip->open($file);
    return $zip->extractTo($path);
  }
  else
  return false;
}

/**
 * Возвращает данные о дополнительных полях раздела или дополнения.
 *
 * @param string $item Полный строковой идентификатор раздела или дополнения.
 * @param string $type='' Тип полей для выборки.
 * @param string $get='' Какие данные извлекать, по умолчанию описание.
 * @return array Асоциированный массив: Идентификатор поля => Описание.
 */

function getFields($item,$type='',$get='')
{ static $fields=array();
  if(isset($fields[$item]))
  return $fields[$item];
  else
  { $where=!empty($type)?" AND type='$type'":"";
    if(empty($get))
	$get='f.name_ru';
    return $fields[$item]=A::$DB->getAssoc("SELECT f.field,$get FROM mysite_fields AS f WHERE f.item='$item'$where ORDER BY sort");
  }
}

/**
 * Дополняет данные в записи данными дополнительных полей.
 *
 * @param string $item Полный строковой идентификатор раздела или дополнения.
 * @param array &$data Запись в виде ассоциированного массива.
 * @return array Массив идентификаторов всех дополнительных полей.
 */

function prepareValues($item,&$data)
{ static $fields=array();
  if(!isset($fields[$item]))
  $fields[$item]=A::$DB->getAssoc("SELECT f.field,f.* FROM mysite_fields AS f WHERE f.item='$item' ORDER BY sort");
  $data['fields']=array();
  foreach($fields[$item] as $field=>$row)
  if(isset($data[$field]))
  $data['fields'][]=array('field'=>$field,'name'=>$row['name_ru'],'value'=>is_array($data[$field])?$data[$field]['name']:$data[$field]);
  return array_keys($fields[$item]);
}

/**
 * Защищает строку с фрагментом SQL запроса для ORDER BY.
 *
 * @param string $item Полный строковой идентификатор раздела или дополнения.
 * @return string
 */

function escape_order_string($string)
{
  $in=explode(",",$string);
  $out=array();
  foreach($in as $i=>$s)
  if(preg_match("/^[a-zA-Z0-9_]+\s?(|DESC|ASC)$/i",$s=trim($s)))
  $out[]=$s;
  return $out?implode(",",$out):'id';
}

/**
 * Чистит HTML.
 *
 * @param string $html Форматированный текст.
 * @return string
 */

function htmlsafe($html,$allow=null)
{ static $jevix=null;
  require_once("system/libs/jevix.php");
  if(preg_match_all("@(<\s*param\s*name\s*=\s*\".*\"\s*value\s*=\s*\".*\")\s*/?\s*>(?!</param>)@ui",$html,$aMatch))
  foreach($aMatch[1] as $key => $str)
  { $str_new=$str.'></param>';
    $html=str_replace($aMatch[0][$key],$str_new,$html);
  }
  if(preg_match_all("@(<\s*embed\s*.*)\s*/?\s*>(?!</embed>)@ui",$html,$aMatch))
  foreach($aMatch[1] as $key => $str)
  { $str_new=$str.'></embed>';
    $html=str_replace($aMatch[0][$key],$str_new,$html);
  }
  if(preg_match_all("@(<param\s.*name=\"wmode\".*>\s*</param>)@ui",$html,$aMatch))
  foreach($aMatch[1] as $key => $str)
  $html=str_replace($aMatch[0][$key],'',$html);
  if(preg_match_all("@(<object\s.*>)@ui",$html,$aMatch))
  foreach($aMatch[1] as $key => $str)
  $html=str_replace($aMatch[0][$key],$aMatch[0][$key].'<param name="wmode" value="opaque"></param>',$html);
  if(is_null($jevix))
  { $jevix = new Jevix();
    if($allow)
	$jevix->cfgAllowTags($allow);
	else
	$jevix->cfgAllowTags(array('p', 'cut', 'a', 'img', 'i', 'b', 'u', 's', 'video', 'em',  'strong', 'nobr', 'li', 'ol', 'ul', 'sup', 'abbr', 'sub', 'acronym', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'br', 'hr', 'pre', 'code', 'object', 'param', 'embed', 'blockquote', 'table', 'tr', 'th', 'td', 'tbody'));
	$jevix->cfgSetTagShort(array('br','img', 'hr', 'cut'));
	$jevix->cfgSetTagPreformatted(array('pre','code','video'));
	$jevix->cfgAllowTagParams('img', array('src', 'alt' => '#text', 'title', 'align' => array('right', 'left', 'center'), 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int'));
	$jevix->cfgAllowTagParams('a', array('title', 'href', 'rel'));
	$jevix->cfgAllowTagParams('p', array('align','style'));
	$jevix->cfgAllowTagParams('cut', array('name'));
	$jevix->cfgAllowTagParams('object', array('width' => '#int', 'height' => '#int', 'data' => '#link'));
	$jevix->cfgAllowTagParams('param', array('name' => '#text', 'value' => '#text'));
	$jevix->cfgAllowTagParams('embed', array('src' => '#image', 'type' => '#text','allowscriptaccess' => '#text', 'allowfullscreen' => '#text','width' => '#int', 'height' => '#int', 'flashvars'=> '#text', 'wmode'=> '#text'));
	$jevix->cfgAllowTagParams('table', array('border', 'cellpadding', 'cellspacing', 'width'));
    $jevix->cfgAllowTagParams('th', array('width', 'colspan', 'rowspan'));
	$jevix->cfgAllowTagParams('td', array('width', 'colspan', 'rowspan'));
	$jevix->cfgSetTagParamsRequired('img', 'src');
	$jevix->cfgSetTagParamsRequired('a', 'href');
	$jevix->cfgSetTagCutWithContent(array('script', 'iframe', 'style'));
	$jevix->cfgSetTagChilds('ul', array('li'), false, true);
	$jevix->cfgSetTagChilds('ol', array('li'), false, true);
	$jevix->cfgSetTagChilds('object', 'param', false, true);
	$jevix->cfgSetTagChilds('object', 'embed', false, false);
	$jevix->cfgSetTagChilds('table', array('tr', 'tbody'), false, false);
    $jevix->cfgSetTagChilds('tbody', array('tr'), false, false);
    $jevix->cfgSetTagChilds('tr', array('td', 'th'), false, false);
	$jevix->cfgSetTagIsEmpty(array('param','embed'));
	$jevix->cfgSetTagNoAutoBr(array('ul','ol','object'));
	$jevix->cfgSetTagParamDefault('embed','wmode','opaque',true);
	$jevix->cfgSetAutoBrMode(false);
	$jevix->cfgSetAutoReplace(array('+/-', '(c)', '(с)', '(r)', '(C)', '(С)', '(R)','<o:p>','</o:p>'), array('±', '©', '©', '®', '©', '©', '®','',''));
	$jevix->cfgSetTagNoTypography('code');
	$jevix->cfgSetTagNoTypography('video');
	$jevix->cfgSetTagNoTypography('object');
	$jevix->cfgSetTagBlockType(array('h1','h2','h3','h4','h5','h6','ol','ul','blockquote','pre'));
  }
  $html=$jevix->parse($html,$err=null);
  $html=preg_replace('/<video>http:\/\/(?:www\.|)youtube\.com\/watch\?v=([a-zA-Z0-9_\-]+)<\/video>/ui', '<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/$1&hl=en"></param><param name="wmode" value="opaque"></param><embed src="http://www.youtube.com/v/$1&hl=en" type="application/x-shockwave-flash" wmode="opaque" width="425" height="344"></embed></object>', $html);
  $html=preg_replace('/<video>http:\/\/(?:www\.|)rutube.ru\/tracks\/\d+.html\?v=([a-zA-Z0-9_\-]+)<\/video>/ui', '<OBJECT width="470" height="353"><PARAM name="movie" value="http://video.rutube.ru/$1"></PARAM><PARAM name="wmode" value="opaque"></PARAM><PARAM name="allowFullScreen" value="true"></PARAM><PARAM name="flashVars" value="uid=662118"></PARAM><EMBED src="http://video.rutube.ru/$1" type="application/x-shockwave-flash" wmode="opaque" width="470" height="353" allowFullScreen="true" flashVars="uid=662118"></EMBED></OBJECT>', $html);
  $html=str_replace("<code>",'<pre class="prettyprint"><code>',$html);
  $html=str_replace("</code>",'</code></pre>',$html);
  return $html;
}

/**
 * Разрезает HTML по тегу cut.
 *
 * @param string $html Форматированный текст.
 * @return array
 */

function Cut($html)
{
  $result=array('description'=>$html,'content'=>$html,'catname'=>false);
  if(preg_match("/^(.*)<cut(.*)>(.*)$/ui",$html,$aMatch))
  { $result['description']=$aMatch[1];
    $result['content']=$aMatch[1].' '.$aMatch[3];
    if(preg_match('/^\s*name\s*=\s*"(.+)"\s*\/?$/ui',$aMatch[2],$aMatchCut))
    $result['catname']=trim($aMatchCut[1]);
  }
  return $result;
}

/**
 * Возвращает содержимое URL.
 *
 * @param string $url URL.
 * @return string
 */

function url_get_content($url)
{
  if(extension_loaded('curl'))
  { $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }
  else
  return @file_get_contents($url);
}

function idn_decode($idn)
{
  require_once('system/libs/idna.php');
  $idna = new idna_convert();
  return $idna->decode($idn);
}