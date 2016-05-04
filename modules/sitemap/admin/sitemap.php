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
 * Панель управления модуля "Карта сайта".
 *
 * <a href="http://wiki.a-cms.ru/modules/sitemap">Руководство</a>.
 */

class SiteMapModule_Admin extends A_MainFrame
{
/**
 * Конструктор.
 */

  function __construct()
  {
    parent::__construct("module_sitemap.tpl");

	$this->AddJScript("/modules/sitemap/admin/sitemap.js");
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "save": $res=$this->Save(); break;
	}
    if(!empty($res))
	A::goUrl("admin.php?mode=sections&item=".SECTION);
  }

/**
 * Обработчик действия: Сохранение выбранных разделов.
 */

  function Save()
  {
	return setTextOption(SECTION,'sections',isset($_REQUEST['ids'])?serialize($_REQUEST['ids']):"");
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$checkeds=getTextOption(SECTION,'sections');
	$checkeds=!empty($checkeds)?unserialize($checkeds):array();

    $sections=array();
	A::$DB->query("
	SELECT * FROM ".DOMAIN."_sections
	WHERE lang='".LANG."' OR lang='all' AND module<>'sitemap'
	ORDER BY sort");
	while($row=A::$DB->fetchRow())
	if(function_exists($row['module'].'_createMap'))
	{ $row['checked']=in_array($row['id'],$checkeds);
	  $sections[]=$row;
	}
	A::$DB->free();

	$this->Assign("sections",$sections);
	$this->AddJVar("csections",count($sections));
  }
}

A::$MAINFRAME = new SiteMapModule_Admin;