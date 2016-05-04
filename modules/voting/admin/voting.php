<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Modules
 */
/**************************************************************************/

/**
 * Панель управления модуля "Голосование".
 *
 * <a href="http://wiki.a-cms.ru/modules/voting">Руководство</a>.
 */

class VotingModule_Admin extends A_MainFrame
{
/**
 * Конструктор.
 */

  function __construct()
  {
    parent::__construct("module_voting.tpl");

	$this->AddJScript("/modules/voting/admin/voting.js");
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
    { case "save": $res=$this->Save(); break;
	  case "addvariant": $res=$this->AddVariant(); break;
	  case "editvariant": $res=$this->EditVariant(); break;
	  case "delvariant": $res=$this->DelVariant(); break;
	  case "newvoting": $res=$this->NewVoting(); break;
	}
    if(!empty($res))
	A::goUrl("admin.php?mode=sections&item=".SECTION,array('tab'));
  }

/**
 * Обработчик действия: Сохранение параметров голосования.
 */

  function Save()
  {
	setOption(SECTION,'question',strip_tags($_REQUEST['question']));
	setOption(SECTION,'datebegin',(integer)$_REQUEST['begin']);
	setOption(SECTION,'dateend',(integer)$_REQUEST['end']);
	setOption(SECTION,'active',isset($_REQUEST['active'])?1:0);

	return true;
  }

/**
 * Обработчик действия: Добавление варианта ответа.
 */

  function AddVariant()
  {
    $_REQUEST['sort']=A::$DB->getOne("SELECT MAX(sort) FROM ".SECTION."_variants")+1;
    $dataset = new A_DataSet(SECTION."_variants");
	$dataset->fields=array("name","sort");
	return $dataset->Insert();
  }

/**
 * Обработчик действия: Изменение варианта ответа.
 */

  function EditVariant()
  {
    $dataset = new A_DataSet(SECTION."_variants");
	$dataset->fields=array("name");
	return $dataset->Update();
  }

/**
 * Обработчик действия: Удаление варианта ответа.
 */

  function DelVariant()
  {
    $dataset = new A_DataSet(SECTION."_variants");
    return $dataset->Delete();
  }

/**
 * Обработчик действия: Новое голосование.
 */

  function NewVoting()
  {
    if(isset($_REQUEST['arch']))
	{
	  $_REQUEST['date1']=A::$OPTIONS['datebegin'];
	  $_REQUEST['date2']=A::$OPTIONS['dateend'];
      $_REQUEST['name']=A::$OPTIONS['question'];
	  $_REQUEST['count']=A::$DB->getOne("SELECT SUM(count) FROM ".SECTION."_variants");
	  $_REQUEST['result']=serialize(A::$DB->getAll("SELECT * FROM ".SECTION."_variants ORDER BY sort"));

	  if(!empty($_REQUEST['date1']) && !empty($_REQUEST['date2']) && !empty($_REQUEST['name']) && !empty($_REQUEST['count']))
	  { $dataset = new A_DataSet(SECTION."_arch");
	    $dataset->fields=array("date1","date2","name","result","count");
	    $dataset->Insert();
	  }
	}

    A::$DB->execute("DELETE FROM ".SECTION."_variants");

	setOption(SECTION,'question','');
	setOption(SECTION,'datebegin',0);
	setOption(SECTION,'dateend',0);
	setOption(SECTION,'active',0);

	return true;
  }

/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	$this->Assign("question",A::$OPTIONS['question']);
	$this->Assign("datebegin",A::$OPTIONS['datebegin']);
	$this->Assign("dateend",A::$OPTIONS['dateend']);

	if(!(A::$OPTIONS['datebegin']<time() && A::$OPTIONS['dateend']>time()))
	$status=1;
	elseif(A::$OPTIONS['active']==0)
	$status=2;
	else
	$status=3;
	$this->Assign("status",$status);
	$this->Assign("active",A::$OPTIONS['active']==1);

	$all=A::$DB->getOne("SELECT SUM(count) FROM ".SECTION."_variants");
	$variants=array();
	A::$DB->query("SELECT * FROM ".SECTION."_variants ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $row['pr']=$all>0?round($row['count']*100/$all,2):0;
	  $variants[]=$row;
	}
	A::$DB->free();

	$this->Assign("variants",$variants);

	$arch=array();
	$pager = new A_Pager(20);
	$pager->tab="arch";
	$pager->query("SELECT * FROM ".SECTION."_arch ORDER BY date1 DESC");
	while($row=$pager->fetchRow())
	$arch[]=$row;
	$pager->free();

	$this->Assign("arch",$arch);
	$this->Assign("arch_pager",$pager);
  }
}

A::$MAINFRAME = new VotingModule_Admin;