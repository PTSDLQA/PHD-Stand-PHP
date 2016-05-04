<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Modules
 */
/**************************************************************************/

require_once("system/framework/html.php");
require_once("system/framework/tree.php");

/**
 * Узел дерева элементов карты сайта.
 */

class SiteMap_Box extends A_HTMLContent
{
/**
 * Дочерние элементы.
 */

  public $items=array();

/**
 * Название узла.
 */

  public $name;

/**
 * Ссылка.
 */

  public $link;

/**
 * Идентификатор узла.
 */

  static $boxid=0;

/**
 * Конструктор.
 *
 * @param string $name Название.
 * @param string $link Ссылка.
 */

  function __construct($name,$link)
  {
    $this->name=$name;
	$this->link=$link;
  }

/**
 * Формирование HTML кода узла со всеми дочерними элементами.
 *
 * @param integer $level Уровень узла.
 */

  function createData($level)
  {
	foreach($this->items as $item)
    $this->AddContent($item->getContent($level+1));
	$smarty = new Smarty();
	$smarty->template_dir=SMARTY_TEMPLATES."/others/";
	$smarty->compile_dir=SMARTY_COMPILE."/others/";
	if(A::$OPTIONS['smartysecurity'])
	{ $this->security=true;
	  $this->secure_dir=array(SMARTY_TEMPLATES."/others");
	}
	$smarty->Assign("level",$level);
	$smarty->Assign("id","mapbox".(SiteMap_Box::$boxid++));
	$smarty->Assign_by_ref("name",$this->name);
	$smarty->Assign_by_ref("link",$this->link);
	$smarty->Assign("content",$this->content);
	$this->content=$smarty->fetch("sitemap_box.tpl");
  }

/**
 * Возвращает HTML узла со всеми дочерними элементами.
 *
 * @param integer $level=1 Уровень узла.
 */

  function getContent($level=1)
  { $this->createData($level);
    return $this->content;
  }
}

/**
 * Модуль "Карта сайта".
 *
 * <a href="http://wiki.a-cms.ru/modules/sitemap">Руководство</a>.
 */

class SiteMapModule extends A_MainFrame
{
/**
 * Объект дерева элементов.
 */

  public $treemap;

/**
 * Конструктор.
 */

  function __construct()
  {
    $this->treemap = new A_ExpandTree();

    parent::__construct();
  }

/**
 * Маршрутизатор URL.
 *
 * @param array $uri Элементы полного пути URL.
 */

  function Router($uri)
  {
    if(count($uri)==0)
	$this->page="sitemap";
	else
	A::NotFound();
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$this->supportCached();

	$checkeds=getTextOption(SECTION,'sections');
	$checkeds=!empty($checkeds)?unserialize($checkeds):array();

	A::$DB->query("SELECT * FROM ".DOMAIN."_sections WHERE lang='".LANG."' OR lang='all' ORDER BY sort");
	while($row=A::$DB->fetchRow())
	if(in_array($row['id'],$checkeds))
	{ if(function_exists($row['module'].'_createMap'))
	  { $section = DOMAIN."_".$row['lang']."_".$row['name'];
		$caption=!empty($row['caption_'.LANG])?$row['caption_'.LANG]:$row['caption'];
		call_user_func($row['module']."_createMap",$this->treemap,$section,$caption);
	  }
	}
	A::$DB->free();

	$this->Assign_by_ref("treemap",$this->treemap);
	$this->AddNavigation(SECTION_NAME);
  }
}

if(A::$CACHE->page)
A::$CACHE->page->restore();

A::$MAINFRAME = new SiteMapModule;