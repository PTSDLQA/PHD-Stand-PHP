<?php
/** \file download.php
 * Скачивание файла.
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

if(!empty($_GET['file']))
{
  ini_set("include_path","system/pear/".PATH_SEPARATOR.ini_get("include_path"));

  $file=preg_replace("/^\//i","",preg_replace("/[.]{2,}/i","",preg_replace("/[^a-zA-Zа-яА-Я0-9_.\/-]/iu","",$_GET['file'])));

  if(!is_file($file))
  die('file not exists');

  $securitydir=array('files/','templates/');
  $security=false;
  foreach($securitydir as $dir)
  if(mb_strpos($file,$dir)===0)
  $security=true;
  if(!$security)
  { define("A_MODE",0);
    require_once("system/framework/ini.php");
    $AUTH_ADMIN = A_Auth::getInstanceAdmin();
    if(!$AUTH_ADMIN->isSuperAdmin())
    die('access denied');
  }

  require_once('HTTP/Download.php');
  require_once("system/libs/mimetypes.php");

  $path_parts=pathinfo($file);
  $ext=strtolower($path_parts["extension"]);
  $mime=isset($mimetypes[$ext])?$mimetypes[$ext]:"application/octet-stream";

  $params['file'] = $file;
  $params['contenttype'] = $mime;
  $params['contentdisposition'] = array(HTTP_DOWNLOAD_ATTACHMENT,end(explode('/',$file)));

  HTTP_Download::staticSend($params,false);
}
elseif(!empty($_GET['id']))
{
  define("A_MODE",0);
  require_once("system/framework/ini.php");

  if(A::$FILE=A::$DB->getRowById($_GET['id'],"mysite_files"))
  {
    A::$DB->execute("UPDATE mysite_files SET dwnl=dwnl+1 WHERE id=".A::$FILE['id']);

    require_once('HTTP/Download.php');

    $params['file'] = preg_replace("/^\//i","",A::$FILE['path']);
    $params['contenttype'] = A::$FILE['mime'];
    $params['contentdisposition'] = array(HTTP_DOWNLOAD_ATTACHMENT,A::$FILE['name']);

    HTTP_Download::staticSend($params,false);
  }
  else
  die('file not exists');
}
else
die('file not exists');