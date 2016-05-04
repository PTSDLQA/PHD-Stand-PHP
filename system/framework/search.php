<?php
/** \file system/framework/search.php
 * Система поиска по сайту.
 */
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AFramework
 */
/**************************************************************************/

/**
 * Класс реализует механизмы поиска по сайту.
 */

class A_SearchEngine
{
  private static $instance;
  private $section;

  private $centroids;
  private $tags;
  private $clusters;

/**
 * Конструктор.
 *
 * @param string $section='' Полный строковой идентификатор раздела поиска.
 */

  function __construct($section='')
  {
    $this->section=$section;
  }

/**
 * Возвращает одиночный объект класса.
 */

  function getInstance()
  {
    if(!self::$instance)
    { $section=getSectionByModule('search');
	  self::$instance = new A_SearchEngine($section);
    }

    return self::$instance;
  }

/**
 * Отключает механизмы индексации.
 */

  function resetSection()
  {
	self::$instance->section = '';
  }

  function s_replace(&$s,$re,$to)
  {
    $orig = $s;
    $s = preg_replace($re,$to,$s);
    return $orig !== $s;
  }

/**
 * Преобразует слово в стем.
 *
 * @param string $word Слово.
 * @return string Стем.
 */

  function getStem($word)
  {
    $word = preg_replace("/[^a-zA-Zа-яА-Я0-9-]/iu","",$word);
	$word = str_replace("ё","е",mb_strtolower($word));
    $stem = $word;

    if(!preg_match('/^(.*?[аеиоуыэюя])(.*)$/u',$word,$p)) return $stem;
    $start = $p[1];
    $RV = $p[2];
    if(!$RV) return $stem;

	if(!$this->s_replace($RV, '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/u', ''))
	{ $this->s_replace($RV, '/(с[яь])$/u', '');
      if($this->s_replace($RV, '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/u', ''))
	  $this->s_replace($RV, '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/u', '');
      elseif(!$this->s_replace($RV, '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/u', ''))
      $this->s_replace($RV, '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/u', '');
    }

    $this->s_replace($RV, '/и$/u', '');

    if(preg_match('/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/u',$RV))
    $this->s_replace($RV, '/ость?$/u', '');

    if(!$this->s_replace($RV, '/ь$/u', ''))
	{ $this->s_replace($RV, '/ейше?/u', '');
      $this->s_replace($RV, '/нн$/u', 'н');
    }

    return $start.$RV;
  }

/**
 * Преобразует каждое слово текста в стемы.
 *
 * @param string $text Текст.
 * @return string Текст из стемов.
 */

  function getStems($text)
  {
    $words=array();
	$stems=array();
    $text=strip_tags($text);
	$text=str_replace("-"," ",$text);
	$text=preg_replace("/\&[a-z]+/iu","",$text);
	$text=preg_replace("/[^a-zA-Zа-яА-Я0-9 ]/iu","",$text);
	$swords=explode(' ',$text);
	foreach($swords as $word)
	{ $word=trim($word);
	  if(mb_strlen($word)>3)
	  { if(!isset($words[$word]))
	    { if($stem=$this->getStem($word))
		  if(mb_strlen($stem)>3)
		  $stems[$stem]=1;
		  $words[$word]=1;
		}
	  }
	}
	return implode(' ',array_keys($stems));
  }

/**
 * Очищает текст от форматирования.
 *
 * @param string $string Текст.
 * @return string Текст.
 */

  function clearText($string)
  {
    $string=strip_tags($string);
    $string=str_replace(array('&nbsp;','&quot;','&amp;','&lt;','&gt;','&laquo;','&raquo;'),array(' ','"','&','<','>','«','»'),$string);
	$string=preg_replace("/[ \t\n\r]+/iu"," ",$string);
	return $string;
  }

/**
 * Обновляет индекс элемента.
 *
 * @param integer $idsec Числовой идентификатор раздела.
 * @param integer $iditem Числовой идентификатор записи в разделе.
 * @param string $name Название.
 * @param string $content Текст.
 * @param string $tags='' Список тегов через запятую.
 */

  function updateIndex($idsec,$iditem,$name,$content,$tags='')
  {
    if(empty($this->section)) return;

	$fields=array(
	"date"=>time(),
	"idsec"=>$idsec,
	"iditem"=>$iditem,
	"name"=>$this->clearText($name),
	"stems"=>$this->getStems($name.' '.$content),
	"content"=>$this->clearText($content)
	);

	if(empty($fields['stems']))
	{ $this->deleteIndex($idsec,$iditem);
	  return;
	}

	$idtags=array();
	if(!empty($tags))
	{ $tags=explode(',',$tags);
	  foreach($tags as $i=>$tag)
	  if($tags[$i]=mb_strtolower(trim($tag)))
	  { if($idtag=A::$DB->getOne("SELECT id FROM {$this->section}_tags WHERE tag=?",$tags[$i]))
	    $idtags[]=$idtag;
		else
		$idtags[]=A::$DB->Insert($this->section.'_tags',array('tag'=>$tags[$i]));
	  }
	}
	$idtags=array_unique($idtags);
	$_idtags=A::$DB->getOne("SELECT idtags FROM {$this->section} WHERE idsec=?i AND iditem=?i",array($idsec,$iditem));
	if(!empty($_idtags))
	{ $_idtags=explode(' ',$_idtags);
	  foreach($_idtags as $i=>$idtag)
	  $_idtags[$i]=(integer)$idtag;
	}
	else
	$_idtags=array();

	foreach($idtags as $idtag)
	if(!in_array($idtag,$_idtags))
	A::$DB->execute("UPDATE {$this->section}_tags SET `count`=`count`+1 WHERE id=$idtag");

    foreach($_idtags as $idtag)
	if(!in_array($idtag,$idtags))
	A::$DB->execute("UPDATE {$this->section}_tags SET `count`=`count`-1 WHERE id=$idtag");
	A::$DB->execute("DELETE FROM {$this->section}_tags WHERE `count`<=0");

	foreach($idtags as $i=>$idtag)
	$idtags[$i]=sprintf("%04d",$idtag);

	$fields['idtags']=implode(' ',$idtags);

	A::$DB->Replace($this->section,$fields,"idsec=?i AND iditem=?i",array($idsec,$iditem));
  }

/**
 * Удаляет элемента из поиска.
 *
 * @param integer $idsec Числовой идентификатор раздела.
 * @param integer $iditem Числовой идентификатор записи в разделе.
 */

  function deleteIndex($idsec,$iditem)
  {
    if(empty($this->section)) return;

	$_idtags=A::$DB->getOne("SELECT idtags FROM $this->section WHERE idsec=?i AND iditem=?i",array($idsec,$iditem));
	if(!empty($_idtags))
	{ $_idtags=explode(' ',$_idtags);
	  foreach($_idtags as $i=>$idtag)
	  $_idtags[$i]=(integer)$idtag;
	}
	else
	$_idtags=array();

	foreach($_idtags as $idtag)
	A::$DB->execute("UPDATE {$this->section}_tags SET `count`=`count`-1 WHERE id=$idtag");

	A::$DB->execute("DELETE FROM {$this->section}_tags WHERE `count`<=0");
	A::$DB->execute("DELETE FROM `{$this->section}` WHERE idsec=? AND iditem=?",array($idsec,$iditem));
  }

/**
 * Удаляет все элементы раздела из поиска.
 *
 * @param integer $idsec Числовой идентификатор раздела.
 */

  function deleteSection($idsec)
  {
    if(empty($this->section)) return;

	$__idtags=A::$DB->getCol("SELECT idtags FROM `{$this->section}` WHERE idsec=".(integer)$idsec);

	foreach($__idtags as $_idtags)
	{ if(!empty($_idtags))
	  { $_idtags=explode(' ',$_idtags);
	    foreach($_idtags as $i=>$idtag)
	    $_idtags[$i]=(integer)$idtag;
	  }
	  else
	  $_idtags=array();

	  foreach($_idtags as $idtag)
	  A::$DB->execute("UPDATE {$this->section}_tags SET `count`=`count`-1 WHERE id=$idtag");
	}

	A::$DB->execute("DELETE FROM {$this->section}_tags WHERE `count`<=0");
	A::$DB->execute("DELETE FROM `{$this->section}` WHERE idsec=".(integer)$idsec);
  }

/**
 * Формирует фрагмент текста с найденными словами.
 *
 * @param array $words Список слов.
 * @param array $stems Список стемов.
 * @param string &$text Исходный текст.
 * @return string Фрагмент.
 */

  function getFindedText($words,$stems,&$text)
  {
    if(empty($this->section)) return "";

    $blocks=array();
	$desc=truncate($text,350);
	$text=explode(" ",$text);
	$count=count($text);
	for($i=0;$i<$count;$i++)
	{ $text[$i]=mb_strtolower($text[$i]);
	  foreach($stems as $stem)
	  if(isset($text[$i]) && mb_strpos($text[$i],$stem)!==false)
	  { $text[$i]="<b class=\"selectedword\">{$text[$i]}</b>";
	    $b=($i-10)>=0?$i-10:0;
		$blocks[]=implode(" ",array_slice($text,$b,20));
		if(count($blocks)==3)
		return "... ".implode(" ... ",$blocks)." ...";
		else
		$i+=10;
	  }
	  foreach($words as $word)
	  if(isset($text[$i]) && mb_strpos($text[$i],$word)!==false)
	  { $text[$i]="<b class=\"selectedword\">{$text[$i]}</b>";
	    $b=($i-10)>=0?$i-10:0;
		$blocks[]=implode(" ",array_slice($text,$b,20));
		if(count($blocks)==3)
		return "... ".implode(" ... ",$blocks)." ...";
		else
		$i+=10;
	  }
	}
	return !empty($blocks)?"... ".implode(" ... ",$blocks)." ...":$desc;
  }

/**
 * Возвращает массив разделов участвующих в поиске.
 *
 * @return array Ассоциированный массив: числовой идентификатор => Название.
 */

  function getSections()
  {
    if(empty($this->section)) return array();

    $sections=array();
	A::$DB->query("
	SELECT s.*,COUNT(i.id) AS count,MAX(i.date) AS date
	FROM ".getDomain($this->section)."_sections AS s
	LEFT JOIN {$this->section} AS i ON i.idsec=s.id
	WHERE s.active='Y'
	GROUP BY s.id,i.idsec
	ORDER BY s.sort");
	while($row=A::$DB->fetchRow())
	if($row['count']>0)
	$sections[$row['id']]=$row['caption'];
	A::$DB->free();
	return $sections;
  }

/**
 * Возвращает массив записей с тегами-ссылками по их числовым идентификаторам.
 *
 * @param string $idtags Строка с числовыми идентификаторами тегов через пробел.
 * @return array Массив записей с тегами-ссылками.
 */

  function getTags($idtags)
  {
    if(empty($idtags) || empty($this->section)) return array();
	$idtags=explode(' ',$idtags);
	$tags=array();
	foreach($idtags as $idtag)
	if($tag=A::$DB->getOne("SELECT tag FROM {$this->section}_tags WHERE id=".(integer)$idtag))
	{ $tag=array('name'=>$tag);
	  if(A_MODE==1)
	  $tag['link']="admin.php?mode=sections&item={$this->section}&tag=".urlencode($tag['name']);
	  else
	  $tag['link']=getSectionLink($this->section).'?tag='.urlencode($tag['name']);
	  $tags[]=$tag;
	}
	return $tags;
  }

/**
 * Возвращает массив записей с тегами-ссылками по их названиям.
 *
 * @param string $tags Строка с названиями тегов через запятую.
 * @return array Массив записей с тегами-ссылками.
 */

  function convertTags($tags)
  {
    if(empty($tags) || empty($this->section)) return array();
	$stags=explode(',',$tags);
	$tags=array();
	foreach($stags as $tag)
	{ $tag=array('name'=>trim($tag));
	  if(A_MODE==1)
	  $tag['link']="admin.php?mode=sections&item={$this->section}&tag=".urlencode($tag['name']);
	  else
	  $tag['link']=getSectionLink($this->section).'?tag='.urlencode($tag['name']);
	  $tags[]=$tag;
	}
	return $tags;
  }

/**
 * Формирует облако тегов.
 *
 * @param integer $tagsNum=50 Кличество тегов в облаке.
 * @param integer $clustersNum=10 Количество кластеров.
 * @param integer $idsec=0 Числовой идентификатор раздела (0 - все).
 * @return array Массив записей с тегами-ссылками.
 */

  function getCloudTags($tagsNum=50,$clustersNum=10,$idsec=0)
  {
    if(empty($this->section))
	return array();

	$this->tags=array();
	if($min=A::$DB->getOne("SELECT MAX(`count`) FROM {$this->section}_tags"))
	{
      if($idsec>0)
      { $idsec=(integer)$idsec;
        $idtags=array();
	    $_idtags=A::$DB->getCol("SELECT idtags FROM {$this->section} WHERE idsec=$idsec AND NOT ISNULL(idtags) AND idtags<>''");
	    foreach($_idtags as $_idtag)
	    { $_idtag=explode(" ",$_idtag);
	      foreach($_idtag as $idtag)
	      { $idtag=(integer)$idtag;
	        $idtags[$idtag]=1;
	      }
	    }
	    if($idtags=array_keys($idtags))
	    $where="WHERE id IN(".implode(",",$idtags).") ";
	    else
	    $where="WHERE id=0 ";
      }
      else
      $where="";

	  $max=0;
	  if($tagsNum>0)
	  A::$DB->queryLimit("SELECT tag,`count` FROM {$this->section}_tags {$where}ORDER BY `count` DESC",0,$tagsNum);
	  else
	  A::$DB->query("SELECT tag,`count` FROM {$this->section}_tags {$where}ORDER BY `count` DESC");

	  while($row=A::$DB->fetchRow())
	  { if(A_MODE==1)
	    $row['link']='admin.php?mode='.MODE.'&item='.ITEM.'&tag='.urlencode($row['tag']);
		else
		$row['link']=getSectionLink($this->section).'?tag='.urlencode($row['tag']);
	    if($row['count']>$max)
		$max=$row['count'];
		if($row['count']<$min)
		$min=$row['count'];
	    $this->tags[]=$row;
	  }
	  A::$DB->free();

	  $step = ($max - $min) / ($clustersNum - 1);
	  $this->centroids = array();
	  for ($i = 0; $i < $clustersNum; $i++)
	  $this->centroids[$i] = $min + $i*$step;

      $prevClusters = array();
	  while(1)
	  { $distances = $this->_getDistances();
		$this->clusters = $this->_clasterizeTags($distances);
		if ($prevClusters === $this->clusters)
		break;
		else
		{ $prevClusters = $this->clusters;
		  $this->centroids = $this->_recalcCentroids();
		}
	  }

	  foreach ($this->clusters as $cIndex => $cluster)
	  { $cur=0;
		foreach ($cluster as $tIndex=>$tag)
		if ($tag===1)
		{ $this->tags[$tIndex]['cluster']=$cIndex;
		  $cur++;
		}
	  }
	}

	return array_multisort_key($this->tags,'tag',SORT_ASC,'strcmp');
  }

  private function _getDistances()
  {
    $distancies = array();
	foreach ($this->centroids as $i => $centroid)
	foreach ($this->tags as $j => $tag)
	$distancies[$i][$j] = abs($centroid - $tag['count']);
	return $distancies;
  }

  private function _clasterizeTags($distances)
  {
	$clasters = array();
	$rowsNum = count($distances);
	$colsNum = count($distances[0]);
	for ($j = 0; $j < $colsNum; $j++)
	{ $min = $distances[0][$j];
	  $minIndex = 0;
	  for ($i = 0; $i < $rowsNum; $i++)
	  {	$clasters[$i][$j] = 0;
		if ($distances[$i][$j] < $min)
		$minIndex = $i;
	  }
	  $clasters[$minIndex][$j] = 1;
	}
	return $clasters;
  }

  private function _recalcCentroids()
  {
	$newCentroids = array();
	for ($centrIndex = 0; $centrIndex < count($this->centroids); $centrIndex++)
	{
	  $newCentroids[$centrIndex] = 0;
	  $n = 0;
	  foreach ($this->clusters[$centrIndex] as $key => $val)
	  { if ($val === 1)
	    { $newCentroids[$centrIndex] += $this->tags[$key]['count'];
		  $n++;
		}
	  }
	  if ($n > 0)
	  $newCentroids[$centrIndex] /= $n;
	  else
	  $newCentroids[$centrIndex] = $this->centroids[$centrIndex];
	}
	return $newCentroids;
  }
}