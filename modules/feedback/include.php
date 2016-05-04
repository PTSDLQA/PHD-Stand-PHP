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

function feedback_createMap($treemap,$section,$caption)
{
  $treemap->items[$section] = new SiteMap_Box($caption,getSectionLink($section));
}

/**
 * Индексация страницы в базу поиска по сайту.
 *
 * @param string $section Полный строковой идентификатор раздела.
 */

function feedback_searchIndexAll($section)
{
  $idsec=getSectionId($section);
  $name=A::$DB->getOne("SELECT caption_".A::$LANG." FROM ".DOMAIN."_sections WHERE id=".$idsec);
  A_SearchEngine::getInstance()->updateIndex($idsec,0,$name,getTextOption($section,'content'));
}

/**
 * Обработчик события "Создание раздела".
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

function feedback_createSection($section,$params)
{
  if($params['module']=='feedback')
  { $lang=!empty($params['lang']) && $params['lang']!='all'?$params['lang']:DEFAULTLANG;
    $sort=A::$DB->getOne("SELECT MAX(sort) FROM ".DOMAIN."_fields WHERE item='$section'")+1;
    $data=array('item'=>$section,'field'=>'name','name_'.$lang=>'Ваше имя','type'=>'string','property'=>50,'fill'=>'Y','sort'=>$sort++);
    A::$DB->Insert(DOMAIN."_fields",$data);
    $data=array('item'=>$section,'field'=>'email','name_'.$lang=>'Ваш email','type'=>'string','property'=>50,'fill'=>'Y','sort'=>$sort++);
    A::$DB->Insert(DOMAIN."_fields",$data);
    $data=array('item'=>$section,'field'=>'message','name_'.$lang=>'Сообщение','type'=>'text','property'=>5,'fill'=>'Y','sort'=>$sort++);
    A::$DB->Insert(DOMAIN."_fields",$data);
  }
}

A::$OBSERVER->AddHandler('CreateSection','feedback_createSection');