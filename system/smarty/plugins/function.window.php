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

function smarty_function_window($params, &$smarty)
{
  $idwin="window";
  $width=$height=0;
  $center='true';
  $caption='';

  foreach($params as $_key => $_val)
  switch($_key)
  { case "url": $url=$_val; break;
    case "content": $content=$_val; break;
	case "template": $template=$_val; break;
    case "idimg":
	  if($row=A::$DB->getRow("SELECT path,width,height FROM mysite_images WHERE id=".(integer)$_val))
      { $url="/".preg_replace("/^\//i","",$row['path']);
	    $width=$row['width'];
	    $height=$row['height'];
      }
	  break;
    case "width": $width=$_val; break;
	case "height": $height=$_val; break;
	case "idwin": $idwin=$_val; break;
	case "center": $center=$_val?'true':'false'; break;
	case "caption": $caption=$_val;
  }

  if(!empty($url) && $width>0 && $height>0)
  { if(!empty($params['idimg']))
    return "javascript:open_imgwindow('$url','$caption',$width,$height)";
    else
    return "javascript:open_window('$url',$width,$height,'$idwin',$center)";
  }
  else
  return "";
}