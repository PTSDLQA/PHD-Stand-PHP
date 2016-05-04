<?php
/** \file system/framework/form.php
 * Форма HTML.
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
 * Класс формы. Как правило используется только в панели управления.
 */

class A_Form extends Smarty
{
/**
 * Шаблон формы.
 */

  public $template;

/**
 * Массив данных, который в шаблоне становится доступным в переменной $form.
 */

  public $data=array();

/**
 * Конструктор.
 *
 * @param string $template Шаблон формы.
 */

  function __construct($template)
  {
    $this->template_dir=SMARTY_TEMPLATES."/forms/";
	$this->compile_dir=SMARTY_COMPILE."/forms/";

	$this->template=$template;

	A::$OBSERVER->Event('CreateForm',$template,array('object'=>&$this));
  }

/**
 * Дополняет данные формы массивом с информацией о дополнительных полях (для форм добавления записи).
 */

  function fieldseditor_addprepare()
  {
    $this->data['fields']=array();
    $item=MODE=='sections'?SECTION:STRUCTURE;
    A::$DB->query("SELECT * FROM ".DOMAIN."_fields WHERE item='$item' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $row['name']=$row['name_ru'];
	  if($row['type']=="bool")
      $row['value']=$row['property']?'Y':'N';
      else
      $row['value']="";
      $this->data['fields'][]=$row;
    }
    A::$DB->free();
  }

/**
 * Дополняет данные формы массивом с информацией о дополнительных полях (для форм редактирования записи).
 */

  function fieldseditor_editprepare()
  {
    $this->data['fields']=array();
    $item=MODE=='sections'?SECTION:STRUCTURE;
    A::$DB->query("SELECT * FROM ".DOMAIN."_fields WHERE item='$item' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { $row['value']=$this->data[$row['field']];
	  if($row['type']=="float")
      $row['value']=round($row['value'],2);
      $row['name']=$row['name_ru'];
      $this->data['fields'][]=$row;
    }
    A::$DB->free();
  }

/**
 * Дополняет данные формы массивом с информацией о дополнительных полях (для форм фильтрации записей).
 */

  function fieldseditor_filterprepare(&$data)
  {
	$this->data['fields']=array();
    $item=MODE=='sections'?SECTION:STRUCTURE;
    A::$DB->query("SELECT * FROM ".DOMAIN."_fields WHERE item='$item' AND search='Y' ORDER BY sort");
    while($row=A::$DB->fetchRow())
    { if($row['type']=="int" || $row['type']=="float")
      { $row['field1']=$row['field']."1";
        $row['field2']=$row['field']."2";
	    $row['value1']=isset($data[$row['field1']])?$data[$row['field1']]:"";
	    $row['value2']=isset($data[$row['field2']])?$data[$row['field2']]:"";
      }
      else
      $row['value']=isset($data[$row['field']])?$data[$row['field']]:"";
      $row['name']=$row['name_ru'];
      $this->data['fields'][]=$row;
    }
    A::$DB->free();
  }

/**
 * Переопределяемый метод формирования дополнительных данных доступных в шаблоне.
 */

  function createData()
  {
  }

/**
 * Метод возвращает сгенерированную форму.
 *
 * @return string HTML код формы.
 */

  function getContent()
  {
	$this->data=A::$OBSERVER->Modifier('prepareForm',$this->template,$this->data);

	$this->Assign_by_ref("form",$this->data);
	$this->Assign_by_ref("system",A::getSystem());
    $this->Assign_by_ref("auth",A::$AUTH);
	$this->Assign_by_ref("options",A::$OPTIONS);

	$this->createData();

	if(A_MODE==A_MODE_ADMIN)
	{ $path1=$this->template_dir.$this->template;
	  switch(MODE)
	  { case 'block': $path2='blocks/'.ITEM.'/templates/admin/forms/'.$this->template; break;
	    case 'sections': $path2='modules/'.MODULE.'/templates/admin/forms/'.$this->template; break;
		case 'structures': $path2='plugins/'.PLUGIN.'/templates/admin/forms/'.$this->template; break;
	  }
	  if(!empty($path2) && is_file($path2) && (!is_file($path1) || filemtime($path2)>filemtime($path1)))
	  copyfile($path2,$path1,true);
	}

	A::$OBSERVER->Event('ShowForm',$this->template,array('object'=>&$this));

	if(is_file($this->template_dir.$this->template))
	return $this->fetch($this->template);
	else
	return "";
  }
}