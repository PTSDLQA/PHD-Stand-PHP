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
 * Модуль "Поиск по сайту".
 *
 * <a href="http://wiki.a-cms.ru/modules/search">Руководство</a>.
 */

class SearchModule extends A_MainFrame
{
/**
 * Маршрутизатор URL.
 *
 * @param array $uri Элементы полного пути URL.
 */

  function Router($uri)
  {
	if(count($uri)==0)
	$this->page="result";
	else
	A::NotFound();
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$items=array();
    $pager = new A_Pager(A::$OPTIONS['rows']);

	if(!empty($_GET['idsec']))
    { if(is_array($_GET['idsec']))
	  { foreach($_GET['idsec'] as $i=>$id)
	    $idsec[$i]=(integer)$id;
	  }
	  else
	  $idsec=array((integer)$_GET['idsec']);
	}

	$imodules=array('feedback');

	if(!empty($_GET['query']))
	{
	  $_GET['query']=mb_substr($_GET['query'],0,50);

	  $stems=$_stems=array();
	  $words=$_words=array();

	  $query=explode(" ",$_GET['query']);
	  foreach($query as $word)
	  if(mb_strlen($word)>2)
	  { $word=mb_strtolower($word);
	    $words[]=$word;
	    $_words[]=empty($_words)?"+>$word":">$word";
	    if($stem=A_SearchEngine::getInstance()->getStem($word))
		if(mb_strlen($stem)>3)
	    { $stems[]=$stem;
		  $_stems[]=empty($_stems)?"+<$stem":"<$stem";
		}
	  }

	  $_words=A::$DB->real_escape_string(implode(" ",$_words));
	  $_stems=A::$DB->real_escape_string(implode(" ",$_stems));

	  if(!empty($_words))
      {
	    $pager->query("
	    SELECT `date`,`idsec`,`iditem`,`name`,`content`,`idtags`,
	    MATCH(`name`) AGAINST('{$_words}')+MATCH(`content`) AGAINST('{$_words}')+MATCH(`stems`) AGAINST('{$_stems}') AS relevant
	    FROM ".SECTION."
	    WHERE ".(!empty($idsec)?"idsec IN(".implode(",",$idsec).") AND ":"")."
	    ( MATCH(`name`) AGAINST ('{$_words}' IN BOOLEAN MODE)
	      OR
		  MATCH(`content`) AGAINST ('{$_words}' IN BOOLEAN MODE)
		  OR
		  MATCH(`stems`) AGAINST ('{$_stems}' IN BOOLEAN MODE)
	    )
	    ORDER BY `relevant` DESC, `date` DESC");
	    while($row=$pager->fetchRow())
	    { $row['num']=$pager->begin+count($items);
		  $row['description']=A_SearchEngine::getInstance()->getFindedText($words,$stems,$row['content']);
	      if($srow = A::$DB->getRowById($row['idsec'],DOMAIN."_sections"))
	      { $section=DOMAIN.'_'.$srow['lang'].'_'.$srow['name'];
	        if(!empty($row['name']) && !in_array($srow['module'],$imodules))
	        { if(!empty($srow['caption_'.LANG]))
		      $row['name']=$srow['caption_'.LANG]." - ".$row['name'];
		    }
		    else
		    $row['name']=$srow['caption_'.LANG];
			if($row['iditem']>0)
			$row['link']=function_exists($srow['module'].'_createItemLink')?call_user_func($srow['module'].'_createItemLink',$row['iditem'],$section):getSectionLink($section);
			else
			$row['link']=function_exists($srow['module'].'_createCategoryLink')?call_user_func($srow['module'].'_createCategoryLink',-$row['iditem'],$section):getSectionLink($section);
			$row['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array($row['idsec'],$row['iditem']));
	        $row['idimg']=isset($row['images'][0]['id'])?$row['images'][0]['id']:0;
			$row['tags']=A_SearchEngine::getInstance()->getTags($row['idtags']);
		    $items[]=$row;
	      }
	    }
		$pager->free();
	  }
	}
	elseif(!empty($_GET['tag']))
	{
	  if($_GET['tag']=mb_substr(mb_strtolower(trim($_GET['tag'])),0,50))
	  if($idtag=A::$DB->getOne("SELECT id FROM ".SECTION."_tags WHERE tag=?",$_GET['tag']))
	  { $idtag=sprintf("%04d",$idtag);
	    $pager->query("
	    SELECT `date`,`idsec`,`iditem`,`name`,`content`,`idtags`
	    FROM ".SECTION."
	    WHERE ".(!empty($idsec)?"idsec IN(".implode(",",$idsec).") AND ":"")."
	    MATCH(`idtags`) AGAINST ('{$idtag}' IN BOOLEAN MODE)
	    ORDER BY `date` DESC");
	    while($row=$pager->fetchRow())
	    { $row['num']=$pager->begin+count($items);
		  $row['description']=truncate($row['content'],350);
	      if($srow = A::$DB->getRowById($row['idsec'],DOMAIN."_sections"))
	      { $section=DOMAIN.'_'.$srow['lang'].'_'.$srow['name'];
	        if(!empty($row['name']) && !in_array($srow['module'],$imodules))
	        { if(!empty($srow['caption_'.LANG]))
		      $row['name']=$srow['caption_'.LANG]." - ".$row['name'];
		    }
		    else
		    $row['name']=$srow['caption_'.LANG];
			if($row['iditem']>0)
			$row['link']=function_exists($srow['module'].'_createItemLink')?call_user_func($srow['module'].'_createItemLink',$row['iditem'],$section):getSectionLink($section);
			else
			$row['link']=function_exists($srow['module'].'_createCategoryLink')?call_user_func($srow['module'].'_createCategoryLink',-$row['iditem'],$section):getSectionLink($section);
			$row['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array($row['idsec'],$row['iditem']));
	        $row['idimg']=isset($row['images'][0]['id'])?$row['images'][0]['id']:0;
			$row['tags']=A_SearchEngine::getInstance()->getTags($row['idtags']);
		    $items[]=$row;
	      }
	    }
		$pager->free();
	  }
	}

	$this->Assign("items",$items);
	$this->Assign("items_pager",$pager);

	$this->Assign("sections",A_SearchEngine::getInstance()->getSections());

	$this->AddNavigation(SECTION_NAME);
  }
}

A::$MAINFRAME = new SearchModule;