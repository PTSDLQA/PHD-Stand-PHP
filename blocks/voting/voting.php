<?php
/**
 * @project Astra.CMS
 * @link http://a-cms.ru/
 * @copyright 2011 "Астра Вебтехнологии"
 * @author Vitaly Hohlov <admin@a-cms.ru>
 * @package Blocks
 */
/**************************************************************************/

/**
 * Блок "Форма голосования".
 *
 * <a href="http://wiki.a-cms.ru/blocks/voting">Руководство</a>.
 */

class voting_Block extends A_Block
{
/**
 * Формирование данных доступных в шаблоне.
 */

  function createData()
  {
	if($this->section==SECTION)
	{ $this->template="";
	  return;
	}

	$this->Assign("question",$this->options['question']);
	$this->Assign("datebegin",$this->options['datebegin']);
	$this->Assign("dateend",$this->options['dateend']);

	$ucode=md5($this->options['question']);

	if(!empty($this->options['datebegin']) && !empty($this->options['dateend']) &&
	!($this->options['datebegin']<time() && $this->options['dateend']>time()))
	$status=0;
	elseif(A_Session::get("{$this->section}_votes",isset($_COOKIE[$this->section."_votes"])?$_COOKIE[$this->section."_votes"]:"")==$ucode)
	$status=1;
	else
	$status=2;

	$this->Assign("active",!empty($this->options['active']));
	$this->Assign("isvoting",$status<2);

	$all=A::$DB->getOne("SELECT SUM(count) FROM {$this->section}_variants");

	$variants=array();
	A::$DB->query("SELECT * FROM {$this->section}_variants ORDER BY sort");
	while($row=A::$DB->fetchRow())
	{ $row['pr']=$all>0?round($row['count']*100/$all,2):0;
	  $variants[]=$row;
	}
	A::$DB->free();

	$this->Assign("variants",$variants);
	$this->Assign("allcount",$all);
  }
}