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

class A_FilesRequest extends A_Request
{
/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
	 switch($action)
     { case "upload": $this->Upload(); break;
	   case "register": $this->Register(); break;
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
 * Обработчик действия: Загрузка файла.
 */

  function Upload()
  {
	$sort=A::$DB->getOne("SELECT MAX(sort) FROM mysite_files WHERE idsec=".getSectionId(SECTION)." AND iditem=".(integer)$_POST['iditem'])+1;
    UploadFile("file",$_POST['caption'],0,(integer)$_POST['iditem'],$sort);
	$this->Refresh();
  }

/**
 * Обработчик действия: Регистрация файла.
 */

  function Register()
  {
    $sort=A::$DB->getOne("SELECT MAX(sort) FROM mysite_files WHERE idsec=".getSectionId(SECTION)." AND iditem=".(integer)$_POST['iditem'])+1;
    RegisterFile($_POST['path'],$_POST['caption'],0,(integer)$_POST['iditem'],$sort);
	$this->Refresh();
  }

/**
 * Обработчик действия: Получение описания файла.
 */

  function getCaption()
  {
    $this->RESULT['caption']=A::$DB->getOne("SELECT caption FROM mysite_files WHERE id=".(integer)$_POST['id']);
  }

/**
 * Обработчик действия: Сохранение описания файла.
 */

  function Save()
  {
    if($file=A::$DB->getRowById($_POST['id'],"mysite_files"))
	{ UploadFile("file",$_POST['caption'],$file['id'],(integer)$_POST['iditem'],$file['sort']);
	  RenameRegFile($file['id'],$_POST['caption']);
	}
	$this->Refresh();
  }

/**
 * Обработчик действия: Удаление файла.
 */

  function Del()
  {
    DelRegFile((integer)$_POST['id']);
	$this->Refresh();
  }

/**
 * Обработчик действия: Перемещение на позицию выше.
 */

  function Up()
  {
   	$idsec=getSectionId(SECTION);
	$iditem=(integer)$_POST['iditem'];
    if($row=A::$DB->getRowById($_POST['id'],"mysite_files"))
	{ if($prevrow=A::$DB->getRow("SELECT id,sort FROM mysite_files WHERE idsec=$idsec AND iditem=$iditem AND sort<{$row['sort']} ORDER BY sort DESC LIMIT 0,1"))
	  { A::$DB->execute("UPDATE mysite_files SET sort={$row['sort']} WHERE id={$prevrow['id']}");
	    A::$DB->execute("UPDATE mysite_files SET sort={$prevrow['sort']} WHERE id={$row['id']}");
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
	if($row=A::$DB->getRowById($_POST['id'],"mysite_files"))
	{ if($nextrow=A::$DB->getRow("SELECT id,sort FROM mysite_files WHERE idsec=$idsec AND iditem=$iditem AND sort>{$row['sort']} ORDER BY sort LIMIT 0,1"))
	  { A::$DB->execute("UPDATE mysite_files SET sort={$row['sort']} WHERE id={$nextrow['id']}");
	    A::$DB->execute("UPDATE mysite_files SET sort={$nextrow['sort']} WHERE id={$row['id']}");
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
	if($row=A::$DB->getRowById($_POST['id'],"mysite_files"))
	{ if($frow=A::$DB->getRow("SELECT id,sort FROM mysite_files WHERE idsec=$idsec AND iditem=$iditem ORDER BY sort LIMIT 0,1"))
	  { A::$DB->execute("UPDATE mysite_files SET sort={$frow['sort']}-1 WHERE id={$row['id']}");
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
	if($row=A::$DB->getRowById($_POST['id'],"mysite_files"))
	{ if($lrow=A::$DB->getRow("SELECT id,sort FROM mysite_files WHERE idsec=$idsec AND iditem=$iditem ORDER BY sort DESC LIMIT 0,1"))
	  { A::$DB->execute("UPDATE mysite_files SET sort={$lrow['sort']}+1 WHERE id={$row['id']}");
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

	$minsort=A::$DB->getOne("SELECT MIN(sort) FROM mysite_files WHERE idsec=$section_id AND iditem=$iditem");
	$maxsort=A::$DB->getOne("SELECT MAX(sort) FROM mysite_files WHERE idsec=$section_id AND iditem=$iditem");

	$rows=!empty($_POST['rows'])?(integer)$_POST['rows']:5;

	$grid = new A_Grid(8,"inform_grid.tpl");
	$grid->headers=array("Файл","Описание","Размер","Скач.","&nbsp;","&nbsp;","&nbsp;","&nbsp;");
	$grid->width=array(220,"",80,40,40,40,20,20);
	$grid->align=array("left","","left","center","center","center","center","center");

	$pager = new A_Pager($rows,"files_gopage");
	$pager->query("SELECT * FROM mysite_files WHERE idsec=$section_id AND iditem=$iditem ORDER BY sort");
	while($row=$pager->fetchRow())
	{ $grow=array();
	  $grow[0]=AddClickText(basename($row['path']),"files_edit({$row['id']})","Редактировать");
	  $grow[1]=AddClickText($row['caption'],"files_edit({$row['id']})","Редактировать");
	  $grow[2]=sizestring($row['size']);
	  $grow[3]=$row['dwnl'];
	  $grow[4]=$grow[5]="";
	  if($row['sort']>$minsort)
	  { $grow[4].=AddImageButton("/templates/admin/images/up.gif","files_sort({$row['id']},'up')","Выше",16,16);
	    $grow[5].=AddImageButton("/templates/admin/images/tobegin.gif","files_sort({$row['id']},'tobegin')","В Начало",16,16);
	  }
	  if($row['sort']<$maxsort)
	  { $grow[4].=AddImageButton("/templates/admin/images/down.gif","files_sort({$row['id']},'down')","Ниже",16,16);
	    $grow[5].=AddImageButton("/templates/admin/images/toend.gif","files_sort({$row['id']},'toend')","В конец",16,16);
	  }
	  $grow[6]=AddImageButtonLink("/templates/admin/images/save.gif","/download.php?id={$row['id']}","Скачать",16,16);
	  $grow[7]=AddImageButton("/templates/admin/images/del.gif","files_del({$row['id']})","Удалить",16,16);
	  $grid->AddRow($grow);
	}
	$pager->free();

	$this->RESULT['html']=$grid->rows>0?$grid->getContent().$pager->getContent():AddBox("Нет файлов");
  }
}

A::$REQUEST = new A_FilesRequest;