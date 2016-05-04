<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Modules
 */
/**************************************************************************/

/**
 * Панель управления модуля "Архив материалов".
 *
 * <a href="http://wiki.a-cms.ru/modules/archive">Руководство</a>.
 */

class ArchiveModule_Admin extends A_MainFrame
{

/**
 * Конструктор.
 */

  function __construct()
  {
    parent::__construct("module_archive.tpl");
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "savesections": $res=$this->SaveSections(); break;
	}
    if(!empty($res))
	A::goUrl("admin.php?mode=sections&item=".SECTION);
  }

/**
 * Обработчик действия: Сохранение используемых разделов.
 */

  function SaveSections()
  {
	return setOption(SECTION,'sections',isset($_REQUEST['ids'])?serialize($_REQUEST['ids']):"");
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$checkeds=!empty(A::$OPTIONS['sections'])?unserialize(A::$OPTIONS['sections']):array();

    $sections=array();
	A::$DB->query("SELECT * FROM ".DOMAIN."_sections WHERE active='Y' AND module='catalog' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $row['checked']=in_array($row['id'],$checkeds);
	  $sections[]=$row;
	}
	A::$DB->free();

	$this->Assign("sections",$sections);

	$this->Assign("optbox1",new A_OptionsBox("Внешний вид на сайте:",array('idgroup'=>1)));
  }
}

A::$MAINFRAME = new ArchiveModule_Admin;