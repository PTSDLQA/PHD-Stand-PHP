<?php
require_once('system/mail/mimepart.php');

class htmlMimeMail
{
  public $headers;
  public $html;
  public $text;
  protected $output;
  protected $html_text;
  protected $html_images;
  protected $image_types;
  protected $build_params;
  protected $attachments;
  protected $is_built;
  protected $return_path;
  protected $smtp_params;
  protected $errors;

  function __construct()
  {
    $this->html_images = array();
    $this->headers     = array('X-Mailer'=>'A.CMS','X-Priority'=>'3 (Normal)','MIME-Version'=>'1.0');
    $this->image_types = array('gif' => 'image/gif','jpg' => 'image/jpeg','jpeg'  => 'image/jpeg','bmp' => 'image/bmp','png' => 'image/png');
    $this->is_built    = false;

    $this->build_params['html_encoding'] = 'base64';
    $this->build_params['text_encoding'] = '8bit';
    $this->build_params['html_charset']  = 'utf-8';
    $this->build_params['text_charset']  = 'utf-8';
    $this->build_params['head_charset']  = 'utf-8';
    $this->build_params['text_wrap']     = 998;

    $this->smtp_params['host'] = 'localhost';
    $this->smtp_params['port'] = 25;
    $this->smtp_params['helo'] = getenv('HTTP_HOST');
    $this->smtp_params['auth'] = false;
    $this->smtp_params['user'] = '';
    $this->smtp_params['pass'] = '';
  }

  function setCrlf($crlf = "\n")
  {
    if (!defined('CRLF'))
	{
      define('CRLF', $crlf, true);
    }

    if (!defined('MAIL_MIMEPART_CRLF'))
	{
      define('MAIL_MIMEPART_CRLF', $crlf, true);
    }
  }

  function setSMTPParams($host = null, $port = null, $auth = null, $user = null, $pass = null)
  {
    if (!is_null($host)) $this->smtp_params['host'] = $host;
    if (!is_null($port)) $this->smtp_params['port'] = $port;
    if (!is_null($auth)) $this->smtp_params['auth'] = $auth;
    if (!is_null($user)) $this->smtp_params['user'] = $user;
    if (!is_null($pass)) $this->smtp_params['pass'] = $pass;
  }

  function setHeader($name, $value)
  {
    $this->headers[$name] = $value;
  }

  function setSubject($subject)
  {
    $this->headers['Subject'] = $subject;
  }

  function setFrom($from)
  {
    $this->headers['From'] = $from;
  }

  function setReturnPath($return_path)
  {
    $this->return_path = $return_path;
  }

  function setCc($cc)
  {
    $this->headers['Cc'] = $cc;
  }

  function setBcc($bcc)
  {
    $this->headers['Bcc'] = $bcc;
  }

  function setText($text = '')
  {
    $this->text = preg_replace("/\r/","",$text);
  }

  function setHtml($html, $text = null, $images_dir = null)
  {
    $this->html = preg_replace("/[\r\n]/i","",$html);
    $this->html_text = $text;

    if (isset($images_dir))
	{
      $this->_findHtmlImages($images_dir);
    }
  }

  function _findHtmlImages($images_dir)
  {
    $i=0;
	$images=array();
	if(preg_match_all("/<img[^>]+src='?\"?([^'^\"]+)'?\"?[^>]*>/i",$this->html,$matches))
    foreach($matches[1] as $src)
	if(!in_array($src,$images) && file_exists($images_dir.$src))
	{ $path_parts=pathinfo($src);
	  $path_parts["extension"]=mb_strtolower($path_parts["extension"]);
	  $this->html = str_replace($src,"img".$i.".".$path_parts["extension"],$this->html);
	  $this->addHtmlImage(@file_get_contents($images_dir.$src),"img".$i.".".$path_parts["extension"],$this->image_types[$path_parts["extension"]]);
	  $images[]=$src;
	  $i++;
	}
  }

  function addHtmlImage($file, $name = '', $c_type='application/octet-stream')
  {
    $this->html_images[] = array(
                    'body'   => $file,
                    'name'   => $name,
                    'c_type' => $c_type,
                    'cid'    => md5($name.time())
                  );
  }

  function addAttachment($file, $name = '', $c_type='application/octet-stream', $encoding = 'base64')
  {
    $this->attachments[] = array(
                  'body'    => $file,
                  'name'    => $name,
                  'c_type'  => $c_type,
                  'encoding'  => $encoding
                  );
  }

  function _addTextPart(&$obj, $text)
  {
    $params['content_type'] = 'text/plain';
    $params['encoding']     = $this->build_params['text_encoding'];
    $params['charset']      = $this->build_params['text_charset'];
    if (is_object($obj)) {
      return $obj->addSubpart($text, $params);
    } else {
	  $x=new Mail_mimePart($text, $params);
      return $x;
    }
  }

  function _addHtmlPart(&$obj)
  {
    $params['content_type'] = 'text/html';
    $params['encoding']     = $this->build_params['html_encoding'];
    $params['charset']      = $this->build_params['html_charset'];
    if (is_object($obj)) {
      return $obj->addSubpart($this->html, $params);
    } else {
      $x = new Mail_mimePart($this->html, $params);
      return $x;
    }
  }

  function _addMixedPart()
  {
    $params['content_type'] = 'multipart/mixed';
    $x = new Mail_mimePart('', $params);
	return $x;
  }

  function _addAlternativePart(&$obj)
  {
    $params['content_type'] = 'multipart/alternative';
    if (is_object($obj)) {
      return $obj->addSubpart('', $params);
    } else {
	  $x = new Mail_mimePart('', $params);
      return $x;
    }
  }

  function _addRelatedPart(&$obj)
  {
    $params['content_type'] = 'multipart/related';
    if (is_object($obj)) {
      return $obj->addSubpart('', $params);
    } else {
	  $x = new Mail_mimePart('', $params);
      return $x;
    }
  }

  function _addHtmlImagePart(&$obj, $value)
  {
    $params['content_type'] = $value['c_type'];
    $params['encoding']     = 'base64';
    $params['disposition']  = 'inline';
    $params['dfilename']    = $value['name'];
    $params['cid']          = $value['cid'];
    return $obj->addSubpart($value['body'], $params);
  }

  function _addAttachmentPart(&$obj, $value)
  {
    $params['content_type'] = $value['c_type'];
    $params['encoding']     = $value['encoding'];
    $params['disposition']  = 'attachment';
    $params['dfilename']    = $value['name'];
    return $obj->addSubpart($value['body'], $params);
  }

  function buildMessage($params = array())
  {
    if (!empty($params)) {
      while (list($key, $value) = each($params)) {
        $this->build_params[$key] = $value;
      }
    }

    if (!empty($this->html_images)) {
      foreach ($this->html_images as $value) {
        $this->html = str_replace($value['name'], 'cid:'.$value['cid'], $this->html);
      }
    }

    $null        = null;
    $attachments = !empty($this->attachments) ? true : false;
    $html_images = !empty($this->html_images) ? true : false;
    $html        = !empty($this->html)        ? true : false;
    $text        = isset($this->text)         ? true : false;

    switch (true) {
      case $text AND !$attachments:
        $message = $this->_addTextPart($null, $this->text);
        break;

      case !$text AND $attachments AND !$html:
        $message = $this->_addMixedPart();

        for ($i=0; $i<count($this->attachments); $i++) {
          $this->_addAttachmentPart($message, $this->attachments[$i]);
        }
        break;

      case $text AND $attachments:
        $message = $this->_addMixedPart();
        $this->_addTextPart($message, $this->text);

        for ($i=0; $i<count($this->attachments); $i++) {
          $this->_addAttachmentPart($message, $this->attachments[$i]);
        }
        break;

      case $html AND !$attachments AND !$html_images:
        if (!is_null($this->html_text)) {
          $message = $this->_addAlternativePart($null);
          $this->_addTextPart($message, $this->html_text);
          $this->_addHtmlPart($message);
        } else {
          $message = $this->_addHtmlPart($null);
        }
        break;

      case $html AND !$attachments AND $html_images:
        if (!is_null($this->html_text)) {
          $message = $this->_addAlternativePart($null);
          $this->_addTextPart($message, $this->html_text);
          $related = $this->_addRelatedPart($message);
        } else {
          $message = $this->_addRelatedPart($null);
          $related = $message;
        }
        $this->_addHtmlPart($related);
        for ($i=0; $i<count($this->html_images); $i++) {
          $this->_addHtmlImagePart($related, $this->html_images[$i]);
        }
        break;

      case $html AND $attachments AND !$html_images:
        $message = $this->_addMixedPart();
        if (!is_null($this->html_text)) {
          $alt = $this->_addAlternativePart($message);
          $this->_addTextPart($alt, $this->html_text);
          $this->_addHtmlPart($alt);
        } else {
          $this->_addHtmlPart($message);
        }
        for ($i=0; $i<count($this->attachments); $i++) {
          $this->_addAttachmentPart($message, $this->attachments[$i]);
        }
        break;

      case $html AND $attachments AND $html_images:
        $message = $this->_addMixedPart();
        if (!is_null($this->html_text)) {
          $alt = $this->_addAlternativePart($message);
          $this->_addTextPart($alt, $this->html_text);
          $rel = $this->_addRelatedPart($alt);
        } else {
          $rel = $this->_addRelatedPart($message);
        }
        $this->_addHtmlPart($rel);
        for ($i=0; $i<count($this->html_images); $i++) {
          $this->_addHtmlImagePart($rel, $this->html_images[$i]);
        }
        for ($i=0; $i<count($this->attachments); $i++) {
          $this->_addAttachmentPart($message, $this->attachments[$i]);
        }
        break;

    }

    if (isset($message)) {
      $output = $message->encode();
      $this->output   = $output['body'];
      $this->headers  = array_merge($this->headers, $output['headers']);

      srand((double)microtime()*10000000);
      if ( isset($_SERVER['HTTP_HOST']) ) $server_info_string = $_SERVER['HTTP_HOST'];
      elseif ( isset($_SERVER['SERVER_NAME']) ) $server_info_string = $_SERVER['SERVER_NAME'];
      else $server_info_string = 'localhost';
      $message_id = sprintf('<%s.%s@%s>', base_convert(time(), 10, 36), base_convert(rand(), 10, 36), $server_info_string);
      $this->headers['Message-ID'] = $message_id;

      $this->is_built = true;
      return true;
    } else {
      return false;
    }
  }

  function _encodeHeader($input, $charset = 'utf-8')
  {
	if(preg_match("/[а-яА-Я]+/iu",$input))
	{ if(preg_match("/^([^<]+)(\<[a-zA-Z0-9@._-]+\>)$/i",$input,$matches))
	  return "=?$charset?b?".base64_encode($matches[1])."?= ".$matches[2];
	  else
	  return "=?$charset?b?".base64_encode($input)."?=";
	}
	else
	return $input;
  }

  function send($recipients, $type = 'mail')
  {
    if (!defined('CRLF'))
	{
      $this->setCrlf($type == 'mail' ? "\n" : "\r\n");
    }

    if (!$this->is_built)
	{
      $this->buildMessage();
    }

    switch ($type) {
      case 'mail':
        $subject = '';
        if (!empty($this->headers['Subject']))
		{
          $subject = $this->_encodeHeader($this->headers['Subject'], $this->build_params['head_charset']);
          unset($this->headers['Subject']);
        }

        foreach ($this->headers as $name => $value)
		{
          $headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
        }

        $to = $this->_encodeHeader(implode(', ', $recipients), $this->build_params['head_charset']);

        if (!empty($this->return_path))
		{
          $result = mail($to, $subject, $this->output, implode(CRLF, $headers), '-f' . $this->return_path);
        } else {
          $result = mail($to, $subject, $this->output, implode(CRLF, $headers));
        }

        if ($subject !== '')
		{
          $this->headers['Subject'] = $subject;
        }

        return $result;
        break;

      case 'smtp':
        require_once('system/mail/smtp.php');
        require_once('system/mail/rfc822.php');
        $smtp = smtp::connect($this->smtp_params);

        foreach ($recipients as $recipient)
		{
          $addresses = Mail_RFC822::parseAddressList($recipient, $this->smtp_params['helo'], null, false);
          foreach ($addresses as $address) {
            $smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
          }
        }
        unset($addresses);
        unset($address);

        foreach ($this->headers as $name => $value)
		{
          if ($name == 'Cc' OR $name == 'Bcc')
		  {
            $addresses = Mail_RFC822::parseAddressList($value, $this->smtp_params['helo'], null, false);
            foreach ($addresses as $address)
			{
              $smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
            }
          }
          if ($name == 'Bcc') {
            continue;
          }
          $headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
        }

        $headers[] = 'To: ' . $this->_encodeHeader(implode(', ', $recipients), $this->build_params['head_charset']);

        $send_params['headers']    = $headers;
        $send_params['recipients'] = array_values(array_unique($smtp_recipients));
        $send_params['body']       = $this->output;

        if (isset($this->return_path))
		{
          $send_params['from'] = $this->return_path;
        }
		elseif (!empty($this->headers['From']))
		{
          $from = @Mail_RFC822::parseAddressList($this->headers['From']);
          $send_params['from'] = sprintf('%s@%s', $from[0]->mailbox, $from[0]->host);
        }
		else
		{
          $send_params['from'] = 'postmaster@' . $this->smtp_params['helo'];
        }

        if (!$smtp->send($send_params))
		{
          $this->errors = $smtp->errors;

          if(!empty(A::$OPTIONS['debugmode']))
		  foreach($smtp->errors as $i=>$error)
		  print $i.". ".$error."<br>";

		  return false;
        }

        return true;
        break;
    }
  }

  function getRFC822($recipients)
  {
    $this->setHeader('Date', date('D, d M y H:i:s O'));

    if (!defined('CRLF'))
	{
      $this->setCrlf($type == 'mail' ? "\n" : "\r\n");
    }

    if (!$this->is_built)
	{
      $this->buildMessage();
    }

    if (isset($this->return_path))
	{
      $headers[] = 'Return-Path: ' . $this->return_path;
    }

    foreach ($this->headers as $name => $value)
	{
      $headers[] = $name . ': ' . $value;
    }

	$headers[] = 'To: ' . implode(', ', $recipients);

    return implode(CRLF, $headers) . CRLF . CRLF . $this->output;
  }
}