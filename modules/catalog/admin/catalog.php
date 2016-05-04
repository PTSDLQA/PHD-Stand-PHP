<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Modules
 */
/**************************************************************************/

/**
 * Панель управления модуля "Каталог материалов".
 *
 * <a href="http://wiki.a-cms.ru/modules/catalog">Руководство</a>.
 */

class CatalogModule_Admin extends A_MainFrame
{
/**
 * Конструктор.
 */

  function __construct()
  {
    parent::__construct("module_catalog.tpl");

	$this->AddJScript("/modules/catalog/admin/catalog.js");
	$this->AddJScript("/system/objcomp/jscripts/files.js");
	$this->AddJScript("/system/objcomp/jscripts/images.js");
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "additem": $res=$this->AddItem(); break;
  	  case "edititem": $res=$this->EditItem(); break;
	  case "delitem": $res=$this->DelItem(); break;
	  case "setsort": $res=$this->setSort(); break;
	  case "setrows": $res=$this->setRows(); break;
	  case "setfilter": $res=$this->setFilter(); break;
	  case "unfilter": $res=$this->unFilter(); break;
	  case "setactive": $res=$this->SetActive(); break;
	  case "setunactive": $res=$this->SetUnActive(); break;
	  case "delete": $res=$this->Delete(); break;
	  case "moveitems": $res=$this->MoveItems(); break;
	  case "save": $res=$this->Save(); break;
	}
	if(!empty($res))
	A::goUrl("admin.php?mode=sections&item=".SECTION,array('idcat','page','tab'));
  }

/**
 * Обработчик действия: Способ сортировки.
 */

  function setSort()
  {
    A_Session::set(SECTION."_sort",$_REQUEST['sort']);
	setcookie(SECTION."_sort",$_REQUEST['sort'],time()+31104000);
	return true;
  }

/**
 * Обработчик действия: Количество строк на странице.
 */

  function setRows()
  {
	A_Session::set(SECTION."_rows",$_REQUEST['rows']);
	setcookie(SECTION."_rows",$_REQUEST['rows'],time()+31104000);
	return true;
  }

/**
 * Обработчик действия: Установка фильтра.
 */

  function setFilter()
  {
	$data=array();
    $data['active']=true;
    $data['name']=A::$DB->real_escape_string($_REQUEST['name']);
	$data['content']=A::$DB->real_escape_string($_REQUEST['content']);
	$data['date']=isset($_REQUEST['date']);
	$data['date1']=(integer)$_REQUEST['from'];
	$data['date2']=(integer)$_REQUEST['to'];
    $data['status']=A::$DB->real_escape_string($_REQUEST['status']);
	$this->fieldseditor_setfilter($data);
	A_Session::set(SECTION."_filter",$data);
	return true;
  }

/**
 * Обработчик действия: Сброс фильтра.
 */

  function unFilter()
  {
    $data=array("active"=>false,"name"=>"","content"=>"","date"=>false,"status"=>0);
  	$date1=A::$DB->getOne("SELECT MIN(date) FROM ".SECTION."_catalog");
	$data['date1']=$date1>0?$date1:time();
	$data['date2']=time();
	$this->fieldseditor_unfilter($data);
	A_Session::set(SECTION."_filter",$data);
    return true;
  }

/**
 * Обработчик действия: Добавление записи.
 */

  function AddItem()
  {
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
    $_REQUEST['idcat']=(integer)$_REQUEST['idcat'];
	$_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION."_catalog","idcat=".(integer)$_REQUEST['idcat']);
	$_REQUEST['active']=isset($_REQUEST['active'])?"Y":"N";
	$_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".SECTION."_catalog")+1;
	$_REQUEST['keywords']=getkeywords($_REQUEST['content']);
	if(A::$OPTIONS['autoanons'])
	$_REQUEST['description']=truncate($_REQUEST['content'],A::$OPTIONS['anonslen']);
	if(!A::$OPTIONS['usedate'])
	$_REQUEST['date']=time();
	$_REQUEST['mdate']=time();

    $dataset = new A_DataSet(SECTION."_catalog",true);
    $dataset->fields=array("date","mdate","idcat","name","urlname","description","keywords","content","tags","active","sort");

    if($id=$dataset->Insert())
	{
	  for($i=1;$i<=5;$i++)
	  if(isset($_REQUEST["imagedescription$i"]))
	  UploadImage("image$i",$_REQUEST["imagedescription$i"],0,$id,$i);

	  for($i=1;$i<=5;$i++)
	  if(isset($_REQUEST["filedescription$i"]))
	  UploadFile("file$i",$_REQUEST["filedescription$i"],0,$id,$i);


      if($_REQUEST['active']=='Y')
	  { $path=getTreePath(SECTION."_categories",$_REQUEST['idcat']," - ");
	    $name=!empty($path)?$path.' - '.$_REQUEST['name']:$_REQUEST['name'];
	    A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$id,'name'=>$name,'content'=>$_REQUEST['content'],'tags'=>$_REQUEST['tags']));
	  }

	  self::updateCategoryItems($_REQUEST['idcat']);

	  unset($_POST['idcat']);
	  return $id;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Изменение записи.
 */

  function EditItem()
  {
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
    $_REQUEST['idcat']=(integer)$_REQUEST['idcat'];
	$_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION."_catalog","idcat=".(integer)$_REQUEST['idcat']." AND id<>".(integer)$_REQUEST['id']);
	$_REQUEST['active']=isset($_REQUEST['active'])?"Y":"N";
	$_REQUEST['keywords']=getkeywords($_REQUEST['content']);
	if(A::$OPTIONS['autoanons'])
	$_REQUEST['description']=truncate($_REQUEST['content'],A::$OPTIONS['anonslen']);
	if(!A::$OPTIONS['usedate'])
	$_REQUEST['date']=time();
	$_REQUEST['mdate']=time();

    $dataset = new A_DataSet(SECTION."_catalog",true);
    $dataset->fields=array("date","mdate","idcat","name","urlname","description","keywords","content","tags","active");

    if($row=$dataset->Update())
	{
	  if($_REQUEST['active']=='Y')
	  { $path=getTreePath(SECTION."_categories",$_REQUEST['idcat']," - ");
	    $name=!empty($path)?$path.' - '.$_REQUEST['name']:$_REQUEST['name'];
	    A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$row['id'],'name'=>$name,'content'=>$_REQUEST['content'],'tags'=>$_REQUEST['tags']));
	  }
	  else
	  A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$row['id']));

	  if($_REQUEST['idcat']!=$row['idcat'])
	  { self::updateCategoryItems($_REQUEST['idcat']);
	    self::updateCategoryItems($row['idcat']);
	  }

	  unset($_POST['idcat']);
	  return true;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Удаление записи.
 *
 * @param integer $id=0 Идентификатор записи.
 */

  function DelItem($id=0)
  {
	if($id>0)
	$_REQUEST['id']=$id;

	$dataset = new A_DataSet(SECTION."_catalog",true);
    if($row=$dataset->Delete())
	{ DelRegSectionItemImages(SECTION_ID,$row['id']);
	  DelRegSectionItemFiles(SECTION_ID,$row['id']);

	  A::$DB->execute("DELETE FROM ".DOMAIN."_comments WHERE idsec=".SECTION_ID." AND iditem=".$row['id']);
	  A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$row['id']));

	  if($id==0)
	  self::updateCategoryItems($row['idcat']);

	  return true;
	}
	else
	return false;
  }

/**
 * Обновление количества записей в категории.
 *
 * @param integer $idcat Идентификатор категории.
 */

  function updateCategoryItems($idcat)
  { static $cache=array();
    if($idcat>0 && !isset($cache[$idcat]))
	if($category=A::$DB->getRowById($idcat,SECTION."_categories"))
    { $count=A::$DB->getCount(SECTION."_catalog","idcat={$idcat} AND active='Y'");
      $subcount=A::$DB->getOne("SELECT SUM(citems) FROM ".SECTION."_categories WHERE idker={$idcat} AND active='Y'");
      $citems=$count+$subcount;
      $sub=$citems-$category['citems'];
      A::$DB->Update(SECTION."_categories",array('citems'=>$citems),"id=".$category['id']);
      while($category['idker']>0)
      if($category=A::$DB->getRowById($category['idker'],SECTION."_categories"))
      A::$DB->Update(SECTION."_categories",array('citems'=>$category['citems']+$sub),"id=".$category['id']);
      $cache[$idcat]=true;
    }
  }

/**
 * Обработчик события: Удаление ветки категорий.
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

  function DeleteCategory($section,$params)
  {
    @set_time_limit(0);
	A::$DB->query("SELECT id FROM ".SECTION."_catalog WHERE idcat=".$params['id']);
    while($row=A::$DB->fetchRow())
    self::DelItem($row['id']);
    A::$DB->free();
    self::updateCategoryItems($params['idker']);
  }

/**
 * Обработчик события: Перемещение категории.
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

  function MoveCategory($section,$params)
  {
	self::updateCategoryItems($params['idker']);
	self::updateCategoryItems($params['idto']);
  }

/**
 * Обработчик события: Изменение активности категории.
 *
 * @param string $section Полный строковой идентификатор раздела.
 * @param array $params Параметры события.
 */

  function ActiveCategory($section,$params)
  {
	$idcat=!empty($params['id'])?(integer)$params['id']:0;
	$cats=array($idcat);
	if($idcat>0)
	{ getTreeSubItems(SECTION."_categories",$idcat,$cats);
	  $active=!empty($params['active'])&&$params['active']=='N'?'N':'Y';
	  $ids=A::$DB->getCol("SELECT id FROM ".SECTION."_catalog WHERE idcat IN(".implode(",",$cats).")");
	  foreach($ids as $id)
	  { A::$DB->execute("UPDATE ".SECTION."_catalog SET active='{$active}' WHERE id=$id");
	    if($active=='N')
	    A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$id));
	  }
    }
	foreach($cats as $idcat)
	self::updateCategoryItems($idcat);
  }

/**
 * Обработчик действия: Включение группы записей.
 */

  function SetActive()
  {
    if(!empty($_REQUEST['checkitem']))
	{ $cats=array();
	  foreach($_REQUEST['checkitem'] as $id)
	  if($row=A::$DB->getRowById($id,SECTION."_catalog"))
	  { A::$DB->execute("UPDATE ".SECTION."_catalog SET active='Y' WHERE id=".(integer)$id);
	    $cats[$row['idcat']]=true;
	  }
	  $cats=array_keys($cats);
	  foreach($cats as $idcat)
	  self::updateCategoryItems($idcat);
	}
	return true;
  }

/**
 * Обработчик действия: Выключение группы записей.
 */

  function SetUnActive()
  {
    if(!empty($_REQUEST['checkitem']))
	{ $cats=array();
	  foreach($_REQUEST['checkitem'] as $id)
	  if($row=A::$DB->getRowById($id,SECTION."_catalog"))
	  { A::$DB->execute("UPDATE ".SECTION."_catalog SET active='N' WHERE id=".(integer)$id);
	    A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>(integer)$id));
	    $cats[$row['idcat']]=true;
	  }
	  $cats=array_keys($cats);
	  foreach($cats as $idcat)
	  self::updateCategoryItems($idcat);
	}
	return true;
  }

/**
 * Обработчик действия: Удаление группы записей.
 */

  function Delete()
  {
    if(!empty($_REQUEST['checkitem']))
	{ $cats=array();
	  foreach($_REQUEST['checkitem'] as $id)
	  if($row=A::$DB->getRowById($id,SECTION."_catalog"))
	  { $this->DelItem($row['id']);
	    $cats[$row['idcat']]=true;
	  }
	  $cats=array_keys($cats);
	  foreach($cats as $idcat)
	  self::updateCategoryItems($idcat);
	}
	return true;
  }

/**
 * Обработчик действия: Перемещение группы записей.
 */

  function MoveItems()
  {
	$idto=(integer)$_REQUEST['idto'];
	$cats=array();

	if(!empty($_REQUEST['checkitem']))
	foreach($_REQUEST['checkitem'] as $id)
	if($data=A::$DB->getRowById($id,SECTION."_catalog"))
	{ if($data['idcat']==$idto) continue;

	  $update=array();
	  $update['idcat']=$idto;
	  $update['urlname']=getURLName($data['name'],$data['urlname'],SECTION."_catalog","idcat=$idto");
	  A::$DB->Update(SECTION."_catalog",$update,"id=".(integer)$id);

	  $path=getTreePath(SECTION."_categories",$idto," - ");
	  $name=!empty($path)?$path.' - '.$data['name']:$data['name'];

	  if($data['active']=='Y')
	  A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$id,'name'=>$name,'content'=>$data['content'],'tags'=>$data['tags']));

	  $cats[$data['idcat']]=true;
	}

	$cats=array_keys($cats);
    foreach($cats as $idcat)
	self::updateCategoryItems($idcat);
	self::updateCategoryItems($idto);

	return true;
  }

/**
 * Обработчик действия: Сохранение групповых значений.
 */

  function Save()
  {
	$sort=A_Session::get(SECTION."_sort",isset($_COOKIE[SECTION.'_sort'])?$_COOKIE[SECTION.'_sort']:A::$OPTIONS['sort']);
	if(isset($_REQUEST['edititem']))
    foreach($_REQUEST['edititem'] as $id)
	if($row=A::$DB->getRowById($id,SECTION."_catalog"))
	{ $data=array();
	  if($sort=='sort')
	  $data['sort']=isset($_REQUEST['sort_'.$id])?(integer)$_REQUEST['sort_'.$id]:0;
	  if(isset($_REQUEST['vote_'.$id]))
	  { $vote=$row['cvote']>0?round($row['svote']/$row['cvote'],2):0;
	    $_REQUEST['vote_'.$id]=(float)$_REQUEST['vote_'.$id];
	    if($_REQUEST['vote_'.$id]!=$vote)
		{ if(empty($_REQUEST['vote_'.$id]))
		  { $data['cvote']=0;
	        $data['svote']=0;
		  }
		  else
		  { for($cvote=1;$cvote<100;$cvote++)
		    { $svote=$_REQUEST['vote_'.$id]*$cvote;
		      if($svote==round($svote,0))
			  { $data['cvote']=$cvote;
		        $data['svote']=$svote;
			    break;
			  }
		    }
		  }
		}
	  }
	  A::$DB->Update(SECTION."_catalog",$data,"id=".(integer)$id);
	}
	return true;
  }

/**
 * Формирование условия SQL запроса по текущим фильтрам.
 *
 * @return string
 */

  function getFilter()
  {
	$data=A_Session::get(SECTION."_filter",array());
	$where=array();
    if($data['active'])
	{ if(!empty($data['name']))
	  $where[]="c.name LIKE '%{$data['name']}%'";
	  if(!empty($data['content']))
	  $where[]="c.content LIKE '%{$data['content']}%'";
	  if($data['date'])
	  $where[]="c.date>={$data['date1']} AND c.date<={$data['date2']}";
	  if(!empty($data['status']))
	  $where[]="c.active='{$data['status']}'";
	  $where=$this->adminfilter(implode(" AND ",$where),$data,'c.');
	}
	return $where;
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	if(!A_Session::is_set(SECTION."_filter"))
    $this->unFilter();

	$categories=A::$DB->getAll("SELECT id,idker,name FROM ".SECTION."_categories ORDER BY level,sort");
	$this->Assign("categories",$categories);

	if(!empty($_GET['idcat']))
	{ $this->Assign("category",A::$DB->getRowById($_GET['idcat'],SECTION."_categories"));
	  $idcat=(integer)$_GET['idcat'];
	  $childcats=array($idcat);
	  getTreeSubItems(SECTION."_categories",$idcat,$childcats);
	  $where="c.idcat IN(".implode(",",$childcats).")";
	  $this->Assign("childcats",$childcats=count($childcats));
	}
	else
	$where="";

	if($filter=$this->getFilter())
	$where=!empty($where)?"{$where} AND {$filter}":$filter;

	$rows=(integer)A_Session::get(SECTION."_rows",isset($_COOKIE[SECTION.'_rows'])?$_COOKIE[SECTION.'_rows']:10);
	$sort=escape_order_string(A_Session::get(SECTION."_sort",isset($_COOKIE[SECTION.'_sort'])?$_COOKIE[SECTION.'_sort']:A::$OPTIONS['sort']));

	$items=array();
	$pager = new A_Pager($rows);
	$pager->tab="items";
    $pager->query("
	SELECT c.*,c.svote/c.cvote AS vote,cc.name AS category
	FROM ".SECTION."_catalog AS c
	LEFT JOIN ".SECTION."_categories AS cc ON cc.id=c.idcat
	".(!empty($where)?" WHERE $where":"")."
	ORDER BY $sort");
    while($row=$pager->fetchRow())
    { $row['link']=catalog_createItemLink($row['id'],SECTION);
      $row['vote']=round($row['vote'],2);
	  if(A::$OPTIONS['useimages'])
	  { $row['images']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_images WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$row['id']));
	    $row['idimg']=isset($row['images'][0]['id'])?$row['images'][0]['id']:0;
	  }
	  if(A::$OPTIONS['usefiles'])
	  { $row['files']=A::$DB->getAll("SELECT * FROM ".DOMAIN."_files WHERE idsec=? AND iditem=? ORDER BY sort",array(SECTION_ID,$row['id']));
	    $row['idfile']=isset($row['files'][0]['id'])?$row['files'][0]['id']:0;
	  }
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  if(empty($_GET['idcat']) || $childcats>1)
	  $row['catpath']=getTreePath(SECTION."_categories",$row['idcat']);
	  $items[]=$row;
	}
	$pager->free();

	$this->Assign("items",$items);
	$this->Assign("items_pager",$pager);

	$this->Assign("treebox",new A_CategoriesTree("items"));

	if(A::$OPTIONS['usecomments'])
	$this->Assign("commbox",new A_CommentsEditor(SECTION."_catalog"));

	$this->Assign("optbox1",new A_OptionsBox("Внешний вид на сайте:",array('idgroup'=>1)));
	$this->Assign("optbox2",new A_OptionsBox("Файлы:",array('idgroup'=>2)));
	$this->Assign("optbox3",new A_OptionsBox("Комментирование и голосование:",array('idgroup'=>3)));
	$this->Assign("optbox4",new A_OptionsBox("Дополнительно:",array('idgroup'=>4)));

	$this->Assign("fieldsbox",new A_FieldsEditor(SECTION."_catalog",array('tab'=>'opt','tab_opt'=>'fields'),false,true));

	$this->Assign("rows",$rows);
	$this->Assign("sort",$sort);
	$this->Assign("filter",!empty($filter));
  }
}

A::$OBSERVER->AddHandler('DeleteCategory',array('CatalogModule_Admin','DeleteCategory'));
A::$OBSERVER->AddHandler('MoveCategory',array('CatalogModule_Admin','MoveCategory'));
A::$OBSERVER->AddHandler('ActiveCategory',array('CatalogModule_Admin','ActiveCategory'));

A::$MAINFRAME = new CatalogModule_Admin;