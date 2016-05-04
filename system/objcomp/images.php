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
 * Компонент управления прикрепленными изображениями.
 */

class A_Images extends A_Component
{
/**
 * Конструктор.
 *
 * @param integer $iditem Идентификатор элемента раздела.
 * @param integer $rows=0 Количество строк.
 */

  function __construct($iditem,$rows=0)
  {
	if(isset(A::$MAINFRAME))
	{ A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/images.js");
	  A::$MAINFRAME->AddJVar("images_iditem",$iditem);
	  if(!empty($rows))
	  A::$MAINFRAME->AddJVar("images_rows",$rows);

	  $this->Assign("mainframe",true);
	}

	parent::__construct("images.tpl");
  }
}