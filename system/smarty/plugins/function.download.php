<?php
/**************************************************************************/
/* Smarty plugin
/* @copyright 2011 "Астра Вебтехнологии"
/* @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
/* @link http://a-cms.ru
 * @package Smarty
 * @subpackage plugins
/**************************************************************************/

function smarty_function_download($params, &$smarty)
{
  if(!empty($params['id']))
  {
    if($row=A::$DB->getRowById($params['id'],"mysite_files"))
    {
      $caption=$row['name'];
	  $title="Скачать";

	  if(A_MODE==0)
	  $link="/getfile/".$row['id']."/".$row['name'];
	  else
	  $link.="/download.php?id=".$row['id'];

      $attr="";
      foreach($params as $_key => $_val)
      switch($_key)
      { case "data":
        case "separator":
          break;
	    case "caption":
	      $caption=$_val;
	      break;
	    case "title":
	      $title=$_val;
	      break;
	    case "size":
	      $caption.="&nbsp;(".sizestring($row['size']).")";
		  break;
	    case "dwnl":
	      $caption.="&nbsp;[".$row['dwnl']."]";
		  break;
	    default:
	      $attr.=" $_key='$_val'";
      }

	  return "<a href=\"$link\" title=\"$title\"$attr>$caption</a>";
    }
  }
  elseif(!empty($params['data']) && is_array($params['data']))
  {
    $result=array();
    $separator=!empty($params['separator'])?$params['separator']:", ";
    $data=!empty($params['max'])?array_slice($params['data'],0,(integer)$params['max']):$params['data'];

    foreach($data as $row)
    {
      $caption=$row['name'];
	  $title="Скачать";

	  if(A_MODE==0)
	  $link="/getfile/".$row['id']."/".$row['name'];
	  else
	  $link.="/download.php?id=".$row['id'];

      $attr="";
      foreach($params as $_key => $_val)
      switch($_key)
      { case "data":
        case "separator":
        case "max":
          break;
	    case "caption":
	      $caption=$_val;
	      break;
	    case "title":
	      $title=$_val;
	      break;
	    case "size":
	      $caption.="&nbsp;(".(is_numeric($row['size'])?sizestring($row['size']):$row['size']).")";
		  break;
	    case "dwnl":
	      $caption.="&nbsp;[".$row['dwnl']."]";
		  break;
	    default:
	      $attr.=" $_key='$_val'";
      }

      $result[]="<a href=\"$link\" title=\"$title\"$attr>$caption</a>";
    }

    if(count($data)<count($params['data']))
    $result[]="...";

    return implode($separator,$result);
  }
  else
  return "";
}