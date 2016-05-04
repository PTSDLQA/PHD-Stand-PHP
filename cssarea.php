<?php
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

require_once("system/framework/session.php");

A_Session::start();

header("Content-type: text/css");

$_css="";
$files=scandir("templates/mysite");
foreach($files as $file)
if($file=='editorarea.css')
die(@file_get_contents("templates/mysite/$file"));
elseif(is_file($file="templates/mysite/$file"))
{ $path_parts=pathinfo($file);
  if($path_parts['extension']=='css')
  $_css.=@file_get_contents($file);
}
if(!empty($_css))
{ $css=array();
  if(preg_match_all("/\/\* \[EditorArea Begin\] \*\/([^\]]+)\/\* \[EditorArea End\] \*\//i",$_css,$matches))
  { foreach($matches[1] as $match)
	$css[]=str_replace("#content ","body ",$match);
    if(!empty($css))
    die(implode("\n",$css));
  }
}