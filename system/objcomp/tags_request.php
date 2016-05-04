<?php
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AComponents
 */
/**************************************************************************/

class A_TagsRequest extends A_Request
{
  function Action($action)
  {
	 switch($action)
     { case "gettags": $this->getTags(); break;
     }
  }

  function getTags()
  {
	$query = A::$DB->real_escape_string(mb_strtolower($_POST['query']));

    if($section=getSectionByModule('search'))
    {
	  $tags=A::$DB->getCol("SELECT tag FROM {$section}_tags WHERE tag LIKE '{$query}%' LIMIT 0,20");
      if($tags)
      print '<ul><li>'.implode('</li><li>',$tags).'</li></ul>';
      else
      print '<span></span>';
    }
    else
    print '<span></span>';
  }
}

A::$REQUEST = new A_TagsRequest;