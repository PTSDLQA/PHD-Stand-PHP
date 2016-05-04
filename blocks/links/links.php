<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Blocks
 */
/**************************************************************************/

/**
 * Блок "Список произвольных ссылок".
 *
 * <a href="http://wiki.a-cms.ru/blocks/links">Руководство</a>.
 */

class links_Block extends A_Block
{
/**
 * Специальная обработка параметров блока.
 *
 * @return array
 */

  function prepareParams()
  {
    $params=array();
	if(isset($_REQUEST['b_count']) && $_REQUEST['b_count']>=0)
	{ for($i=0;$i<=$_REQUEST['b_count'];$i++)
	  if(!empty($_REQUEST["b_caption$i"]) && isset($_REQUEST["b_link$i"]))
	  { $caption=!empty($_REQUEST["b_caption$i"])?self::escape($_REQUEST["b_caption$i"]):"";
		$link=!empty($_REQUEST["b_link$i"])?self::escape($_REQUEST["b_link$i"]):"";
		$section=!empty($_REQUEST["b_section$i"])?self::escape($_REQUEST["b_section$i"]):"";
		$id=!empty($_REQUEST["b_id$i"])?self::escape($_REQUEST["b_id$i"]):"";
	    $params[$i]="array(\"id\"=>\"$id\",\"name\"=>\"$caption\",\"link\"=>\"$link\",\"section\"=>\"$section\",\"sublinks\"=>array(\$sublinks))";
	  }
	}

	if(isset($_FILES['xlsfile']['tmp_name']) && file_exists($_FILES['xlsfile']['tmp_name']))
    { $path_parts=pathinfo($_FILES['xlsfile']['name']);
	  $ext=preg_replace("/[^a-z0-9]+/i","",mb_strtolower($path_parts['extension']));
	  if($ext=='xls')
	  { $i=0;
		$excel = new A_ExcelReader($_FILES['xlsfile']['tmp_name']);
		foreach($excel as $row)
        { $caption=!empty($row[0])?self::escape($row[0]):"";
		  $link=!empty($row[1])?self::escape($row[1]):"";
		  $id=!empty($row[2])?self::escape($row[2]):"";
		  $section=!empty($row[3])?self::escape($row[3]):"";
		  if(!empty($caption) || !empty($link))
	      $params[$i++]="array(\"id\"=>\"$id\",\"name\"=>\"$caption\",\"link\"=>\"$link\",\"section\"=>\"$section\",\"sublinks\"=>array(\$sublinks))";
	    }
		$_REQUEST['b_count']+=count($excel);
	  }
	}

	$param="";
	$sublinks="";
	for($i=0;$i<=$_REQUEST['b_count'];$i++)
	if(isset($params[$i]))
	{ $sub=$i>0 && !empty($_REQUEST['b_sub']) && in_array($i,$_REQUEST['b_sub']);
	  if($sub)
	  { if(!empty($sublinks)) $sublinks.=",";
	    $sublinks.=$params[$i];
	  }
	  else
	  { if(isset($ii))
	    { eval("\$params[\$ii]=\"".addcslashes($params[$ii],"\"")."\";");
		  if(!empty($param)) $param.=",";
		  $param.=$params[$ii];
		}
	    $sublinks="";
	    $ii=$i;
	  }
	}
	if(isset($ii) && isset($params[$ii]))
    { eval("\$params[\$ii]=\"".addcslashes($params[$ii],"\"")."\";");
	  if(!empty($param)) $param.=",";
	  $param.=$params[$ii];
	}
	$sublinks="";
	eval("\$param=\"".addcslashes($param,"\"")."\";");
	eval("\$links=array($param);");

	return array('template'=>$_REQUEST['b_template'],'links'=>$links);
  }

  private function escape($string)
  {
    return preg_replace("/[\"';]+/i","",strip_tags($string));
  }

  private function linkcmp($link)
  { static $cur=null;
    if(strpos($link,"http://")===0)
    return false;
    if(is_null($cur))
    { $puri=parse_url(urldecode(getenv('REQUEST_URI')));
      preg_match_all("/\/([a-zA-Zа-яА-Я0-9._-]+)/iu",$puri['path'],$matches);
      $cur=$matches[1];
    }
    $puri=parse_url($link);
    preg_match_all("/\/([a-zA-Zа-яА-Я0-9._-]+)/iu",$puri['path'],$matches);
    $link=$matches[1];
    $_cur=count($cur);
    $_link=count($link);
    if($_link>$_cur)
    return false;
    elseif($_link==0)
    return $_cur==0;
	foreach($cur as $i=>$param)
	if(isset($link[$i]) && $link[$i]!=$param)
	return false;
	return true;
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
    foreach($this->params['links'] as $i=>$link)
	{ $this->params['links'][$i]['selected'] = $this->linkcmp($this->params['links'][$i]['link']);
	  foreach($this->params['links'][$i]['sublinks'] as $j=>$sublink)
	  $this->params['links'][$i]['sublinks'][$j]['selected'] = $this->linkcmp($this->params['links'][$i]['sublinks'][$j]['link']);
	}

	$this->Assign_by_ref("links",$this->params['links']);
  }
}