<?php
/**
 * @project Astra.CMS Free
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @version 2.07.04
 * @license GNU General Public License
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package APanel
 */
/**************************************************************************/

class SystemOptions extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("system_options.tpl");
  }

  function Action($action)
  {
    $res=false;
	switch($action)
	{ case "save": $res=$this->Save(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=system&item=options");
  }

  function Save()
  {
    A::$DB->Update("_options",array('value'=>isset($_REQUEST['debugmode'])?1:0),"var='debugmode'");

	return true;
  }

  function createData()
  {
	$this->Assign("caption","Настройки системы");
  }
}

A::$MAINFRAME = new SystemOptions;