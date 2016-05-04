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
 * Компонент "файловый менеджер".
 */

class A_FileAdmin extends A_Component
{
  private $id;
  private $curdir;
  private $basedir;

/**
 * Конструктор.
 *
 * @param string $basedir Базовая директория, выше которой доступ запрещен.
 * @param string $curdir='' Стартовая директория, если не указано то берется базовая.
 * @param string $id='fa' Идентификатор компонента.
 */

  function __construct($basedir,$curdir='',$id='fa')
  {
	$id=$this->id=$id.A::$AUTH->id;

	$this->basedir='./'.preg_replace("/^\//i","",preg_replace("/[^a-zA-Zа-яА-Я0-9-_\/]/iu","",A_Session::get("{$id}_basedir",$basedir)));
	$this->curdir='./'.preg_replace("/^\//i","",preg_replace("/[^a-zA-Zа-яА-Я0-9-_\/]/iu","",A_Session::get("{$id}_curdir",!empty($curdir)?$curdir:$basedir)));

	if(!A_Session::is_set("{$id}_basedir"))
	A_Session::set("{$id}_basedir",$this->basedir);
	if(!A_Session::is_set("{$id}_curdir"))
	A_Session::set("{$id}_curdir",$this->curdir);

	A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/fileadmin.js");
	A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/tpleditor.js");
	A::$MAINFRAME->AddJVar("fa_oid",$id);

	parent::__construct("fileadmin.tpl");
  }
}