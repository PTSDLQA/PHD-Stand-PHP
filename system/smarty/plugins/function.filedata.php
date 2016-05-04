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

function smarty_function_filedata($params, &$smarty)
{
  require_once $smarty->_get_plugin_filepath('modifier','curlang');

  if(!empty($params['id']) && !empty($params['var']))
  { if($data=A::$DB->getRowById($params['id'],"mysite_files"))
	{ $data['caption']=smarty_modifier_curlang($data['caption']);
	  $data['link']="/getfile/".$data['id']."/".$data['name'];
	  $data['size']=sizestring($data['size']);
      $smarty->Assign($params['var'],$data);
    }
  }
}