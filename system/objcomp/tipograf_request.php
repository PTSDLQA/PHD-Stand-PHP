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

class A_TipografRequest extends A_Request
{
  function Action($action)
  { switch($action)
	{ case "getjevix": $this->getJevix(); break;
	  case "getlebedev": $this->getLebedev(); break;
	}
  }

  function getJevix()
  {
    require_once("system/libs/simple_html_dom.php");

	$html=str_replace(array('<div','</div>','<DIV','</DIV>'),array('<p','</p>','<p','</p>'),$_POST['text']);

	if($dhtml=@str_get_html($html))
    { if($items=$dhtml->find("a[mce_href],img[mce_src]"))
      foreach($items as $item)
      if($item->hasAttribute("mce_src"))
	  $item->removeAttribute("mce_src");
	  elseif($item->hasAttribute("mce_href"))
	  $item->removeAttribute("mce_href");
      $html=(string)$dhtml;
    }

	$html=htmlsafe($html,$error=null);

	if($dhtml=@str_get_html($html))
    { if($_p=$dhtml->find("p"))
	  foreach($_p as $p)
      if(!$p->find("img",0))
	  { if(!$str=preg_replace("/[^a-zA-Zа-яА-Я0-9]+/iu","",$p->plaintext))
	    $p->outertext="";
      }
      $html=(string)$dhtml;
    }

	$this->RESULT['html'] = $html;
  }

  function getLebedev()
  {
    require_once("system/libs/remotetypograf.php");

    $remoteTypograf = new RemoteTypograf('UTF-8');
    $remoteTypograf->htmlEntities();
    $remoteTypograf->br(false);
    $remoteTypograf->p(true);
    $remoteTypograf->nobr(3);
    $this->RESULT['html'] = $remoteTypograf->processText($_POST['text']);
  }
}

A::$REQUEST = new A_TipografRequest;