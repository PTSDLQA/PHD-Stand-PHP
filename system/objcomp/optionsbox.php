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

require_once("system/framework/comp.php");

/**
 * Компонент "редактор опций".
 */

class A_OptionsBox extends A_Component
{
/**
 * Заголовок группы опций.
 */

  protected $title;

/**
 * Тип опций.
 */

  protected $type;

/**
 * Идентификатор группы.
 */

  protected $idgroup=0;

/**
 * Конструктор.
 *
 * @param string $title Заголовок группы опций.
 * @param array $params Список параметров, обязательный idgroup.
 */

  function __construct($title,$params)
  {
    $this->title=$title;

	if(MODE=='sections' || MODE=='structures')
	$this->type=MODE;
	else
	$this->type='global';

	foreach($params as $key=>$value)
	switch($key)
	{ case 'idgroup': $this->idgroup=$value; break;
	  case 'tab':  A::$MAINFRAME->AddJVar("opt_tab",$value); break;
	}

    A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/optionsbox.js");
	A::$MAINFRAME->AddJScript("/system/objcomp/jscripts/tpleditor.js");

	parent::__construct("optionsbox.tpl");
  }

/**
 * Формирование данных шаблона.
 */

  function createData()
  {
    $grid = new A_Grid(2);
    $grid->headers=array("Параметр","Значение");
    $grid->width=array("40%","60%");
    $grid->title=$this->title;

	switch($this->type)
	{ case "global": $item=""; break;
	  case "structures": $item=STRUCTURE; break;
	  case "sections": $item=SECTION; break;
	  default: return;
	}

	A::$DB->query("SELECT * FROM mysite_options WHERE item='{$item}' AND idgroup={$this->idgroup} ORDER BY id");

	while($row=A::$DB->fetchRow())
    if(($row['superonly']=='N' || A::$AUTH->isSuperAdmin()) && ($row['var']!='usetags' || getSectionByModule('search')))
	{ $grow[0]=AddLink($row['name'],"javascript:geteditoptform({$row['id']},$this->idgroup)","Редактировать значение");
	  switch($row['type'])
	  { case 'string':
	      $grow[1]=AddDiv("opt_{$this->idgroup}_{$row['id']}",empty($row['value'])?"&nbsp;":htmlspecialchars($row['value']));
		  break;
	    case 'int':
		  $grow[1]=AddDiv("opt_{$this->idgroup}_{$row['id']}",$row['value']);
		  break;
		case 'bool':
		  $grow[1]=AddDiv("opt_{$this->idgroup}_{$row['id']}",$row['value']==1?AddImage("/templates/admin/images/checked.gif",16,16,"Включено"):AddImage("/templates/admin/images/unchecked.gif",16,16,"Выключено"));
		  break;
		case 'select':
		  if(!empty($row['options']))
		  { $selectvars=unserialize($row['options']);
		    $grow[1]=AddDiv("opt_{$this->idgroup}_{$row['id']}",$selectvars[$row['value']]);
		  }
		  break;
		case 'date':
		  $grow[1]=AddDiv("opt_{$this->idgroup}_{$row['id']}",date("d.m.Y",$row['value']));
		  break;
		case 'mailtpl':
		  if(!empty($row['value']))
		  $value="<a href=\"javascript:edittpl('mails/{$row['value']}')\" title=\"Редактировать шаблон\">".htmlspecialchars($row['value'])."</a>";
		  else
		  $value="&nbsp;";
		  $grow[1]=AddDiv("opt_{$this->idgroup}_{$row['id']}",$value);
		  break;
		case 'othertpl':
		  if(!empty($row['value']))
		  $value="<a href=\"javascript:edittpl('others/{$row['value']}')\" title=\"Редактировать шаблон\">".htmlspecialchars($row['value'])."</a>";
		  else
		  $value="&nbsp;";
		  $grow[1]=AddDiv("opt_{$this->idgroup}_{$row['id']}",$value);
		  break;
	  }
      $grid->AddRow($grow);
    }
    if($grid->rows>0)
	$this->Assign("options_grid",$grid);
  }

/**
 * Возвращает сгенерированный HTML код компонента.
 *
 * @param string $title='' Заголовок группы опций.
 * @return string
 */

  function getContent($title="")
  {
    if(!empty($title))
	$this->title=$title;

	return parent::getContent();
  }
}