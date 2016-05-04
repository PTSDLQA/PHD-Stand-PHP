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
 * Панель управления модуля "Фотогалерея".
 *
 * <a href="http://wiki.a-cms.ru/modules/gallery">Руководство</a>.
 */

class GalleryModule_Admin extends A_MainFrame
{
/**
 * Конструктор.
 */

  function __construct()
  {
    parent::__construct("module_gallery.tpl");

	$this->AddJScript("/modules/gallery/admin/gallery.js");
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
	  case "setactive": $res=$this->SetActive(); break;
	  case "setunactive": $res=$this->SetUnActive(); break;
	  case "delete": $res=$this->Delete(); break;
	  case "moveitems": $res=$this->MoveItems(); break;
	  case "save": $res=$this->Save(); break;
	  case "addimage": $res=$this->AddImage(); break;
	  case "isetmain": $res=$this->SetMainImage(); break;
	  case "idelete": $res=$this->DeleteImages(); break;
	  case "isave": $res=$this->SaveImages(); break;
	}
	if(!empty($res))
	A::goUrl("admin.php?mode=sections&item=".SECTION,array('idcat','idalb','tab','tab_albums','page'));
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
 * Обработчик действия: Добавление альбома.
 */

  function AddItem()
  {
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['idcat']=(integer)$_REQUEST['idcat'];
    $_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION."_albums","idcat=".(integer)$_REQUEST['idcat']);
	$_REQUEST['active']=isset($_REQUEST['active'])?"Y":"N";
	$_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".SECTION."_albums")+1;
	if(!A::$OPTIONS['usedate'])
	$_REQUEST['date']=time();

    $dataset = new A_DataSet(SECTION."_albums",true);
    $dataset->fields=array("date","idcat","name","tags","urlname","description","active","sort");

    if($id=$dataset->Insert())
	{
	  $path=getTreePath(SECTION."_categories",$_REQUEST['idcat']," - ");
	  $name=!empty($path)?$path.' - '.$_REQUEST['name']:$_REQUEST['name'];

	  if($_REQUEST['active']=='Y')
	  A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$id,'name'=>$name,'content'=>$_REQUEST['description'],'tags'=>$_REQUEST['tags']));

	  $this->updateCategoryItems($_REQUEST['idcat']);

	  unset($_POST['idcat']);
	  $_GET['idalb']=$id;
	  $_GET['tab_albums']='images';

	  return $id;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Изменение альбома.
 */

  function EditItem()
  {
	$_REQUEST['name']=strip_tags($_REQUEST['name']);
	$_REQUEST['idcat']=(integer)$_REQUEST['idcat'];
    $_REQUEST['urlname']=getURLName($_REQUEST['name'],$_REQUEST['urlname'],SECTION."_albums","idcat=".(integer)$_REQUEST['idcat']." AND id<>".(integer)$_REQUEST['id']);
	$_REQUEST['active']=isset($_REQUEST['active'])?"Y":"N";
	if(!A::$OPTIONS['usedate'])
	$_REQUEST['date']=time();

    $dataset = new A_DataSet(SECTION."_albums",true);
    $dataset->fields=array("date","idcat","name","tags","urlname","description","active");

    if($row=$dataset->Update())
	{
	  $path=getTreePath(SECTION."_categories",$_REQUEST['idcat']," - ");
	  $name=!empty($path)?$path.' - '.$_REQUEST['name']:$_REQUEST['name'];

	  if($_REQUEST['active']=='Y')
	  A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$row['id'],'name'=>$name,'content'=>$_REQUEST['description'],'tags'=>$_REQUEST['tags']));
	  else
	  A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$row['id']));

	  if($_REQUEST['idcat']!=$row['idcat'])
	  { $this->updateCategoryItems($_REQUEST['idcat']);
	    $this->updateCategoryItems($row['idcat']);
	  }

	  unset($_POST['idcat']);
	  return true;
	}
	else
	return false;
  }

/**
 * Обработчик действия: Удаление альбома.
 *
 * @param integer $id=0 Идентификатор альбома.
 */

  function DelItem($id=0)
  {
	if($id>0)
	$_REQUEST['id']=$id;

    $dataset = new A_DataSet(SECTION."_albums",true);
    if($row=$dataset->Delete())
	{
	  DelRegSectionItemImages(SECTION_ID,$row['id']);

	  A::$DB->execute("DELETE FROM ".DOMAIN."_comments WHERE idsec=".SECTION_ID." AND iditem=".$row['id']);
	  A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$row['id']));

	  if($id==0)
	  $this->updateCategoryItems($row['idcat']);

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
    { $count=A::$DB->getCount(SECTION."_albums","idcat={$idcat} AND active='Y'");
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
	A::$DB->query("SELECT id FROM ".SECTION."_albums WHERE idcat=".$params['id']);
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
	  $ids=A::$DB->getCol("SELECT id FROM ".SECTION."_albums WHERE idcat IN(".implode(",",$cats).")");
	  foreach($ids as $id)
	  { A::$DB->execute("UPDATE ".SECTION."_albums SET active='{$active}' WHERE id=$id");
	    if($active=='N')
	    A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>$id));
	  }
    }
	foreach($cats as $idcat)
	self::updateCategoryItems($idcat);
  }

/**
 * Обработчик действия: Включение группы альбомов.
 */

  function SetActive()
  {
    if(!empty($_REQUEST['checkitem']))
	{ $cats=array();
	  foreach($_REQUEST['checkitem'] as $id)
	  if($row=A::$DB->getRowById($id,SECTION."_albums"))
	  { A::$DB->execute("UPDATE ".SECTION."_albums SET active='Y' WHERE id=".(integer)$id);
	    $cats[$row['idcat']]=true;
	  }
	  $cats=array_keys($cats);
	  foreach($cats as $idcat)
	  $this->updateCategoryItems($idcat);
	}
	return true;
  }

/**
 * Обработчик действия: Выключение группы альбомов.
 */

  function SetUnActive()
  {
    if(!empty($_REQUEST['checkitem']))
	{ $cats=array();
	  foreach($_REQUEST['checkitem'] as $id)
	  if($row=A::$DB->getRowById($id,SECTION."_albums"))
	  { A::$DB->execute("UPDATE ".SECTION."_albums SET active='N' WHERE id=".(integer)$id);
	    A::$OBSERVER->Event('searchIndexDelete',SECTION,array('id'=>(integer)$id));
	    $cats[$row['idcat']]=true;
	  }
	  $cats=array_keys($cats);
	  foreach($cats as $idcat)
	  $this->updateCategoryItems($idcat);
	}
	return true;
  }

/**
 * Обработчик действия: Удаление группы альбомов.
 */

  function Delete()
  {
    if(!empty($_REQUEST['checkitem']))
	{ $cats=array();
	  foreach($_REQUEST['checkitem'] as $id)
	  if($row=A::$DB->getRowById($id,SECTION."_albums"))
	  { $this->DelItem($id);
	    $cats[$row['idcat']]=true;
	  }
	  $cats=array_keys($cats);
	  foreach($cats as $idcat)
	  $this->updateCategoryItems($idcat);
	}
	return true;
  }

/**
 * Обработчик действия: Перемещение группы альбомов.
 */

  function MoveItems()
  {
	$idto=(integer)$_REQUEST['idto'];
	$cats=array();

	if(!empty($_REQUEST['checkitem']))
	foreach($_REQUEST['checkitem'] as $id)
	if($data=A::$DB->getRowById($id,SECTION."_albums"))
	{ if($data['idcat']==$idto) continue;

	  $update=array();
	  $update['idcat']=$idto;
	  $update['urlname']=getURLName($data['name'],$data['urlname'],SECTION."_albums","idcat=$idto");
	  A::$DB->Update(SECTION."_albums",$update,"id=".(integer)$id);

	  $path=getTreePath(SECTION."_categories",$idto," - ");
	  $name=!empty($path)?$path.' - '.$data['name']:$data['name'];

	  if($data['active']=='Y')
	  A::$OBSERVER->Event('searchIndexUpdate',SECTION,array('id'=>$id,'name'=>$name,'content'=>$data['description'],'tags'=>$data['tags']));

	  $cats[$data['idcat']]=true;
	}

	$cats=array_keys($cats);
    foreach($cats as $idcat)
	$this->updateCategoryItems($idcat);
	$this->updateCategoryItems($idto);

	return true;
  }

/**
 * Обработчик действия: Сохранение групповых значений.
 */

  function Save()
  {
	$sort=A_Session::get(SECTION."_sort",isset($_COOKIE[SECTION.'_sort'])?$_COOKIE[SECTION.'_sort']:"date DESC");
	if(isset($_REQUEST['edititem']))
    foreach($_REQUEST['edititem'] as $id)
	if($row=A::$DB->getRowById($id,SECTION."_albums"))
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
	  A::$DB->Update(SECTION."_albums",$data,"id=$id");
	}
	return true;
  }

/**
 * Обработчик действия: Добавление изображений в альбом.
 */

  function AddImage()
  {
	if($album=A::$DB->getRowById($_REQUEST['idalb'],SECTION."_albums"))
	{
	  $sort=A::$DB->getOne("SELECT MAX(sort) FROM ".DOMAIN."_images WHERE idsec=".SECTION_ID." AND iditem=".$album['id']);

	  for($i=1;$i<=6;$i++)
	  { $idimg=UploadImage("image$i",$_REQUEST["name$i"],0,$album['id'],$sort+$i);
        if(empty($album['idimg']))
        { A::$DB->Update(SECTION."_albums",array('idimg'=>$idimg),"id=".$album['id']);
          $album['idimg']=$idimg;
        }
	  }

	  if(extension_loaded('zip') && isset($_FILES['archzip']['tmp_name']) && file_exists($_FILES['archzip']['tmp_name']))
	  { delDir("files/".DOMAIN."/tmp");
		mk_dir("files/".DOMAIN."/tmp");
        $zip = new ZipArchive();
        $zip->open($_FILES['archzip']['tmp_name']);
        if($zip->extractTo("files/".DOMAIN."/tmp"))
        { $images=scandir("files/".DOMAIN."/tmp");
          $i=1;
		  foreach($images as $file)
          if($file!='.' && $file!='..')
		  { $idimg=RegisterImage("files/".DOMAIN."/tmp/$file",'',0,$album['id'],$sort+$i++,A::$OPTIONS['img_resize'],A::$OPTIONS['img_x'],A::$OPTIONS['img_y']);
		    if(empty($album['idimg']))
            { A::$DB->Update(SECTION."_albums",array('idimg'=>$idimg),"id=".$album['id']);
              $album['idimg']=$idimg;
            }
		  }
        }
        $zip->close();
        delDir("files/".DOMAIN."/tmp");
	  }

      if(!empty($_REQUEST['path']) && A::$AUTH->isSuperAdmin())
      { $_path=preg_replace("/^[.\/]+/i","",$_REQUEST['path']);
        if(empty($_path)) return true;
		if(A::$AUTH->isSuperAdmin())
		{ if(is_dir($_path))
		  $path=$_path;
		  elseif(is_file($_path))
		  $file=$_path;
		  elseif(is_dir("files/".DOMAIN."/".$_path))
		  $path="files/".DOMAIN."/".$_path;
		  elseif(is_file("files/".DOMAIN."/".$_path))
		  $file="files/".DOMAIN."/".$_path;
		}
		elseif(is_dir("files/".DOMAIN."/".$_path))
		$path="files/".DOMAIN."/".$_path;
		elseif(is_file("files/".DOMAIN."/".$_path))
		$file="files/".DOMAIN."/".$_path;

        if(!empty($file) && extension_loaded('zip'))
        { delDir("files/".DOMAIN."/tmp");
		  mk_dir("files/".DOMAIN."/tmp");
		  $zip = new ZipArchive();
          $zip->open($file);
          if($zip->extractTo("files/".DOMAIN."/tmp"))
          $path="files/".DOMAIN."/tmp";
          $zip->close();
        }

		if(!empty($path))
		{ $images=scandir($path);
          $i=1;
		  foreach($images as $file)
          if($file!='.' && $file!='..')
		  { $idimg=RegisterImage("$path/$file",'',0,$album['id'],$sort+$i++,A::$OPTIONS['img_resize'],A::$OPTIONS['img_x'],A::$OPTIONS['img_y']);
		    if(empty($album['idimg']))
            { A::$DB->Update(SECTION."_albums",array('idimg'=>$idimg),"id=".$album['id']);
              $album['idimg']=$idimg;
            }
		  }
		}
      }
	}
    return true;
  }

/**
 * Обработчик действия: Установка главного изображения в альбоме.
 */

  function SetMainImage()
  {
    if(!empty($_REQUEST['checkimg']) && !empty($_GET['idalb']))
    foreach($_REQUEST['checkimg'] as $id)
    { A::$DB->Update(SECTION."_albums",array('idimg'=>(integer)$id),"id=".(integer)$_GET['idalb']);
      break;
    }
    return true;
  }

/**
 * Обработчик действия: Удаление группы изображений из альбома.
 */

  function DeleteImages()
  {
    if(!empty($_REQUEST['checkimg']) && !empty($_GET['idalb']))
    if($album=A::$DB->getRowById($_GET['idalb'],SECTION."_albums"))
    foreach($_REQUEST['checkimg'] as $id)
    { DelRegImage((integer)$id);
      if($album['idimg']==$id)
      { A::$DB->Update(SECTION."_albums",array('idimg'=>0),"id=".$album['id']);
        $album['idimg']=0;
      }
    }
    return true;
  }

/**
 * Обработчик действия: Сохранение описаний для фото.
 */

  function SaveImages()
  {
	if(!empty($_REQUEST['editimg']))
    foreach($_REQUEST['editimg'] as $id)
	if(isset($_REQUEST['icaption_'.$id]))
	A::$DB->Update(DOMAIN."_images",array('caption'=>$_REQUEST['icaption_'.$id]),"id=".(integer)$id);
	return true;
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
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

	$rows=(integer)A_Session::get(SECTION."_rows",isset($_COOKIE[SECTION.'_rows'])?$_COOKIE[SECTION.'_rows']:20);
	$sort=escape_order_string(A_Session::get(SECTION."_sort",isset($_COOKIE[SECTION.'_sort'])?$_COOKIE[SECTION.'_sort']:A::$OPTIONS['sort']));

	$items=array();
	$pager = new A_Pager($rows);
	$pager->tab="albums";
    $pager->query("
	SELECT c.*,c.svote/c.cvote AS vote,cc.name AS category
	FROM ".SECTION."_albums AS c
	LEFT JOIN ".SECTION."_categories AS cc ON cc.id=c.idcat
	".(!empty($where)?" WHERE $where":"")."
	ORDER BY $sort");
    while($row=$pager->fetchRow())
    { $row['link']=gallery_createItemLink($row['id'],SECTION);
      $row['cimages']=A::$DB->getCount(DOMAIN."_images","idsec=".SECTION_ID." AND iditem=".$row['id']);
      $row['vote']=round($row['vote'],2);
	  $row['tags']=A_SearchEngine::getInstance()->convertTags($row['tags']);
	  if(empty($_GET['idcat']) || $childcats>1)
	  $row['catpath']=getTreePath(SECTION."_categories",$row['idcat']);
	  $items[]=$row;
	}
	$pager->free();

	$this->Assign("items",$items);
	$this->Assign("items_pager",$pager);

	if(!empty($_GET['idalb']))
	{ $_GET['idalb']=(integer)$_GET['idalb'];
	  $this->Assign("album",A::$DB->getRowById($_GET['idalb'],SECTION."_albums"));

	  $images=A::$DB->getAll("
	  SELECT * FROM ".DOMAIN."_images
	  WHERE idsec=".SECTION_ID." AND iditem=".$_GET['idalb']."
	  ORDER BY sort");
	  $this->Assign("images",$images);
	}

	$this->Assign("treebox",new A_CategoriesTree("albums"));

	if(A::$OPTIONS['usecomments'])
	$this->Assign("commbox",new A_CommentsEditor(SECTION."_albums"));

	$this->Assign("optbox1",new A_OptionsBox("Внешний вид на сайте:",array('idgroup'=>1)));
	$this->Assign("optbox2",new A_OptionsBox("Фото:",array('idgroup'=>2)));
	$this->Assign("optbox3",new A_OptionsBox("Комментирование и голосование:",array('idgroup'=>3)));
	$this->Assign("optbox4",new A_OptionsBox("Дополнительно:",array('idgroup'=>4)));

	$this->Assign("fieldsbox",new A_FieldsEditor(SECTION."_albums",array('tab'=>'opt','tab_opt'=>'fields')));

	$this->Assign("rows",$rows);
	$this->Assign("sort",$sort);
  }
}

A::$OBSERVER->AddHandler('DeleteCategory',array('GalleryModule_Admin','DeleteCategory'));
A::$OBSERVER->AddHandler('MoveCategory',array('GalleryModule_Admin','MoveCategory'));
A::$OBSERVER->AddHandler('ActiveCategory',array('GalleryModule_Admin','ActiveCategory'));

A::$MAINFRAME = new GalleryModule_Admin;