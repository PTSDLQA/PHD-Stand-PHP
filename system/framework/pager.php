<?php
/** \file system/framework/pager.php
 * Многостраничная навигация.
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

/**
 * Класс реализует многостраничную  навигацию по записям (нумератор страниц).
 */

class A_Pager extends Smarty
{
/**
 * Номер первой записи.
 */

  public $begin=0;

/**
 * Номер последней записи.
 */

  public $end=0;

/**
 * Количество страниц.
 */

  public $pages=0;

/**
 * Общее количество записей.
 */

  public $allcount=0;

/**
 * Шаблон.
 */

  public $template;

/**
 * Идентификатор таба (только панель управления).
 */

  public $tab;

/**
 * Количество записей на странице.
 */

  public $pagerows=0;

/**
 * Количество выводимых первых номеров страниц.
 */

  public $pagecount=50;

/**
 * Параметр $_GET указывающий номер страницы.
 */

  public $varname='page';

/**
 * JavaScript обработчик.
 */

  public $jfun=false;

/**
 * Ссылка на первую страницу.
 */

  public $firstlink=false;

/**
 * Ссылка на последнюю страницу.
 */

  public $lastlink=false;

/**
 * Ссылка на предыдущую страницу.
 */

  public $prevlink=false;

/**
 * Ссылка на следующую страницу.
 */

  public $nextlink=false;

/**
 * Список страниц-ссылок.
 */

  public $pagelinks=array();

/**
 * Идентификатор объекта нумератора.
 */

  protected static $pager_id=0;

/**
 * Конструктор.
 *
 * @param string $pagerows Количество записей на странице.
 * @param string $jfun=false JavaScript обработчик.
 */

  function __construct($pagerows,$jfun=false)
  {
	$this->template_dir=SMARTY_TEMPLATES."/others/";
	$this->compile_dir=SMARTY_COMPILE."/others/";

	$this->template=A_MODE==0?"pager.tpl":"default_pager.tpl";

	if(A_Pager::$pager_id++>0)
	$this->varname.=(A_Pager::$pager_id-1);

	$_GET[$this->varname]=!empty($_GET[$this->varname])?(integer)$_GET[$this->varname]:0;

	if($_GET[$this->varname]<0)
	$_GET[$this->varname]=0;

	$this->pagerows=(integer)$pagerows;
	if($jfun)
	$this->jfun=$jfun;
  }

/**
 * SQL запрос для выборки записей.
 *
 * @param string $sql Строка запроса.
 * @param array $params=null Параметры запроса.
 */

  function query($sql,$params=null)
  {
	$begin=$_GET[$this->varname]*$this->pagerows;

	if($this->pagerows>0)
	$this->allcount=A::$DB->queryLimit($sql,$begin,$this->pagerows,$params);
	else
	$this->allcount=A::$DB->query($sql,$params);

	if($this->allcount>0)
	{ if($this->pagerows>0)
	  $this->pages=ceil($this->allcount/$this->pagerows);
	  else
	  $this->pages=1;
	}

	$this->begin=$begin+1;
	$this->end=$begin+A::$DB->numRows();

	if(A::$DB->numRows()==0 && $_GET[$this->varname]>0)
	{ $_GET[$this->varname]=0;
	  $this->query($sql,$params);
	  return;
	}

	if($this->jfun)
	$this->createJsLinks();
	else
	$this->createUrlLinks();
  }

/**
 * SQL запрос для выборки записей.
 * Не поддерживает параметры и работает в обход системы кэширования.
 *
 * @param string $sql Строка запроса.
 */

  function _query($sql)
  {
	$begin=$_GET[$this->varname]*$this->pagerows;

	if($this->pagerows>0)
	$this->allcount=A::$DB->_queryLimit($sql,$begin,$this->pagerows);
	else
	$this->allcount=A::$DB->_query($sql);

	if($this->allcount>0)
	{ if($this->pagerows>0)
	  $this->pages=ceil($this->allcount/$this->pagerows);
	  else
	  $this->pages=1;
	}

	$this->begin=$begin+1;
	$this->end=$begin+A::$DB->numRows();

	if(A::$DB->numRows()==0 && $_GET[$this->varname]>0)
	{ $_GET[$this->varname]=0;
	  $this->_query($sql,$params);
	  return;
	}

	if($this->jfun)
	$this->createJsLinks();
	else
	$this->createUrlLinks();
  }

/**
 * Извлечение очередной записи из результата запроса.
 *
 * @return array Ассоциированный массив или false.
 */

  function fetchRow()
  {
    return A::$DB->fetchRow();
  }

/**
 * Количество записей в результате запроса.
 *
 * @return array Ассоциированный массив.
 */

  function numRows()
  {
    return A::$DB->numRows();
  }

/**
 * Удаление результата запроса из стека.
 */

  function free()
  {
    return A::$DB->free();
  }

/**
 * Устанавливает количество записей на странице.
 *
 * @param integer $cpages Количество записей на странице.
 */

  function setPages($cpages)
  {
    $this->pages=$cpages;

	if($this->jfun)
	$this->createJsLinks();
	else
	$this->createUrlLinks();
  }

/**
 * Вырезает из исходного массива записей данные для текущей страницы.
 *
 * @param array $items Массив записей.
 * @return array Обрезанный массив записей.
 */

  function setItems($items)
  {
    if($this->pagerows==0)
	return $items;

	$this->allcount=count($items);
	$this->setPages(ceil(count($items)/$this->pagerows));

	$begin=$_GET[$this->varname]*$this->pagerows;
	$this->begin=$begin+1;
	$this->end=$begin+$this->pagerows;
	if($this->end>count($items))
	$this->end=count($items)-$begin;

	return array_slice($items,$begin,$this->pagerows);
  }

/**
 * Формирует ссылку страницы по номеру.
 *
 * @param integer $value Номер страницы.
 */

  function CreateLink($value)
  {
	$VARS=$_GET;
    $VARS[$this->varname]=$value;
    $query="";

	foreach($VARS as $name=>$value)
	{ if(preg_match("/^page[0-9]*/i",$name,$matches) && $value==0)
	  continue;
	  if($name=="tab" && !empty($this->tab))
	  { $query.=empty($query)?"$name=$this->tab":"&$name=$this->tab";
	    $tab=true;
	  }
	  elseif(is_array($value))
	  { foreach($value as $val)
	    $query.=empty($query)?"$name%5B%5D=$val":"&$name%5B%5D=$val";
	  }
	  else
	  $query.=empty($query)?"$name=$value":"&$name=$value";
	}
	if(!empty($this->tab) && !isset($tab))
	$query.=empty($query)?"tab=$this->tab":"&tab=$this->tab";

	$purl=parse_url(getenv('REQUEST_URI'));
	return !empty($query)?$purl['path']."?$query":$purl['path'];
  }

/**
 * Формирует массив ссылок на страницы.
 */

  function createUrlLinks()
  {
	if($this->pages<2) return;

	if($_GET[$this->varname]>0)
	$this->prevlink=$this->CreateLink($_GET[$this->varname]-1);

	$page_1=floor($_GET[$this->varname]/10)*10;
	$page_2=floor($_GET[$this->varname]/10)*10+10;
	$page_3=$_GET[$this->varname]-3;
	$page_4=$_GET[$this->varname]+3;
	$fs=true;

	for($i=0;$i<$this->pages;$i++)
    if($i<$this->pagecount)
	{ if($i>=$page_1 && $i<$page_2)
	  $this->pagelinks[]=array("name"=>$i+1,"link"=>$this->CreateLink($i),"selected"=>$i==$_GET[$this->varname]);
	  elseif($i%10==0)
	  { $p1=$i+1;
	    $p2=$i+10<=$this->pages?$i+10:$this->pages;
		if($p1!=$p2)
	    $this->pagelinks[]=array("name"=>$p1."-".$p2,"link"=>$this->CreateLink($i),"selected"=>false);
		else
		$this->pagelinks[]=array("name"=>$p1,"link"=>$this->CreateLink($i),"selected"=>false);
	  }
	}
	else
	{ if(($i>=$page_3 && $i<=$page_4) || $i==$this->pages-1)
	  { $this->pagelinks[]=array("name"=>$i+1,"link"=>$this->CreateLink($i),"selected"=>$i==$_GET[$this->varname]);
	    $fs=true;
	  }
	  elseif($fs)
	  { $this->pagelinks[]=array("name"=>"...","link"=>"#","selected"=>false);
	    $fs=false;
	  }
	}

	if($_GET[$this->varname] < $this->pages-1)
	$this->nextlink=$this->CreateLink($_GET[$this->varname]+1);

	$this->firstlink=$this->CreateLink(0);
	$this->lastlink=$this->CreateLink($this->pages>0?$this->pages-1:0);
  }

/**
 * Формирует массив ссылок на JavaScript обработчики для страниц.
 */

  function createJsLinks()
  {
	if($this->pages<2) return;

	if($_GET[$this->varname] > 0)
	$this->prevlink="javascript:$this->jfun(".($_GET[$this->varname]-1).")";

	$page_1=floor($_GET[$this->varname]/10)*10;
	$page_2=floor($_GET[$this->varname]/10)*10+10;
	$page_3=$_GET[$this->varname]-3;
	$page_4=$_GET[$this->varname]+3;
	$fs=true;

	for($i=0;$i<$this->pages;$i++)
    if($i<$this->pagecount)
	{ if($i>=$page_1 && $i<$page_2)
	  $this->pagelinks[]=array("name"=>$i+1,"link"=>"javascript:$this->jfun($i)","selected"=>$i==$_GET[$this->varname]);
	  elseif($i%10==0)
	  { $p1=$i+1;
	    $p2=$i+10<=$this->pages?$i+10:$this->pages;
		if($p1!=$p2)
	    $this->pagelinks[]=array("name"=>$p1."-".$p2,"link"=>"javascript:$this->jfun($i)","selected"=>false);
		else
		$this->pagelinks[]=array("name"=>$p1,"link"=>"javascript:$this->jfun($i)","selected"=>false);
	  }
	}
	else
	{ if(($i>=$page_3 && $i<=$page_4) || $i==$this->pages-1)
	  { $this->pagelinks[]=array("name"=>$i+1,"link"=>"javascript:$this->jfun($i)","selected"=>$i==$_GET[$this->varname]);
	    $fs=true;
	  }
	  elseif($fs)
	  { $this->pagelinks[]=array("name"=>"...","link"=>"#","selected"=>false);
	    $fs=false;
	  }
	}

	if($_GET[$this->varname] < $this->pages-1)
	$this->nextlink="javascript:$this->jfun(".($_GET[$this->varname]+1).")";

	$this->firstlink="javascript:$this->jfun(0)";
	$this->lastlink="javascript:$this->jfun(".($this->pages>0?$this->pages-1:0).")";
  }

/**
 * Метод возвращает сгенерированный HTML код нумератора страниц.
 *
 * @param string $template=null Шаблон, если не указано то pager.tpl.
 * @return string HTML код нумератора страниц.
 */

  function getContent($template=null)
  {
    if(count($this->pagelinks)>1)
    { $this->Assign_by_ref("prevlink",$this->prevlink);
	  $this->Assign_by_ref("nextlink",$this->nextlink);
	  $this->Assign_by_ref("firstlink",$this->firstlink);
	  $this->Assign_by_ref("lastlink",$this->lastlink);
	  $this->Assign_by_ref("pagelinks",$this->pagelinks);
	  $this->Assign_by_ref("links",$this->pagelinks);
	  $this->Assign_by_ref("rows",$this->pagerows);
	  $this->Assign_by_ref("system",A::getSystem());
      $this->Assign_by_ref("auth",A::$AUTH);
	  return $this->fetch(!empty($template)?$template:$this->template);
	}
	else
	return "";
  }
}