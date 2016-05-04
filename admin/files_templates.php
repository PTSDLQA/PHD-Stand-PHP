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

class FilesTemplates extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("files_templates.tpl");
  }

  function createData()
  {
	$this->Assign("caption","Шаблоны");

	$this->Assign("fileadmin",new A_FileAdmin("templates/mysite/",'','tpl_mysite'));
  }
}

A::$MAINFRAME = new FilesTemplates;