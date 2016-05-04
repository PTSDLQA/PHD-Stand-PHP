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

/**
 * Серверная сторона AJAX для компонента управления прикрепленными файлами.
 */

class A_ImagesRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
	 switch($action)
     { case "upload": $this->Upload(); break;
	   case "del": $this->Del(); break;
	   case "getcaption": $this->getCaption(); break;
	   case "save": $this->Save(); break;
	   case "up": $this->Up(); break;
	   case "down": $this->Down(); break;
	   case "tobegin": $this->toBegin(); break;
	   case "toend": $this->toEnd(); break;
	   default: $this->Refresh();
     }
  }

/**
 * Обработчик действия: Загрузка изображения.
 */

  function Upload()
  {
    $sort=A::$DB->getOne("SELECT MAX(sort) FROM mysite_images WHERE idsec=".getSectionId(SECTION)." AND iditem=".(integer)$_POST['iditem'])+1;
	$idimg=UploadImage("image",$_POST['caption'],0,(integer)$_POST['iditem'],$sort);
	$this->Refresh();
  }

/**
 * Обработчик действия: Получение описания файла.
 */

  function getCaption()
  {
    $this->RESULT['caption']=A::$DB->getOne("SELECT caption FROM mysite_images WHERE id=".(integer)$_POST['id']);
  }

/**
 * Обработчик действия: Сохранение описания файла.
 */

  function Save()
  {
    if($image=A::$DB->getRowById($_POST['id'],"mysite_images"))
	{ UploadImage("image",$_POST['caption'],$image['id'],(integer)$_POST['iditem'],$image['sort']);
	  RenameRegImage((integer)$_POST['id'],$_POST['caption']);
	}
	$this->Refresh();
  }

/**
 * Обработчик действия: Удаление изображения.
 */

  function Del()
  {
    DelRegImage((integer)$_POST['id']);
	$this->Refresh();
  }

/**
 * Обработчик действия: Перемещение на позицию выше.
 */

  function Up()
  {
   	$idsec=getSectionId(SECTION);
	$iditem=(integer)$_POST['iditem'];
    if($row=A::$DB->getRowById($_POST['id'],"mysite_images"))
	{ if($prevrow=A::$DB->getRow("SELECT id,sort FROM mysite_images WHERE idsec=$idsec AND iditem=$iditem AND sort<{$row['sort']} ORDER BY sort DESC LIMIT 0,1"))
	  { A::$DB->execute("UPDATE mysite_images SET sort={$row['sort']} WHERE id={$prevrow['id']}");
	    A::$DB->execute("UPDATE mysite_images SET sort={$prevrow['sort']} WHERE id={$row['id']}");
		A::$OBSERVER->Event('MoveImage',SECTION,$prevrow);
		A::$OBSERVER->Event('MoveImage',SECTION,$row);
		$this->Refresh();
		return true;
	  }
	}
  }

/**
 * Обработчик действия: Перемещение на позицию ниже.
 */

  function Down()
  {
   	$idsec=getSectionId(SECTION);
	$iditem=(integer)$_POST['iditem'];
	if($row=A::$DB->getRowById($_POST['id'],"mysite_images"))
	{ if($nextrow=A::$DB->getRow("SELECT id,sort FROM mysite_images WHERE idsec=$idsec AND iditem=$iditem AND sort>{$row['sort']} ORDER BY sort LIMIT 0,1"))
	  { A::$DB->execute("UPDATE mysite_images SET sort={$row['sort']} WHERE id={$nextrow['id']}");
	    A::$DB->execute("UPDATE mysite_images SET sort={$nextrow['sort']} WHERE id={$row['id']}");
	    A::$OBSERVER->Event('MoveImage',SECTION,$row);
	    A::$OBSERVER->Event('MoveImage',SECTION,$nextrow);
		$this->Refresh();
		return true;
	  }
	}
  }

/**
 * Обработчик действия: Перемещение на первую позицию.
 */

  function toBegin()
  {
   	$idsec=getSectionId(SECTION);
	$iditem=(integer)$_POST['iditem'];
	if($row=A::$DB->getRowById($_POST['id'],"mysite_images"))
	{ if($frow=A::$DB->getRow("SELECT id,sort FROM mysite_images WHERE idsec=$idsec AND iditem=$iditem ORDER BY sort LIMIT 0,1"))
	  { A::$DB->execute("UPDATE mysite_images SET sort={$frow['sort']}-1 WHERE id={$row['id']}");
	    A::$OBSERVER->Event('MoveImage',SECTION,$row);
	    $this->Refresh();
	    return true;
	  }
	}
  }

/**
 * Обработчик действия: Перемещение на последнюю позицию.
 */

  function toEnd()
  {
   	$idsec=getSectionId(SECTION);
	$iditem=(integer)$_POST['iditem'];
	if($row=A::$DB->getRowById($_POST['id'],"mysite_images"))
	{ if($lrow=A::$DB->getRow("SELECT id,sort FROM mysite_images WHERE idsec=$idsec AND iditem=$iditem ORDER BY sort DESC LIMIT 0,1"))
	  { A::$DB->execute("UPDATE mysite_images SET sort={$lrow['sort']}+1 WHERE id={$row['id']}");
	    A::$OBSERVER->Event('MoveImage',SECTION,$row);
	    $this->Refresh();
		return true;
	  }
	}
  }

/**
 * Формирование таблицы со списком файлов.
 */

  function Refresh()
  {
   	$section_id=getSectionId(SECTION);
	$iditem=(integer)$_POST['iditem'];

	$minsort=A::$DB->getOne("SELECT MIN(sort) FROM mysite_images WHERE idsec=$section_id AND iditem=$iditem");
	$maxsort=A::$DB->getOne("SELECT MAX(sort) FROM mysite_images WHERE idsec=$section_id AND iditem=$iditem");

	$rows=!empty($_POST['rows'])?(integer)$_POST['rows']:3;

	$grid = new A_Grid(6,"images_grid.tpl");
	$grid->headers=array("&nbsp;","Размер","Описание","&nbsp;","&nbsp;","&nbsp;");
	$grid->width=array(80,70,"",40,40,20);
	$grid->align=array("center","left","left","center","center","center");

	$pager = new A_Pager($rows,"images_gopage");
	$pager->query("SELECT * FROM mysite_images WHERE idsec=$section_id AND iditem=$iditem ORDER BY sort");
	while($row=$pager->fetchRow())
	{ $grow=array();
	  $src="/image.php?src=".urlencode(preg_replace("/^\//i","",$row['path']))."&x=80&y=50&b=4";
	  $src2="/".preg_replace("/^\//i","",$row['path']);
	  $grow[0]=AddImageButton($src,"open_imgwindow('$src2','{$row['caption']}',{$row['width']},{$row['height']})","Увеличить",0,50);
	  $grow[1]=$row['width'].'X'.$row['height'];
	  $grow[2]=AddClickText($row['caption'],"images_edit({$row['id']})","Редактировать");
	  $grow[3]=$grow[4]="";
	  if($row['sort']>$minsort)
	  { $grow[3].=AddImageButton("/templates/admin/images/up.gif","images_sort({$row['id']},'up')","Выше",16,16);
	    $grow[4].=AddImageButton("/templates/admin/images/tobegin.gif","images_sort({$row['id']},'tobegin')","В Начало",16,16);
	  }
	  if($row['sort']<$maxsort)
	  { $grow[3].=AddImageButton("/templates/admin/images/down.gif","images_sort({$row['id']},'down')","Ниже",16,16);
	    $grow[4].=AddImageButton("/templates/admin/images/toend.gif","images_sort({$row['id']},'toend')","В конец",16,16);
	  }
	  $grow[5]=AddImageButton("/templates/admin/images/del.gif","images_del({$row['id']})","Удалить",16,16);
	  $grid->AddRow($grow);
	}
	$pager->free();

	$this->RESULT['html']=$grid->rows>0?$grid->getContent().$pager->getContent():AddBox("Нет изображений");
  }
}

A::$REQUEST = new A_ImagesRequest;