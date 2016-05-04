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

function smarty_function_fckeditor($params, &$smarty)
{
  if(!is_dir("system/fckeditor/"))
  { require_once $smarty->_get_plugin_filepath('function','ckeditor');
    return smarty_function_ckeditor($params,$smarty);
  }
  else
  { require_once("system/fckeditor/fckeditor.php");
    $fckeditor = new FCKeditor($params['name']);
    $fckeditor->BasePath="/system/fckeditor/" ;
    $fckeditor->Height=isset($params['height'])?$params['height']:300;
    $fckeditor->Value=isset($params['text'])?$params['text']:"";
    if(A_MODE==1 && !empty(A::$OPTIONS['fckmini']))
    $fckeditor->ToolbarSet="Basic";
    else
    $fckeditor->ToolbarSet=isset($params['toolbar'])?$params['toolbar']:"Default";
    if(A_MODE==0)
    $fckeditor->Config=array('LinkBrowser'=>false,'ImageBrowser'=>false,'FlashBrowser'=>false,'LinkUpload'=>false,'ImageUpload'=>false,'FlashUpload'=>false,'FlashUpload'=>false);
    return $fckeditor->CreateHtml();
  }
}