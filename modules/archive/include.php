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
 * Интеграция в карту сайта.
 *
 * @param object &$treemap Объект дерева карты сайта.
 * @param string $section Полный строковой идентификатор раздела.
 * @param string $caption Название раздела.
 */

function archive_createMap($treemap,$section,$caption)
{
  $treemap->items[$section] = new SiteMap_Box($caption,getSectionLink($section));
}

/**
 * Обработчик события "Создание раздела".
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

function archive_createSection($section,$params)
{
  if($params['module']=='archive')
  { $ids=A::$DB->getCol("
    SELECT id FROM ".getDomain($section)."_sections
	WHERE module='catalog' AND (lang='".A::$LANG."' OR lang='all')");
    setOption($section,'sections',serialize($ids));
  }
  elseif($params['module']=='catalog')
  { if($archive=getSectionByModule('archive'))
    { $ids=getOption($archive,'sections');
      $ids=!empty($ids)?unserialize($ids):array();
      $ids[]=$params['id'];
      setOption($archive,'sections',serialize($ids));
    }
  }
}

A::$OBSERVER->AddHandler('CreateSection','archive_createSection');