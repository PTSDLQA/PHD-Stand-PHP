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
 * Обработчик события "Создание раздела".
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

function sitemap_createSection($section,$params)
{
  if($params['module']=='sitemap')
  { $ids=A::$DB->getCol("SELECT id FROM ".getDomain($section)."_sections WHERE lang='".A::$LANG."' OR lang='all'");
    setTextOption($section,'sections',serialize($ids));
  }
  elseif($sitemap=getSectionByModule('sitemap'))
  { $ids=getTextOption($sitemap,'sections');
    $ids=!empty($ids)?unserialize($ids):array();
    if(!$ids)
	$ids=array();
    $ids[]=$params['id'];
    setTextOption($sitemap,'sections',serialize($ids));
  }
}

A::$OBSERVER->AddHandler('CreateSection','sitemap_createSection');

/**
 * Генерация карты XML.
 */

function sitemap_outXML()
{
  A::$CACHE->page=null;
  require_once("modules/sitemap/sitemap.php");

  $checkeds=getTextOption(getSectionByModule('sitemap'),'sections');
  $checkeds=!empty($checkeds)?unserialize($checkeds):array();

  A::$DB->query("SELECT * FROM ".DOMAIN."_sections WHERE lang='".LANG."' OR lang='all' ORDER BY sort");
  while($row=A::$DB->fetchRow())
  if(in_array($row['id'],$checkeds))
  { if(function_exists($row['module'].'_createMap'))
    { $section = DOMAIN."_".$row['lang']."_".$row['name'];
	  if($caption=!empty($row['caption_'.LANG])?$row['caption_'.LANG]:$row['caption'])
	  call_user_func($row['module']."_createMap",A::$MAINFRAME->treemap,$section,$caption);
	}
  }
  A::$DB->free();

  require_once 'XML/Serializer.php';

  $options = array(
  XML_SERIALIZER_OPTION_XML_DECL_ENABLED=>true,
  XML_SERIALIZER_OPTION_XML_ENCODING=>"utf-8",
  XML_SERIALIZER_OPTION_INDENT      => "\t",
  XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
  XML_SERIALIZER_OPTION_ROOT_NAME   => 'urlset',
  XML_SERIALIZER_OPTION_ROOT_ATTRIBS => array('xmlns'=>'http://www.sitemaps.org/schemas/sitemap/0.9'),
  XML_SERIALIZER_OPTION_DEFAULT_TAG => 'url');

  $serializer = new XML_Serializer($options);

  $data=array();
  sitemap_itemXML(A::$MAINFRAME->treemap,$data);
  $serializer->serialize($data);
  header("Content-type: text/xml; charset=utf-8");
  die($serializer->getSerializedData());
}

function sitemap_itemXML($tree,&$data)
{
  if(isset($tree->link))
  $data[]=array('loc'=>'http://'.HOSTNAME.$tree->link);
  foreach($tree->items as &$ti)
  sitemap_itemXML($ti,$data);
}