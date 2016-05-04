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
 * Генерация ссылки на страницу категории.
 *
 * @param integer $id Идентификатор категории.
 * @param string $section Полный строковой идентификатор раздела.
 * @return string
 */

function catalog_createCategoryLink($id,$section)
{ static $cache=array();
  if(isset($cache[$section][$id]))
  return $cache[$section][$id];
  if($row=A::$DB->getRow("SELECT idker,urlname FROM {$section}_categories WHERE id=".(integer)$id))
  return $cache[$section][$id]=catalog_createCategoryLink($row['idker'],$section).$row['urlname']."/";
  else
  return $cache[$section][$id]=getSectionLink($section);
}

/**
 * Генерация ссылки на детальную страницу материала.
 *
 * @param integer $id Идентификатор материала.
 * @param string $section Полный строковой идентификатор раздела.
 * @return string
 */

function catalog_createItemLink($id,$section)
{ static $cache=array();
  if(isset($cache[$section][$id]))
  return $cache[$section][$id];
  if($row=A::$DB->getRow("SELECT idcat,urlname FROM {$section}_catalog WHERE id=".(integer)$id))
  return $cache[$section][$id]=catalog_createCategoryLink($row['idcat'],$section).$row['urlname'].".html";
  else
  return $cache[$section][$id]=getSectionLink($section);
}

/**
 * Интеграция в карту сайта ссылок на категории.
 *
 * @param object &$treeitem Объект дерева карты сайта.
 * @param string $section Полный строковой идентификатор раздела.
 * @param integer $id=0 Идентификатор родительской категории.
 */

function catalog_createMap_Categories(&$treeitem,$section,$id=0)
{
  A::$DB->query("SELECT * FROM {$section}_categories WHERE idker={$id} AND active='Y' ORDER BY sort");
  while($row=A::$DB->fetchRow())
  { $treeitem->items[$row['id']] = new SiteMap_Box($row['name'],catalog_createCategoryLink($row['id'],$section));
    catalog_createMap_Categories($treeitem->items[$row['id']],$section,$row['id']);
  }
  A::$DB->free();
}

/**
 * Интеграция в карту сайта.
 *
 * @param object &$treemap Объект дерева карты сайта.
 * @param string $section Полный строковой идентификатор раздела.
 * @param string $caption Название раздела.
 */

function catalog_createMap(&$treemap,$section,$caption)
{
  $treemap->items[$section] = new SiteMap_Box($caption,getSectionLink($section));
  catalog_createMap_Categories($treemap->items[$section],$section);
}

/**
 * Интеграция вариантов доступа.
 *
 * @param array &$access Массив вариантов доступа.
 */

function catalog_Access(&$access)
{
  $access[]=array('name'=>'comment','caption'=>"Комментирование",'auth'=>false);
  $access[]=array('name'=>'download','caption'=>"Скачивание файлов",'auth'=>false);
}

/**
 * Индексация всех материалов в базу поиска по сайту.
 *
 * @param string $section Полный строковой идентификатор раздела.
 */

function catalog_searchIndexAll($section)
{
  $idsec=getSectionId($section);
  A::$DB->query("SELECT id,description,tags FROM {$section}_categories WHERE active='Y'");
  while($row=A::$DB->fetchRow())
  A_SearchEngine::getInstance()->updateIndex($idsec,-$row['id'],getTreePath($section."_categories",$row['id']," - "),$row['description'],$row['tags']);
  A::$DB->free();
  A::$DB->query("SELECT id,idcat,name,content,tags FROM {$section}_catalog WHERE active='Y'");
  while($row=A::$DB->fetchRow())
  { $category=getTreePath($section."_categories",$row['idcat']," - ");
	$name=!empty($category)?$category.' - '.$row['name']:$row['name'];
	A_SearchEngine::getInstance()->updateIndex($idsec,$row['id'],$name,$row['content'],$row['tags']);
  }
  A::$DB->free();
}

/**
 * Получение полных данных записи каталога.
 *
 * @param integer $id Идентификатор материала.
 * @param string $section Полный строковой идентификатор раздела.
 * @param integer $idsec=0 Числовой идентификатор раздела.
 * @return array
 */

function catalog_getItem($id,$section,$idsec=0)
{ static $cache=array();
  if(isset($cache[$section][$id]))
  return $cache[$section][$id];
  if($row=A::$DB->getRow("SELECT * FROM {$section}_catalog WHERE id=?i AND active='Y'",$id))
  { if(!$idsec)
    $idsec=getSectionId($section);
    $row['link']=catalog_createItemLink($row['id'],$section);
    $row['vote']=round($row['vote'],2);
	if($row['idcat']>0)
	$row['category']=getTreePath("{$section}_categories",$row['idcat']);
	$row['images']=A::$DB->getAll("SELECT * FROM ".A::$DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array($idsec,$row['id']));
	$row['idimg']=isset($row['images'][0]['id'])?$row['images'][0]['id']:0;
	$row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	prepareValues($section,$row);
    $row=A::$OBSERVER->Modifier('catalog_prepareValues',$section,$row);
    return $cache[$section][$id]=$row;
  }
  else
  return $cache[$section][$id]=false;
}

/**
 * Функция загрузки списка из данных каталога.
 *
 * @param string $item Полный строковой идентификатор раздела.
 */

function catalog_loadlist($item)
{
  $rows=A::$DB->getAll("SELECT * FROM {$item}_catalog LIMIT 0,500");
  $rows=array_multisort_key($rows,'name');
  $list=array();
  foreach($rows as $row)
  { prepareValues($item,$row);
    $list[$row['id']]=$row;
  }
  return $list;
}

function catalog_add($item,$name)
{
  $data=array('date'=>time(),'name'=>$name,'urlname'=>getURLName($name));
  return A::$DB->Insert("{$item}_catalog",$data);
}