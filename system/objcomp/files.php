<?php
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AComponents
 */
/**************************************************************************/

require_once("system/framework/comp.php");

/**
 * Компонент управления прикрепленными файлами.
 */

class A_Files extends A_Component
{
/**
 * Конструктор.
 *
 * @param integer $iditem Идентификатор элемента раздела.
 */

  function __construct($iditem)
  {
	if(isset(A::$MAINFRAME))
	{ A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/files.js");
	  A::$MAINFRAME->AddJScript("var files_iditem=$iditem;","code");

	  $this->Assign("mainframe",true);
	}

	parent::__construct("files.tpl");
  }

/**
 * Формирование данных шаблона.
 */

  function createData()
  {
    $files=array();
	if(is_dir($dir='ifiles'))
    { $_files=scandir($dir);
	  sort($_files);
	  foreach($_files as $file)
	  if($file!='.' && $file!='..' && is_file("$dir/$file"))
	  $files["$dir/$file"]=$file;
	}
	if(is_dir($dir='files/mysite/ifiles'))
    { $_files=scandir($dir);
	  sort($_files);
	  foreach($_files as $file)
	  if($file!='.' && $file!='..' && is_file("$dir/$file"))
	  $files["$dir/$file"]=$file;
	}
    $this->Assign('files',$files);
  }
}