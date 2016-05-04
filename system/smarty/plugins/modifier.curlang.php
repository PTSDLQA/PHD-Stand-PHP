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

function smarty_modifier_curlang($string)
{
  $_string=explode("|",$string);
  $i=0;
  foreach(A::$LANGUAGES as $id=>$name)
  { if($id==A::$LANG)
    { if(!empty($_string[$i]))
      return trim($_string[$i]);
      break;
    }
    $i++;
  }
  return $string;
}