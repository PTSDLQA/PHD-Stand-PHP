<?php
/**************************************************************************/
/* Smarty plugin
/* @copyright 2011 "Астра Вебтехнологии"
/* @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
/* @link http://a-cms.ru
 * @package Smarty
 * @subpackage plugins
/**************************************************************************/

function smarty_function_treeselect($params, &$smarty)
{
    $id='treecat';
	$emptytxt='';
    $name="";
    $width="40%";
	$title="";
	$default="Не выбрано";
	$onclick="modal_treeview_select";
	$onchange="";
	$section=ITEM;

    foreach($params as $_key=>$_value)
	{   switch ($_key) {
		    case 'id':
            case 'items':
			case 'selected':
			case 'emptytxt':
			case 'default':
			case 'width':
			case 'name':
			case 'title':
			case 'onclick':
			case 'onchange':
			case 'section':
				$$_key = $_value;
                break;
			case 'generate':
			case 'imgBase':
			case 'openAtLoad':
			case 'cookies':
			case 'ondrop':
			case 'multiline':
			case 'defaultImg':
			case 'defaultImgSelected':
			case 'defaultImgOpen':
			case 'defaultImgClose':
			case 'defaultImgCloseSelected':
			case 'defaultImgOpenSelected':
			case 'rtlMode':
			case 'dropALT':
			case 'checkboxesThreeState':
			case 'behaviourDrop':
			case 'onOpenPopulate':
				$attributes[$_key] = (string)$_value;
                break;
        }
    }

   	$attributes = array(
		'generate'=>"true",
		'imgBase' => "'/templates/admin/images/tree/'",
		'openAtLoad'=>"false",
		'cookies'=>"true",
		'multiline'=>"true",
		'defaultImg'=>"'folder.gif'",
		'defaultImgSelected'=>"'folder.gif'",
		'defaultImgOpen'=>"'folderopen.gif'",
		'defaultImgClose'=>"'folder.gif'",
		'defaultImgCloseSelected'=>"'folder.gif'",
		'defaultImgOpenSelected'=>"'folderopen.gif'",
		'rtlMode'=>"false",
		'dropALT'=>"false",
		'checkboxes'=>"false",
		'checkboxesThreeState'=>"false",
		'behaviourDrop'=>"'child'",
		'onOpenPopulate'=>"[treeview_open,'/request.php?mode=object&item=tree&action=getbranch&section={$section}&authcode=".A::$AUTH->authcode."']");

	if(empty($selected))
	$selected=0;
	if(empty($name))
	$name=$id;

	include_once('system/libs/tree.php');

	$tree = new TafelTree($id);
	if(!empty($emptytxt))
	{ $tree->addBranch("{$id}0",preg_replace("/[^a-zA-Zа-яА-Я0-9 ]+/iu"," ",$emptytxt),array('onclick'=>$onclick));
	  $seltext=$emptytxt;
	}

	$_items=array();
	foreach($items as $item)
	{ if($selected && $item['id']==$selected)
	  $seltext=$item['name'];
	  if(isset($_items[$item['idker']]))
	  $_items[$item['idker']]['child']++;
	  elseif($item['idker']==0)
	  { $_items[$item['id']]=$item;
	    $_items[$item['id']]['child']=0;
	  }
	}
	foreach($_items as $item)
	{ $options=array('onclick'=>$onclick);
	  if($item['child']>0)
	  $options['canhavechildren']='true';
	  $tree->addBranch("{$id}{$item['id']}",preg_replace("/[^a-zA-Zа-яА-Я0-9 ]+/iu"," ",$item['name']),$options);
	}

	if(!isset($seltext))
	$seltext=$default;

	$html='<input type="hidden" id="'.$id.'_value" name="'.$name.'" value="'.(integer)$selected.'">';
    $html.='<input type="text" id="'.$id.'_txt" name="'.$name.'_txt" value="'.htmlspecialchars($seltext).'" readonly="readonly" style="width:'.$width.'" onchange="'.$onchange.'"/>&nbsp;';
	$html.='<img id="'.$id.'_img" src="/templates/admin/images/tree.gif" width="16" height="16" alt="Выбрать" style="vertical-align:middle;cursor:pointer;" onclick="modal_treeview(\''.$id.'\',\''.$title.'\','.str_replace('"','\'',$tree->getJSON()).','.treeselect_tojson($attributes).');"/>';

	return $html;
}

function treeselect_tojson($data)
{ foreach ($data as $k => $i)
  $_data[] = "'".$k."':".$i;
  return "{".implode(",",$_data)."}";
}