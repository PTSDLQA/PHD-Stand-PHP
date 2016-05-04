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
 * Генерация ссылки на страницу.
 *
 * @param integer $id Идентификатор страницы.
 * @param string $section Полный строковой идентификатор раздела.
 * @return string
 */

function pages_createItemLink($id,$section)
{ static $cache=array();
  if(isset($cache[$section][$id]))
  return $cache[$section][$id];
  if($row=A::$DB->getRow("SELECT idker,urlname,type FROM {$section} WHERE id=".(integer)$id))
  { $link=pages_createItemLink($row['idker'],$section);
    if($row['type']=='page')
	{ if($row['urlname']!="index")
	  $link.=$row['urlname'].".html";
	}
	else
	$link.=$row['urlname']."/";
  	return $cache[$section][$id]=$link;
  }
  else
  return $cache[$section][$id]=getSectionLink($section);
}

/**
 * Индексация всех страниц в базу поиска по сайту.
 *
 * @param string $section Полный строковой идентификатор раздела.
 */

function pages_searchIndexAll($section)
{
  $idsec=getSectionId($section);
  A::$DB->query("SELECT * FROM {$section} WHERE type='page'");
  while($row=A::$DB->fetchRow())
  { $name=getTreePath($section,$row['level']==0||$row['urlname']!='index'?$row['id']:$row['idker']," - ");
	A_SearchEngine::getInstance()->updateIndex($idsec,$row['id'],$name,$row['content'],$row['tags']);
  }
  A::$DB->free();
}

function pages_createSubMap(&$treemap,$section,$caption="",$id=0,$f=false)
{
  A::$DB->query("SELECT id,name,type,urlname,level FROM {$section} WHERE idker={$id} AND active='Y' AND inmap='Y' ORDER BY sort");
  while($row=A::$DB->fetchRow())
  { if($row['type']=="page" && $row['urlname']=="index" && (!$f || $row['level']>0))
    continue;
	$treemap->items[$section.'|'.$row['id']] = new SiteMap_Box($row['name'],pages_createItemLink($row['id'],$section));
	if($row['type']=="dir")
    pages_createSubMap($treemap->items[$section.$row['id']],$section,"",$row['id']);
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

function pages_createMap(&$treemap,$section,$caption="")
{
  if(A::$OPTIONS['mainsection']==getName($section))
  pages_createSubMap($treemap,$section,$caption,0,true);
  else
  { $treemap->items[$section] = new SiteMap_Box($caption,getSectionLink($section));
    pages_createSubMap($treemap->items[$section],$section,$caption);
  }
}

/**
 * Получение полных данных страницы.
 *
 * @param integer $id Идентификатор страницы.
 * @param string $section Полный строковой идентификатор раздела.
 * @param integer $idsec=0 Числовой идентификатор раздела.
 * @return array
 */

function pages_getItem($id,$section,$idsec=0)
{ static $cache=array();
  if(isset($cache[$section][$id]))
  return $cache[$section][$id];
  if($row=A::$DB->getRow("SELECT * FROM {$section} WHERE id=?i AND active='Y'",$id))
  { $row['link']=pages_createItemLink($row['id'],$section);
    $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
    prepareValues($section,$row);
    return $cache[$section][$id]=$row;
  }
  else
  return $cache[$section][$id]=false;
}

/**
 * Обработчик события "Создание раздела".
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

function pages_createSection($section,$params)
{
  if($params['module']=='pages')
  { $data=array('date'=>time(),'name'=>$params['caption'],'urlname'=>'index');
    A::$DB->Insert($section,$data);
  }
}

A::$OBSERVER->AddHandler('CreateSection','pages_createSection');