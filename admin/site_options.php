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

class SiteOptions extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("site_options.tpl");
  }

  function Action($action)
  {
    $res=false;
	switch($action)
	{ case "save": $res=$this->Save(); break;
	}
	if($res)
	A::goUrl("admin.php?mode=site&item=options");
  }

  function Save()
  {
	setOption('','sitename_'.A::$LANG,$caption=!empty($_REQUEST['sitename'])?strip_tags(trim($_REQUEST['sitename'])):"");
	setOption('','sitetitle_'.A::$LANG,!empty($_REQUEST['sitetitle'])?strip_tags(trim($_REQUEST['sitetitle'])):"");
	setOption('','mailsfrom',!empty($_REQUEST['mailsfrom'])?trim($_REQUEST['mailsfrom']):"");
	setOption('','404gomain',isset($_REQUEST['404gomain'])?1:0);
	setOption('','transurl',isset($_REQUEST['transurl'])?1:0);
	setOption('','siteclose',isset($_REQUEST['siteclose'])?1:0);
	setTextOption('','siteclosetext',!empty($_REQUEST['siteclosetext'])?trim($_REQUEST['siteclosetext']):"");
	setTextOption('','codecounters',!empty($_REQUEST['codecounters'])?trim($_REQUEST['codecounters']):"");
	setTextOption('','codemeta',!empty($_REQUEST['codemeta'])?trim($_REQUEST['codemeta']):"");
	if(isset($_REQUEST['cleartpl']))
	{ delFilesByDir("templates_c/admin");
      delFilesByDir("templates_c/mysite");
	}

	return true;
  }

  function createData()
  {
	$this->Assign("caption","Настройки сайта");

  }
}

A::$MAINFRAME = new SiteOptions;