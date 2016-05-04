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
 * Обработчик события "Обновление элемента в базе поиска по сайту".
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

function search_update($section,$params)
{
  $idsec=getSectionId($section);
  $tags=!empty($params['tags'])?$params['tags']:"";
  A_SearchEngine::getInstance()->updateIndex($idsec,$params['id'],$params['name'],$params['content'],$tags);
}

/**
 * Обработчик события "Удаление элемента из базы поиска по сайту".
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

function search_delete($section,$params)
{
  $idsec=getSectionId($section);
  A_SearchEngine::getInstance()->deleteIndex($idsec,$params['id']);
}

/**
 * Обработчик события "Создание раздела".
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

function search_createSection($section,$params)
{
  if($params['module']=='search')
  {
    @set_time_limit(0);

    $sections=A::$DB->getAll("SELECT * FROM ".DOMAIN."_sections WHERE lang='".A::$LANG."' OR lang='all' ORDER BY sort");

	A::$DB->caching=false;

	foreach($sections as $srow)
	if(function_exists($srow['module'].'_searchIndexAll'))
	{ A_SearchEngine::getInstance()->deleteSection($srow['id']);
	  call_user_func($srow['module'].'_searchIndexAll',DOMAIN."_".$srow['lang']."_".$srow['name']);
	}

	A::$CACHE->resetSection($section);
  }
}

/**
 * Обработчик события "Удаление раздела".
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

function search_deleteSection($section,$params)
{
  if($params['module']=='search')
  A_SearchEngine::getInstance()->resetSection();
  else
  A_SearchEngine::getInstance()->deleteSection($params['id']);
}

A::$OBSERVER->AddHandler('searchIndexUpdate','search_update');
A::$OBSERVER->AddHandler('searchIndexDelete','search_delete');
A::$OBSERVER->AddHandler('CreateSection','search_createSection');
A::$OBSERVER->AddHandler('DeleteSection','search_deleteSection');