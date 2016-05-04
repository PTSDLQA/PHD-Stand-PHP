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

function smarty_function_imagedata($params, &$smarty)
{
  require_once $smarty->_get_plugin_filepath('modifier','curlang');

  if(!empty($params['id']) && !empty($params['var']))
  { if($data=A::$DB->getRow("SELECT * FROM mysite_images WHERE id=".(integer)$params['id']))
    { $data['caption']=smarty_modifier_curlang($data['caption']);
	  $smarty->Assign($params['var'],$data);
    }
  }
}