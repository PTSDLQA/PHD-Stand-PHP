<?php
/** \file image.php
 * Масштабирование изображения.
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

ini_set("error_reporting",0);

if(is_file($src=preg_replace("/[.]{2,}/i","",$_GET['src'])))
{
  $root=realpath('.') ;
  $src=realpath($src);

  if(strpos($src,$root)===0)
    $src=substr($src,strlen($root)+1);
  else
    die();

  if(DIRECTORY_SEPARATOR=='/')
    $preg='/^(files|images)\/([a-zA-Z0-9-_]+)\//i';
  else
    $preg='/^(files|images)\\\([a-zA-Z0-9-_]+)\\\/i';

  $path=pathinfo($src);
  $ext=strtolower($path['extension']);
  if(preg_match($preg,$src,$matches))
    $cachename='cache/images/'.$matches[2].'/'.$path['filename'];
  else
    $cachename='cache/images/'.$path['filename'];
  if(!empty($_GET['x']))
    $cachename.='_w'.(integer)$_GET['x'];
  if(!empty($_GET['y']))
    $cachename.='_h'.(integer)$_GET['y'];
  if(!empty($_GET['b']))
    $cachename.='_b'.(integer)$_GET['b'];
  $cachename.='.'.$ext;

  if(is_file($cachename)) {
    $mime=array('gif'=>'image/gif','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png');
    if(isset($mime[$ext])){
        header("Content-type: ".$mime[$ext]);
	    echo file_get_contents($cachename);
	}
  }
  else
  { ini_set("include_path","system/pear/".PATH_SEPARATOR.ini_get("include_path"));

    require_once("Image/Transform.php");

	$it = Image_Transform::factory('GD');
    $it->load($src);

	if(!empty($_GET['x']) && !empty($_GET['y']) && ($it->img_x>(integer)$_GET['x'] || $it->img_y>(integer)$_GET['y']))
    { if($_GET['x']<0 || $_GET['x']>800 || $_GET['y']<0 || $_GET['y']>800)
      { $it->display();
        exit();
      }
	  $it->scaleByX((integer)$_GET['x']);
	  if($it->new_y>(integer)$_GET['y'])
	  $it->crop((integer)$_GET['x'],(integer)$_GET['y'],0,0);
	}
	elseif(!empty($_GET['x']) && $it->img_x>(integer)$_GET['x'])
	{ if($_GET['x']<0 || $_GET['x']>800)
      { $it->display();
        exit();
      }
	  $it->scaleByX((integer)$_GET['x']);
	}
	elseif(!empty($_GET['y']) && $it->img_y>(integer)$_GET['y'])
	{ if($_GET['y']<0 || $_GET['y']>800)
      { $it->display();
        exit();
      }
	  $it->scaleByY((integer)$_GET['y']);
	}

	$it->display();
  }
}