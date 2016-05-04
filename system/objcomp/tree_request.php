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

class A_TreeRequest extends A_Request
{
  function Action($action)
  {
	 switch($action)
     { case "getbranch": $this->getBranch(); break;
     }
  }

  function getBranch()
  {
	include_once('system/libs/tree.php');

    $idtree=preg_replace("/[^a-zA-Z0-9_]/i","",$_POST['tree_id']);
    $section=preg_replace("/[^a-zA-Z0-9_-]/i","",$_POST['section']);
    $idker=(integer)preg_replace("/^".$idtree."/i","",$_POST['branch_id']);

	$tree = new TafelTree($idtree);

    switch($module=getModuleBySection($section))
	{ case 'pages':
	    $table=$section;
		break;
	  default:
	    $table=$section.'_categories';
		break;
	}

	if($module=='pages')
	A::$DB->query("SELECT id,idker,name FROM {$table} WHERE idker={$idker} AND type='dir' ORDER BY sort");
	else
	A::$DB->query("SELECT id,idker,name FROM {$table} WHERE idker={$idker} ORDER BY sort");

	$count=A::$DB->numRows();
    while($row=A::$DB->fetchRow())
    { $options=array();
      $options['onclick']='modal_treeview_select';
	  if(A::$DB->existsRow("SELECT id FROM {$table} WHERE idker=".$row['id'].($module=='pages'?" AND type='dir'":"")))
      $options['canhavechildren']='true';
      $tree->addBranch($idtree.$row['id'],preg_replace("/[^a-zA-Zа-яА-Я0-9 ]+/iu"," ",$row['name']),$options);
    }
    A::$DB->free();
    print $count>0?$tree->getJSON():"[]";
  }
}

A::$REQUEST = new A_TreeRequest;