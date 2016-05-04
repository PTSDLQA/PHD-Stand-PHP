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
 * Модуль "Голосование".
 *
 * <a href="http://wiki.a-cms.ru/modules/voting">Руководство</a>.
 */

class VotingModule extends A_MainFrame
{
/**
 * Маршрутизатор URL.
 *
 * @param array $uri Элементы полного пути URL.
 */

  function Router($uri)
  {
	if(count($uri)==0)
	$this->page="main";
	else
	A::NotFound();
  }

/**
 * Маршрутизатор действий.
 */

  function Action($action)
  {
    switch($action)
	{ case "addvote": $this->AddVote(); break;
	}
  }

/**
 * Обработчик действия: Выбор варианта ответа.
 */

  function AddVote()
  {
	$ucode=md5(A::$OPTIONS['question']);

	if(A_Session::get(SECTION."_votes",isset($_COOKIE[SECTION."_votes"])?$_COOKIE[SECTION."_votes"]:"")==$ucode)
	return false;

	if(empty($_REQUEST['id']))
	return false;

	A::$DB->execute("UPDATE ".SECTION."_variants SET count=count+1 WHERE id=".(integer)$_REQUEST['id']);

	A_Session::set(SECTION."_votes",$ucode);
	setcookie(SECTION."_votes",$ucode,time()+3600*24*7,"/");

	A::goUrl(getSectionLink(SECTION));
  }

/**
 * Формирование данных доступных в шаблоне текущих результатов.
 */

  function createData()
  {
	if(!empty(A::$OPTIONS['datebegin']) && !empty(A::$OPTIONS['dateend']) && !(A::$OPTIONS['datebegin']<time() && A::$OPTIONS['dateend']>time()))
	$status=1;
	elseif(empty(A::$OPTIONS['active']))
	$status=2;
	else
	$status=3;

	if($status==3)
	{
	  $this->Assign("question",A::$OPTIONS['question']);
	  $this->Assign("datebegin",A::$OPTIONS['datebegin']);
	  $this->Assign("dateend",A::$OPTIONS['dateend']);

	  $all=A::$DB->getOne("SELECT SUM(count) FROM ".SECTION."_variants");

	  $result=array();
	  A::$DB->query("SELECT * FROM ".SECTION."_variants ORDER BY sort");
	  while($row=A::$DB->fetchRow())
	  { $row['pr']=$all>0?round($row['count']*100/$all,2):0;
	    $result[]=$row;
	  }
	  A::$DB->free();

	  $this->Assign("result",$result);
	  $this->Assign("allcount",$all);
	}
	else
	A::NotFound();

	$this->AddNavigation(SECTION_NAME);
  }
}

A::$MAINFRAME = new VotingModule;