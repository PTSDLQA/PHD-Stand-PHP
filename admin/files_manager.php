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

class FilesManager extends A_MainFrame
{
  function __construct()
  {
    parent::__construct("files_manager.tpl");
  }

  function createData()
  {
	$this->Assign("caption","Файловый менеджер");

	$this->Assign("fileadmin",new A_FileAdmin('','files/mysite/','files_mysite'));
  }
}

A::$MAINFRAME = new FilesManager;