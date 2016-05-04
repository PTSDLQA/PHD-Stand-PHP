<?php
/** \file system/framework/main.php
 * Страница или группа страниц.
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
 * Интерфейс страницы (группы страниц).
 */

interface A_iMainFrame
{
/**
 * Переопределяемый метод формирования данных доступных в шаблоне.
 */

  function createData();
}

/**
 * Фильтр постобработки страницы.
 *
 * @param string $content Содержимое страницы.
 * @param object &$smarty Объект страницы.
 */

function preparePage($content,&$smarty)
{
  $info="\n<!-- Time generation: ".round(timeMeasure()-TIMESTART, 6)." s. --> \n";
  $info.="<!-- DB query: ".A::$DB->qcounter." -->\n";
  $info.="<!-- Memory usage: ".sizestring(memory_get_usage())." -->";
  return $content.$info;
}

/**
 * Класс страницы (группы страниц).
 */

class A_MainFrame extends Smarty implements A_iMainFrame
{
/**
 * Заголовок (title).
 */

  public $title;

/**
 * Название.
 */

  public $caption;

/**
 * Ключевые слова.
 */

  public $keywords;

/**
 * Описание.
 */

  public $description;

/**
 * Шаблон страницы.
 */

  public $template;

/**
 * Массив левых блоков.
 */

  public $leftblocks=array();

/**
 * Массив правых блоков.
 */

  public $rightblocks=array();

/**
 * Массив блоков с заданным расположением.
 */

  public $freeblocks=array();

/**
 * Массив всех блоков.
 */

  public $blocks=array();

/**
 * Массив задействованных типов блоков: тип блока => 1.
 */

  public $useblocks=array();

/**
 * Идентификатор типа страницы.
 */

  public $page;

/**
 * Флаг проверки доступа к странице.
 */

  public $fad;

/**
 * Запомнить страницу как предыдущую для следующей.
 */

  public $prevc=true;

/**
 * Объект строки навигации (хлебные крошки).
 */

  public $navigation;

/**
 * Массив подключаемых JavaScript файлов.
 */

  public $jscripts_file = array();

/**
 * Массив подключаемого JavaScript кода.
 */

  public $jscripts_code = array();

/**
 * Параметры кэширования страницы.
 */

  public $cache_params=array();

/**
 * Массив с метками о возникших ошибках.
 */

  public $errors=array();

/**
 * Конструктор.
 *
 * @param string $template='' Шаблон страницы.
 */

  function __construct($template='')
  {
    $this->template_dir=SMARTY_TEMPLATES.'/';
	$this->compile_dir=SMARTY_COMPILE.'/';

	$this->template=$template;
	$this->navigation = new A_Navigation;
  }

/**
 * Метод инициализирующий кэширование страницы. Должен вызываться в самом начале метода createData.
 */

  function supportCached()
  {
    //-- Только в полной версии.
  }

/**
 * Метод устанавливает get параметр от значения которого зависит содержимое страницы.
 *
 * @param string $param Имя параметра.
 */

  function addCacheParam_Get($param)
  {
    //-- Только в полной версии.
  }

/**
 * Метод устанавливает переменную сессии от значения которой зависит содержимое страницы.
 *
 * @param string $param Имя переменной сессии.
 */

  function addCacheParam_Session($param)
  {
    //-- Только в полной версии.
  }

/**
 * Добавляет на страницу файл или код JavaScript.
 *
 * @param string $script Путь к файлу или текст скрипта.
 * @param string $mode="file" Принимает значения: file - подключается файл, code - встраивается код.
 */

  function AddJScript($script,$mode='file')
  {
    switch($mode)
    { case "file": if(!in_array($script,$this->jscripts_file))
				   $this->jscripts_file[]=A_MODE==A_MODE_ADMIN?$script.'?'.A::$OPTIONS['version']:$script;
				   break;
	  case "code": $this->jscripts_code[]=$script; break;
	}
  }

/**
 * Добавляет на страницу JavaScript переменную.
 *
 * @param string $var Имя переменной.
 * @param mixed $value Значение переменной.
 */

  function AddJVar($var,$value)
  {
    if(is_string($value)) $value="'{$value}'";
    $this->AddJScript("var {$var}={$value};","code");
  }

/**
 * Добавляет элемент в строку навигации (хлебные крошки).
 *
 * @param string $name Название элемента.
 * @param string $link Ссылка.
 */

  function AddNavigation($name,$link='')
  {
    $this->navigation->Add($name,$link);
  }

/**
 * Создает объекты блоков.
 */

  function loadBlocks()
  {
    A::$DB->query("SELECT * FROM mysite_blocks WHERE active='Y' ORDER BY align,sort");
    while($row=A::$DB->fetchRow())
    if(file_exists("blocks/{$row['block']}/{$row['block']}.php"))
	{ require_once("blocks/{$row['block']}/{$row['block']}.php");
      $this->useblocks[$row['block']]=1;
	  $show=!empty($row['show'])?unserialize($row['show']):array();
      if(empty($show) || (isset($show[SECTION_ID]) && (count($show[SECTION_ID])==0 || in_array($this->page,$show[SECTION_ID]))))
	  { $class=$row['block'].'_Block';
	    $params=!empty($row['params'])?unserialize($row['params']):array();
	    $objblock = new $class($params,$row['block'],$row['name']);
		$objblock->Assign("title",$row['caption']);
		$objblock->Assign("caption",$row['caption']);
	    if(!empty($row['frame']))
	    $obj = new A_Frame($row['frame'],$row['caption'],$objblock);
		else
		$obj = $objblock;
	    switch($row['align'])
	    { case "left":
	        if(!empty($row['name']))
		    { $this->blocks[$row['name']]=$obj;
		      $this->leftblocks[] =& $this->blocks[$row['name']];
		    }
		    else
		    $this->leftblocks[]=$obj;
		    break;
	      case "right":
		    if(!empty($row['name']))
		    { $this->blocks[$row['name']]=$obj;
		      $this->rightblocks[] =& $this->blocks[$row['name']];
		    }
		    else
		    $this->rightblocks[]=$obj;
		    break;
		  case "free":
		    if(!empty($row['name']))
		    { $this->blocks[$row['name']]=$obj;
			  $this->freeblocks[] =& $this->blocks[$row['name']];
			}
			else
			$this->freeblocks[]=$obj;
		    break;
	    }
	  }
	}
	A::$DB->free();

	$this->Assign_by_ref("leftblocks",$this->leftblocks);
	$this->Assign_by_ref("rightblocks",$this->rightblocks);
	$this->Assign_by_ref("blocks",$this->blocks);
  }

/**
 * Доступ запрещен, происходит замена шаблона (если существует access_denied.tpl) или 404 Not Found.
 */

  function goAccessDenied()
  {
    $this->fad=true;
	$denied_tpl=getName(SECTION)."_denied.tpl";
	if(is_file($this->template_dir.'/'.$denied_tpl))
	$this->template=$denied_tpl;
	elseif(is_file($this->template_dir.'/access_denied.tpl'))
	$this->template="access_denied.tpl";
	else
	A::NotFound();
  }

/**
 * Переопределяемый метод маршрутизатора типов страниц.
 *
 * @param array $uri Элементы полного пути URL.
 */

  function Router($uri)
  {
  }

/**
 * Переопределяемый метод маршрутизатора действий.
 *
 * @param string $action Идентификатор действия.
 */

  function Action($action)
  {
  }

/**
 * Переопределяемый метод формирования данных доступных в шаблоне.
 */

  function createData()
  {
  }

/**
 * Дополняет данные страницы массивом $fields с информацией о дополнительных полях (для форм добавления).
 */

  protected function prepareAddForm()
  {
    $fields=array();
    A::$DB->query("SELECT * FROM mysite_fields WHERE item='".SECTION."' AND nofront='N' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { if($row['type']=="date")
      { if(isset($_REQUEST[$row['field'].'Month']) && isset($_REQUEST[$row['field'].'Day']) && isset($_REQUEST[$row['field'].'Year']))
        $_REQUEST[$row['field']]=mktime(0,0,0,(integer)$_REQUEST[$row['field'].'Month'],(integer)$_REQUEST[$row['field'].'Day'],(integer)$_REQUEST[$row['field'].'Year']);
        $row['startyear']=date("Y",isset($_REQUEST[$row['field']])?$_REQUEST[$row['field']]:time())-3;
      }
      if(isset($_REQUEST[$row['field']]))
      { if(is_array($_REQUEST[$row['field']]))
	    { $row['value']=array();
		  foreach($_REQUEST[$row['field']] as $value)
	      $row['value'][]=(integer)$value;
	    }
	    else
		$row['value']=strip_tags($_REQUEST[$row['field']]);
      }
      elseif($row['type']=="bool")
      $row['value']=$row['property']?'Y':'N';
	  else
      $row['value']="";
      if(empty($row['value']) && !empty(A::$AUTH->data[$row['field']]))
	  $row['value']=A::$AUTH->data[$row['field']];
      $row['name']=$row['name_ru'];
      $fields[$row['field']]=$row;
    }
    A::$DB->free();
    $this->Assign("fields",$fields);
  }

/**
 * Дополняет данные страницы массивом $fields с информацией о дополнительных полях (для форм редактирования).
 *
 * @param array $data Массив значений полей.
 */

  protected function prepareEditForm($data)
  {
    $fields=array();
    A::$DB->query("SELECT * FROM mysite_fields WHERE item='".SECTION."' AND nofront='N' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { if($row['type']=="date")
      $row['startyear']=date("Y",!empty($data[$row['field']])?$data[$row['field']]:time())-3;
      $row['value']=isset($data[$row['field']])?strip_tags($data[$row['field']]):"";
      if($row['type']=="float")
      $row['value']=round($row['value'],2);
      $row['name']=$row['name_ru'];
      $fields[$row['field']]=$row;
    }
    A::$DB->free();
    $this->Assign("fields",$fields);
  }

/**
 * Дополняет массив фильтров информацией о дополнительных полях (только панель управления).
 *
 * @param array $data Массив фильтров.
 */

  protected function fieldseditor_setfilter(&$data)
  {
    $item=MODE=='sections'?SECTION:STRUCTURE;
    A::$DB->query("SELECT * FROM mysite_fields WHERE item='$item' AND search='Y' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    switch($row['type'])
    { case "int":
        $data[$row['field']."1"]=!empty($_REQUEST[$row['field']."1"])?(integer)$_REQUEST[$row['field']."1"]:"";
        $data[$row['field']."2"]=!empty($_REQUEST[$row['field']."2"])?(integer)$_REQUEST[$row['field']."2"]:"";
  	    break;
      case "float":
        $data[$row['field']."1"]=!empty($_REQUEST[$row['field']."1"])?(float)$_REQUEST[$row['field']."1"]:"";
        $data[$row['field']."2"]=!empty($_REQUEST[$row['field']."2"])?(float)$_REQUEST[$row['field']."2"]:"";
	    break;
      default:
        $data[$row['field']]=isset($_REQUEST[$row['field']])?A::$DB->real_escape_string($_REQUEST[$row['field']]):"";
    }
    A::$DB->free();
  }

/**
 * В массиве фильтров сбрасывает значения для дополнительных полей (только панель управления).
 *
 * @param array $data Массив фильтров.
 */

  protected function fieldseditor_unfilter(&$data)
  {
    $item=MODE=='sections'?SECTION:STRUCTURE;
    A::$DB->query("SELECT * FROM mysite_fields WHERE item='$item' AND search='Y' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    switch($row['type'])
    { case "int":
      case "float":
        $data[$row['field']."1"]="";
        $data[$row['field']."2"]="";
	    break;
      default:
        $data[$row['field']]="";
    }
    A::$DB->free();
  }

/**
 * Генерирует строку условия для SQL запроса по значениям заданных $_GET параметров. Только для применения на сайте.
 *
 * @param string $where Начальная строка условия.
 * @param array $fields=array('name'=>'string') Список полей с типами, дополнительные поля добавляются автоматически.
 * @param string $prefix='' Префикс для полей в условиях.
 * @param array $types=array('string','int','float','bool','date','select','mselect') Массив типов полей, которые нужно использовать.
 * @return string Условие SQL запроса.
 */

  protected function frontfilter($where='',$fields=array('name'=>'string'),$prefix='',$types=array('string','int','float','bool','date','select','mselect'))
  {
    if(!empty($_GET['filter']))
    { $vars=getLists();
	  A::$DB->query("SELECT * FROM mysite_fields WHERE item='".SECTION."'");
      while($frow=A::$DB->fetchRow())
      if(in_array($frow['type'],$types))
      $fields[$frow['field']]=$frow['type'];
      A::$DB->free();
      foreach($_GET as $key=>$value)
	  { $_fields=explode("_",$key);
	    if(count($_fields)>1)
	    { $where2=array();
		  foreach($_fields as $_key)
	      if(isset($fields[$_key]) && ($fields[$_key]=='string' || $fields[$_key]=='text'))
	      $where2[]="{$prefix}`{$_key}` LIKE '%".A::$DB->real_escape_string(mb_substr($value,0,30))."%'";
	      if($where2)
          $where.=" AND (".implode(' OR ',$where2).")";
	    }
	  }
	  foreach($fields as $field=>$type)
      if(isset($_GET[$field]) || isset($_GET[$field.'_min']) || isset($_GET[$field.'_max']))
      switch($type)
      { case 'string':
          if(!empty($_GET[$field]))
	      $where.=" AND {$prefix}`{$field}` LIKE '%".A::$DB->real_escape_string(mb_substr($_GET[$field],0,30))."%'";
	      break;
	    case 'int':
	    case 'date':
	      if(!empty($_GET[$field]) && !isset($vars[$field]))
	      $where.=" AND {$prefix}`{$field}`=".(integer)$_GET[$field];
	      else
	      { if(isset($vars[$field]))
	        { $list=loadList($field);
	          if(!empty($list[$_GET[$field]]['min']))
	          $_GET[$field.'_min']=$list[$_GET[$field]]['min'];
	          if(!empty($list[$_GET[$field]]['max']))
	          $_GET[$field.'_max']=$list[$_GET[$field]]['max'];
	        }
		    if(!empty($_GET[$field.'_min']))
	        $where.=" AND {$prefix}`{$field}`>=".(integer)$_GET[$field.'_min'];
	        if(!empty($_GET[$field.'_max']))
	        $where.=" AND {$prefix}`{$field}`<=".(integer)$_GET[$field.'_max'];
	      }
	      break;
	    case 'float':
	      if(!empty($_GET[$field]) && !isset($vars[$field]))
	      $where.=" AND {$prefix}`{$field}`=".(float)$_GET[$field];
	      else
	      { if(isset($vars[$field]))
	        { $list=loadList($field);
	          if(!empty($list[$_GET[$field]]['min']))
	          $_GET[$field.'_min']=$list[$_GET[$field]]['min'];
	          if(!empty($list[$_GET[$field]]['max']))
	          $_GET[$field.'_max']=$list[$_GET[$field]]['max'];
	        }
		    if(!empty($_GET[$field.'_min']))
	        $where.=" AND {$prefix}`{$field}`>=".(float)$_GET[$field.'_min'];
	        if(!empty($_GET[$field.'_max']))
	        $where.=" AND {$prefix}`{$field}`<=".(float)$_GET[$field.'_max'];
	      }
	      break;
	    case 'bool':
	      if(isset($_GET[$field]))
	      $where.=" AND {$prefix}`{$field}`=".(!empty($_GET[$field])?"'Y'":"'N'");
	      break;
      }
    }
	return $where;
  }

/**
 * Генерирует строку условия для SQL запроса по значениям фильтров для дополнительных полей. Только для применения в панели управления.
 *
 * @param string $where Начальная строка условия.
 * @param array &$data Массив значений фильтров.
 * @param string $prefix='' Префикс для полей в условиях.
 * @return string Условие SQL запроса.
 */

  protected function adminfilter($where='',&$data,$prefix='')
  {
    $item=defined('MODE') && MODE=='structures'?STRUCTURE:SECTION;
    A::$DB->query("SELECT * FROM mysite_fields WHERE item='$item' AND search='Y' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    if(isset($data[$row['field']]))
	switch($row['type'])
    { case "int":
      case "float":
        if(!empty($data[$row['field']."1"]))
	    { $where.=!empty($where)?" AND ":"";
	      $where.="{$prefix}`{$row['field']}`>=".$data[$row['field']."1"];
	    }
	    if(!empty($data[$row['field']."2"]))
	    { $where.=!empty($where)?" AND ":"";
	      $where.="{$prefix}`{$row['field']}`<=".$data[$row['field']."2"];
	    }
	    break;
      case "bool":
	    if(!empty($data[$row['field']]))
	    { $where.=!empty($where)?" AND ":"";
 	      $where.="{$prefix}`{$row['field']}`='".$data[$row['field']]."'";
	    }
	    break;
      default:
	    if(!empty($data[$row['field']]))
	    { $where.=!empty($where)?" AND ":"";
	      $where.="{$prefix}`{$row['field']}` LIKE '%".$data[$row['field']]."%'";
	    }
    }
	A::$DB->free();
	return $where;
  }

/**
 * Вывод сгенерированной страницы.
 *
 */

  function display()
  {
	A::$OBSERVER->Event('CreatePage',$this->template);

	$this->createData();

	if(A_MODE==A_MODE_FRONT)
    {
	  if(!empty(A::$OPTIONS['siteclose']) && !A_Auth::getInstanceAdmin()->isLogin())
      { $this->template_dir='templates/admin/';
        $this->compile_dir='templates_c/admin/';
        $this->secure_dir=array('templates/admin');
        $this->template='admin_close.tpl';
        $this->Assign("sitename",A::$OPTIONS['sitename_ru']);
      }
	  if(A::$OPTIONS['debugmode'])
      $this->register_outputfilter('preparePage');
	  $this->Assign("description",strip_tags(preg_replace("/[\"'\n\r]+/i","",$this->description)));
	  $this->Assign("keywords",strip_tags($this->keywords));
	  $this->Assign("site_name",A::$OPTIONS['sitename_ru']);
	  $this->Assign("meta",!empty(A::$OPTIONS['codemeta'])?A::$OPTIONS['codemeta']."\n":"");
	  $this->Assign("navigation",$this->navigation);
	  $this->Assign("section_name",SECTION_NAME);
	  $this->Assign('section_idimg',$GLOBALS['A_SECTION']['idimg']);
	  $this->AddJScript("/system/jscripts/front.js");
	}
	else
	{ $this->AddJScript("/system/jscripts/common.js");
	  $this->AddJVar("AUTHCODE",A::$AUTH->authcode);
	  if(defined("MODE")) $this->AddJVar("MODE",MODE);
      if(defined("ITEM")) $this->AddJVar("ITEM",ITEM);
	  if(defined("SECTION")) $this->AddJVar("SECTION",SECTION);
	  $this->AddJVar("LANG",'ru');
	  $this->Assign_by_ref("site_name",A::$OPTIONS['sitename_ru']);

	  if(defined('SECTION_NAME'))
      { $this->Assign("section_name",SECTION_NAME);
	    $this->Assign("caption",!empty($this->_tpl_vars['caption'])?SECTION_NAME.' - '.$this->_tpl_vars['caption']:SECTION_NAME);
	  }

	  $path1=$this->template_dir.'/'.$this->template;
	  if(defined('MODE'))
	  switch(MODE)
	  { case 'sections':
	      if(defined('MODULE'))
		  $path2='modules/'.MODULE.'/templates/admin/'.$this->template;
		  break;
		case 'structures':
		  if(defined('PLUGIN'))
		  $path2='plugins/'.PLUGIN.'/templates/admin/'.$this->template;
		  break;
	  }
	  if(!empty($path2) && is_file($path2) && (!is_file($path1) || filemtime($path2)>filemtime($path1)))
	  copyfile($path2,$path1,true);
	}

	$js="";
	foreach($this->jscripts_file as $i=>$jfile)
	$js.="<script type=\"text/javascript\" src=\"{$jfile}\"></script>\n";
	foreach($this->jscripts_code as $i=>$jcode)
	$js.="<script type=\"text/javascript\">$jcode</script>\n";
	$this->Assign("jscripts",$js);

	$this->Assign("system",A::getSystem());
	$this->Assign_by_ref("options",A::$OPTIONS);
	$this->Assign_by_ref("title",$this->title);
	$this->Assign_by_ref("errors",$this->errors);
    $this->Assign_by_ref("auth",A::$AUTH);

	A::$OBSERVER->Event('ShowPage',$this->template);

	if(is_file($this->template_dir.'/'.$this->template))
	{ if($this->prevc)
	  A_Session::set("A_PREVURL",urldecode(getenv('REQUEST_URI')));
	  parent::display($this->template);
	}
	else
	A::NotFound();

	exit();
  }

/**
 * Вывод сгенерированной страницы без вызова методов формирования данных.
 */

  function _display()
  {
    $this->Assign_by_ref("auth",A::$AUTH);
    $this->Assign("system",A::getSystem());

	if($this->template=='404.tpl')
	{ $this->loadBlocks();
	  $this->Assign_by_ref("options",A::$OPTIONS);
	  $this->Assign('title',!empty($GLOBALS['A_SECTION']['title_ru'])?$GLOBALS['A_SECTION']['title_ru'].(!empty(A::$OPTIONS['sitetitle_ru'])?" - ".A::$OPTIONS['sitetitle_ru']:""):A::$OPTIONS['sitetitle_ru']);
      $this->Assign("site_name",A::$OPTIONS['sitename_ru']);
	  $this->Assign("meta",!empty(A::$OPTIONS['codemeta'])?A::$OPTIONS['codemeta']."\n":"");
      $this->Assign('section_name',SECTION_NAME);
	  $this->Assign('section_idimg',$GLOBALS['A_SECTION']['idimg']);
	}

	A::$OBSERVER->Event('ShowPage',$this->template);

	if(is_file($this->template_dir.'/'.$this->template))
	{ if($this->prevc)
	  A_Session::set("A_PREVURL",urldecode(getenv('REQUEST_URI')));
	  parent::display($this->template);
	}
	else
	A::NotFound();

	exit();
  }
}