<?php
/** \file system/framework/mail.php
 * Письмо.
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

require_once("system/mail/mail.php");

/**
 * Формирование и отправка письма.
 */

class A_Mail extends Smarty
{

/**
 * Формат письма: text или html.
 */

  public $mode;

/**
 * Объект письма.
 */

  public $mail;

/**
 * Шаблон письма. Первая строка в шаблоне - тема письма.
 */

  public $template;

/**
 * Конструктор.
 *
 * @param string $template='' Шаблон письма.
 * @param string $mode='text' Формат письма: text или html.
 */

  function __construct($template='',$mode='text')
  {
    $this->template_dir="templates/mysite/mails/";
	$this->compile_dir="templates_c/mysite/mails/";

	$this->template=$template;
	$this->mode=$mode;

    $this->mail = new htmlMimeMail();
	$this->mail->setFrom(A::$OPTIONS['mailsfrom']);

	$this->Assign('site_name',A::$OPTIONS['sitename_ru']);

	if(defined('SECTION_NAME'))
	$this->Assign('section_name',SECTION_NAME);

	$this->Assign_by_ref("system",A::getSystem());
    $this->Assign_by_ref("auth",A::$AUTH);

	A::$OBSERVER->Event('CreateMail',$template,array('object'=>&$this));
  }

/**
 * Установить тему письма.
 *
 * @param string $subject Тема письма.
 */

  function setSubject($subject)
  {
    $this->mail->setSubject($subject);
  }

/**
 * Установить отправителя и обратный адрес.
 *
 * @param string $email email.
 * @param string $name='' Имя отправителя.
 */

  function setFrom($email,$name='')
  {
    $email=trim($email);
	$name=trim($name);
    if(!empty($name))
    $this->mail->setFrom("{$name}<{$email}>");
	else
	$this->mail->setFrom($email);
  }

/**
 * Установить текст письма.
 *
 * @param string $text Текст.
 */

  function setText($text)
  {
    $this->mail->setText($text);
  }

/**
 * Установить HTML содержимое письма.
 *
 * @param string $html HTML содержимое письма.
 */

  function setHTML($html)
  {
    $this->mail->setHTML($html,strip_tags($html),".");
  }

/**
 * Прикрепить файл.
 *
 * @param string $path Путь к файлу.
 * @param string $filename='' Имя файла.
 * @param string $mime='' Mime тип файла.
 * @return boolean Успешность операции.
 */

  function addAttachment($path,$filename='',$mime='')
  {
	if($data=@file_get_contents($path))
	{ if(empty($filename))
	  $filename=basename($path);
	  if(empty($mime))
	  $mime=getMimeByFile($path);
	  $this->mail->addAttachment($data,mb_convert_encoding($filename,"Windows-1251","UTF-8"),$mime);
	  return true;
	}
	return false;
  }

/**
 * Прикрепить зарегистрированный файл.
 *
 * @param integer $id Числовой идентификатор файла.
 * @return boolean Успешность операции.
 */

  function addAttachmentById($id)
  {
	if($row=A::$DB->getRowById($id,"mysite_files"))
	{ $path=preg_replace("/^\//i","",$row['path']);
	  if($data=@file_get_contents($path))
	  { $this->mail->addAttachment($data,mb_convert_encoding($row['name'],"Windows-1251","UTF-8"),$row['mime']);
		return true;
	  }
	}
	return false;
  }

/**
 * Получить результат обработки шаблона письма.
 *
 * @return string Результат обработки шаблона письма.
 */

  function getContent()
  {
	$content=$this->fetch($this->template);
	$content=preg_replace("/^[^\n]*\n/i","",$content);
	return preg_replace("/\r/","",$content);
  }

/**
 * Отправить письмо.
 *
 * @param string $email Email адресата, если несколько то через запятую или в виде массива.
 */

  function send($email)
  {
    if(empty($email)) return;

	A::$OBSERVER->Event('SendMail',$this->template,array('object'=>&$this,'mailto'=>&$email));

	if(is_file($this->template_dir.$this->template))
	{
	  $message=preg_replace("/\r/","",$this->fetch($this->template));
	  $subject=preg_match("/^([^\n]*)\n/i",$message,$matches)>0?$matches[1]:"";
	  $message=preg_replace("/^[^\n]*\n/i","",$message);

	  if(!empty($subject) && empty($this->mail->headers['Subject']))
      $this->setSubject($subject);

	  switch($this->mode)
	  { case "text": $this->mail->setText($message); break;
	    case "html": $this->mail->setHTML($message,strip_tags($message),"."); break;
	  }
	}

	if(!is_array($email))
	$email=explode(",",$email);

	foreach($email as $i=>$mail)
    $email[$i]=trim($mail);

    $this->mail->send($email,"mail");
  }
}