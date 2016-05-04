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

function smarty_function_image($params, &$smarty)
{
  require_once $smarty->_get_plugin_filepath('modifier','curlang');

  $src="";
  $width=0;
  $height=0;
  $bevel=A_MODE==1?4:0;
  $attr="";

  if(!empty($params['data']) && is_array($params['data']))
  { if(array_key_exists('path',$params['data']))
    { $row=$params['data'];
      unset($params['data']);
    }
    elseif(isset($params['data'][0]) && array_key_exists('path',$params['data'][0]))
	$row=array_shift($params['data']);

	if(!empty($row['path']))
	$src=$row['path'];
  }
  elseif(!empty($params['id']))
  { if($row=A::$DB->getRowById($params['id'],"mysite_images"))
    $src=$row['path'];
  }

  $alt=!empty($row['caption'])?$row['caption']:"";

  foreach($params as $_key => $_val)
  switch($_key)
  { case "id":
	case "data":
    case "domain":
    case "lightbox":
    case "popup":
    case "noimgheight":
    case "empty":
	  break;
    case "src":
      $src=$_val;
	  break;
    case "width":
	  $width=$_val;
	  if(!isset($row['width']))
	  $attr.=" $_key=\"$width\"";
	  elseif($width<=$row['width'])
	  { if(!empty($scale))
	    { $_width=$row['width']/$scale;
	      if($_width<$width)
	      $width=(integer)$_width;
		  $attr.=" $_key=\"$width\"";
		  $width=0;
	    }
	    else
	    { $scale=$row['width']/$width;
	      $attr.=" $_key=\"$width\"";
	    }
	  }
	  elseif($width>$row['width'] && !empty($scale))
	  { $_width=$row['width']/$scale;
	    if($_width<$width)
	    $width=(integer)$_width;
		$attr.=" $_key=\"$width\"";
		$width=0;
	  }
	  break;
	case "height":
	  $height=$_val;
	  if(!isset($row['height']))
	  $attr.=" $_key=\"$height\"";
	  elseif($height<=$row['height'])
	  { if(!empty($scale))
	    { $_height=$row['height']/$scale;
	      if($_height<$height)
	      $height=(integer)$_height;
		  $attr.=" $_key=\"$height\"";
	    }
	    elseif($row['height']>$row['width'])
	    { $scale=$row['height']/$height;
		  $attr.=" $_key=\"$height\"";
		}
	  }
	  break;
	case "bevel":
	  $bevel=(integer)$_val;
	  break;
	case "imgid":
      $attr.=" id=\"$_val\"";
	  break;
	case "alt":
      $alt=$_val;
	  break;
	default:
	  $attr.=" $_key=\"$_val\"";
  }

  $attr.=" alt=\"".htmlspecialchars($alt)."\"";

  if(!empty($src))
  $src=preg_replace("/^\//i","",$src);
  elseif(!empty($params['empty']))
  { $src="templates/mysite/images/".preg_replace("/[.]{2..}/i","",$params['empty']);
    if(is_file($src))
    return "<img src=\"/$src\"$attr/>";
  }
  else
  return "";

  if(($width>0 && (!isset($row['width']) || $width<$row['width'])) || ($height>0 && (!isset($row['height']) || $height<$row['height'])))
  { $path=pathinfo($src);
    if(preg_match("/^[.\/]*(files|images)\/([a-zA-Z0-9-_]+)\//i",$src,$matches))
	$cachename='cache/images/'.$matches[2].'/'.$path['filename'];
	else
	$cachename='cache/images/'.$path['filename'];
	if($width>0)
	$cachename.='_w'.$width;
	if($height>0)
	$cachename.='_h'.$height;
	if($bevel>0)
	$cachename.='_b'.$bevel;
	$cachename.='.'.mb_strtolower($path['extension']);
	if(!is_file($cachename) && is_file($src))
	{ require_once("Image/Transform.php");
	  $it = Image_Transform::factory('GD');
      $it->load($src);
	  if($width>0 && $height>0 && ($it->img_x>$width || $it->img_y>$height))
      { if($width<0 || $width>800 || $height<0 || $height>800)
        { $it->display();
          exit();
        }
        if($it->img_x>$width)
	    $it->scaleByX($width);
	    if($it->new_y>$height)
	    $it->crop($width,$height,0,0);
	  }
	  elseif($width>0 && $it->img_x>$width)
	  $it->scaleByX($width);
	  elseif($height>0 && $it->img_y>$height)
	  $it->scaleByY((integer)$height);
	  if(!empty($matches[2]))
      { if(!is_dir('cache/images/'.$matches[2]))
	    mkdir('cache/images/'.$matches[2]);
	  }
	  $it->save($cachename,'',100);
	  if($bevel>0 && $bevel>0 && $bevel<=5)
	  { require_once("Image/Tools.php");
        $border = Image_Tools::factory('border');
        $border->set('image',$cachename);
        $border->set('style','bevel');
        $border->set('params',array($bevel,'#ffffff', '#000000'));
        switch($it->type)
        { case 'jpg':
          case 'jpeg': $border->save($cachename,IMAGETYPE_JPEG); break;
          case 'gif': $border->save($cachename,IMAGETYPE_GIF); break;
          case 'png': $border->save($cachename,IMAGETYPE_PNG); break;
	    }
	  }
	}
	if(is_file($cachename))
	$image="<img src=\"/$cachename\"$attr/>";
	else
    $image="<img src=\"/image.php?src=".urlencode($src)."&x=$width&y=$height&b=$bevel\"$attr/>";
  }
  elseif(isset($row))
  { $attr=str_replace(" width=\"{$row['width']}\"","",$attr);
    $attr=str_replace(" height=\"{$row['height']}\"","",$attr);
	$image="<img src=\"/$src\" width=\"{$row['width']}\" height=\"{$row['height']}\"{$attr}/>";
	$params['popup']=false;
	if(empty($params['data']))
    $params['lightbox']=false;
  }
  else
  { $image="<img src=\"/$src\"$attr/>";
    $params['popup']=false;
    if(empty($params['data']))
    $params['lightbox']=false;
  }
  if(!empty($params['popup']) && !empty($row))
  { if($params['popup']===true)
    $params['popup']="Увеличить";
	$caption=!empty($params['alt'])?$params['alt']:$row['caption'];
	if(A_MODE==A_MODE_FRONT && is_file($css="templates/mysite/imagewin.css"))
    $image="<a href=\"javascript:open_imgwindow('/{$src}','{$caption}',{$row['width']},{$row['height']},'/{$css}')\" title=\"{$params['popup']}\">$image</a>";
	else
    $image="<a href=\"javascript:open_imgwindow('/{$src}','{$caption}',{$row['width']},{$row['height']})\" title=\"{$params['popup']}\">$image</a>";
  }
  elseif(!empty($params['lightbox']) && !empty($row))
  { $caption=!empty($params['alt'])?$params['alt']:$row['caption'];
    $caption=smarty_modifier_curlang($caption);
	$group=!empty($params['group'])?"[{$params['group']}]":"[default]";
    $image="<a href=\"/{$src}\" rel=\"lightbox{$group}\" title=\"{$caption}\">$image</a>";
	if(!empty($params['data']))
	while($row=array_shift($params['data']))
	{ $row['caption']=smarty_modifier_curlang($row['caption']);
	  $image.="<a href=\"/{$row['path']}\" rel=\"lightbox{$group}\" title=\"{$row['caption']}\" style=\"display:none\"></a>";
	}
  }

  return $image;
}