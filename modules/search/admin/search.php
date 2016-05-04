<?php
/**************************************************************************/
/* A.CMS: content managment system
/* Copyright 2009 Astra WebTechnology
/* author Vitaly Hohlov <vitaly.hohlov@gmail.com>
/* http://a-cms.ru
/**************************************************************************/

/**
 * Панель управления модуля "Поиск по сайту".
 *
 * <a href="http://wiki.a-cms.ru/modules/search">Руководство</a>.
 */

class SearchModule_Admin extends A_MainFrame
{
/**
 * Конструктор.
 */

  function __construct()
  {
    parent::__construct("module_search.tpl");

	$this->AddJScript("/modules/search/admin/search.js");
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "indexall": $res=$this->indexAll(); break;
	}
    if(!empty($res))
	A::goUrl("admin.php?mode=sections&item=".SECTION);
  }

/**
 * Обработчик действия: Переиндексация разделов в базу поиска.
 */

  function indexAll()
  {
    @set_time_limit(0);

	A::$DB->caching=false;

	if(!empty($_REQUEST['sections']))
	foreach($_REQUEST['sections'] as $id)
	{ $section=getSectionById($id);
	  $module=getModuleBySection($section);
	  if(function_exists($module.'_searchIndexAll'))
	  { A_SearchEngine::getInstance()->deleteSection($id);
	    call_user_func($module.'_searchIndexAll',$section);
	  }
	}

	A::$CACHE->resetSection(SECTION);

	return true;
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
      { $pager->query("
	    SELECT `date`,`idsec`,`iditem`,`name`,`content`,`idtags`,
	    MATCH(`name`) AGAINST('{$_words}')+MATCH(`content`) AGAINST('{$_words}')+MATCH(`stems`) AGAINST('{$_stems}') AS relevant
	    FROM ".SECTION."
	    WHERE ".(!empty($idsec)?"(idsec=".implode(" OR idsec=",$idsec).") AND ":"")."
	    ( MATCH(`name`) AGAINST ('{$_words}' IN BOOLEAN MODE)
	      OR
		  MATCH(`content`) AGAINST ('{$_words}' IN BOOLEAN MODE)
		  OR
		  MATCH(`stems`) AGAINST ('{$_stems}' IN BOOLEAN MODE)
	    )
	    ORDER BY `relevant` DESC, `date` DESC");
	    while($row=$pager->fetchRow())
	    { $row['num']=$pager->begin+count($items);
		  $row['description'] = A_SearchEngine::getInstance()->getFindedText($words,$stems,$row['content']);
	      if($srow = A::$DB->getRowById($row['idsec'],DOMAIN."_sections"))
	      { $section=DOMAIN.'_'.$srow['lang'].'_'.$srow['name'];
	        if(!empty($row['name']) && !in_array($srow['module'],$imodules))
	        { if(!empty($srow['caption_'.LANG]))
		      $row['name']=$srow['caption_'.LANG]." - ".$row['name'];
		    }
		    else
		    $row['name']=$srow['caption_'.LANG];
			if(function_exists($srow['module'].'_createItemLink'))
			$row['link']=call_user_func($srow['module'].'_createItemLink',$row['iditem'],$section);
			else
			$row['link']=getSectionLink($section);
			$row['idimg']=A::$DB->getOne("SELECT id FROM ".DOMAIN."_images WHERE idsec={$row['idsec']} AND iditem={$row['iditem']} ORDER BY sort LIMIT 0,1");
			$row['tags']=A_SearchEngine::getInstance()->getTags($row['idtags']);
		    $items[]=$row;
	      }
	    }
		$pager->free();
	  }
	}
	elseif(!empty($_GET['tag']))
	{ if($idtag=A::$DB->getOne("SELECT id FROM ".SECTION."_tags WHERE tag=?",mb_strtolower(trim($_GET['tag']))))
	  { $idtag=sprintf("%04d",$idtag);
	    $pager->query("
	    SELECT `date`,`idsec`,`iditem`,`name`,`content`,`idtags`
	    FROM ".SECTION."
	    WHERE ".(!empty($idsec)?"(idsec=".implode(" OR idsec=",$idsec).") AND ":"")."
	    MATCH(`idtags`) AGAINST ('{$idtag}' IN BOOLEAN MODE)
	    ORDER BY `date` DESC");
	    while($row=$pager->fetchRow())
	    { $row['num']=$pager->begin+count($items);
		  $row['description'] = truncate($row['content'],350);
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
			$row['idimg']=A::$DB->getOne("SELECT id FROM ".DOMAIN."_images WHERE idsec={$row['idsec']} AND iditem={$row['iditem']} ORDER BY sort LIMIT 0,1");
			$row['tags']=A_SearchEngine::getInstance()->getTags($row['idtags']);
		    $items[]=$row;
	      }
	    }
		$pager->free();
	  }
	}

	$this->Assign("items",$items);
	$this->Assign("items_pager",$pager);

	$this->Assign("indexall",A::$DB->getOne("SELECT COUNT(*) FROM ".SECTION));
	$this->Assign("indexdate",A::$DB->getOne("SELECT MAX(date) FROM ".SECTION));
	$this->Assign("sections",A_SearchEngine::getInstance()->getSections());
	$this->Assign('tags',A_SearchEngine::getInstance()->getCloudTags());

	$this->Assign("optbox",new A_OptionsBox("",array("idgroup"=>1)));
  }
}

A::$MAINFRAME = new SearchModule_Admin;