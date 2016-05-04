<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package AComponents
 */
/**************************************************************************/

class SearchModule_Request extends A_Request
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

    $tags=A::$DB->getCol("SELECT tag FROM ".SECTION."_tags WHERE tag LIKE '{$query}%' LIMIT 0,20");
    if($tags)
    print '<ul><li>'.implode('</li><li>',$tags).'</li></ul>';
    else
    print '<span></span>';
  }
}

A::$REQUEST = new SearchModule_Request;