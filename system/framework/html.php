<?php
/** \file system/framework/html.php
 * HTML.
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

/**
 * Формирует HTML код надписи.
 *
 * @param string $text Текст надписи.
 * @param string $align="left" Выравнивание текста.
 * @return HTML.
 */

function AddLabel($text,$align="left")
{
  return "<p align=\"$align\">$text</p>";
}

/**
 * Формирует HTML код ссылки.
 *
 * @param string $name Текст ссылки.
 * @param string $link Ссылка.
 * @param string $title="" Подпись.
 * @return HTML.
 */

function AddLink($name,$link,$title="")
{
  return "<a href=\"$link\" title=\"$title\">$name</a>";
}

/**
 * Формирует HTML код ссылки.
 *
 * @param string $name Текст ссылки.
 * @param string $link Ссылка.
 * @param string $target Способ перехода.
 * @param string $title="" Подпись.
 * @return HTML.
 */

function AddTargetLink($name,$link,$target,$title="")
{
  return "<a href=\"$link\" title=\"$title\" target=\"$target\">$name</a>";
}

/**
 * Формирует HTML код картинки.
 *
 * @param string $image Путь к картинке.
 * @param string $width=0 Ширина.
 * @param string $height=0 Высота.
 * @param string $alt="" Подпись.
 * @return HTML.
 */

function AddImage($image,$width=0,$height=0,$alt="")
{
  $attr="";
  if($width>0)
  $attr.=" width=$width";
  if($height>0)
  $attr.=" height=$height";
  if(!empty($alt))
  $attr.=" alt=\"$alt\"";
  return "<img src=\"$image\" border=0$attr>";
}

/**
 * Формирует HTML код картинки-ссылки.
 *
 * @param string $image Путь к картинке.
 * @param string $link Ссылка.
 * @param string $title="" Подпись.
 * @param string $width=0 Ширина.
 * @param string $height=0 Высота.
 * @param string $attr="" Дополнительные аттрибуты.
 * @return HTML.
 */

function AddImageButtonLink($image,$link,$title="",$width=0,$height=0,$attr="")
{
  $attr2="";
  if($width>0)
  $attr2.=" width=$width";
  if($height>0)
  $attr2.=" height=$height";
  return "<a href=\"$link\" title=\"$title\"$attr><img src=\"$image\" border=0 alt=\"$title\"$attr2></a>";
}

/**
 * Формирует HTML код картинки-кнопки.
 *
 * @param string $image Путь к картинке.
 * @param string $onclick JavaScript обработчик нажатия.
 * @param string $title="" Подпись.
 * @param string $width=0 Ширина.
 * @param string $height=0 Высота.
 * @param string $attr="" Дополнительные аттрибуты.
 * @return HTML.
 */

function AddImageButton($image,$onclick,$title="",$width=0,$height=0,$attr="")
{
  $attr2="";
  if($width>0)
  $attr2.=" width=$width";
  if($height>0)
  $attr2.=" height=$height";
  return "<a href=\"javascript:$onclick\" title=\"$title\"$attr><img src=\"$image\" alt=\"$title\"$attr2></a>";
}

/**
 * Формирует HTML код кнопки-ссылки.
 *
 * @param string $caption Надпись на кнопке.
 * @param string $link Ссылка.
 * @param string $width="120px" Ширина кнопки.
 * @return HTML.
 */

function AddButtonLink($caption,$link,$width="120px")
{
  return "<input class=\"button\" type=\"button\" value=\"$caption\" style=\"width:$width;\" onclick=\"javascript:document.location='$link'\">";
}

/**
 * Формирует HTML код кнопки.
 *
 * @param string $caption Надпись на кнопке.
 * @param string $onclick JavaScript обработчик нажатия.
 * @param string $width="120px" Ширина кнопки.
 * @return HTML.
 */

function AddButton($caption,$onclick,$width="120px")
{
  return "<input class=\"button\" type=\"button\" value=\"$caption\" style=\"width:$width;\" onclick=\"$onclick\">";
}

/**
 * Формирует HTML код области текста с действием на клик.
 *
 * @param string $text Текст.
 * @param string $onclick JavaScript обработчик нажатия.
 * @param string $title="" Подпись.
 * @return HTML.
 */

function AddClickText($text,$onclick,$title="")
{
  if(!empty($text))
  return "<div onclick=\"$onclick\" style=\"cursor:pointer;width:100%;\" title=\"$title\">$text</div>";
  else
  return "<div onclick=\"$onclick\" style=\"cursor:pointer;width:100%;\" title=\"$title\">&nbsp;</div>";
}

/**
 * Формирует HTML код области.
 *
 * @param string $id Аттрибут id.
 * @param string $content="" Содержимое.
 * @param boolean $visible=true Видимость.
 * @return HTML.
 */

function AddDiv($id,$content="",$visible=true)
{
  if($visible)
  return "<div id=\"$id\">$content</div>";
  else
  return "<div id=\"$id\" style=\"display:none;\">$content</div>";
}

/**
 * Формирует HTML код области с CSS классом box.
 *
 * @param string $content="" Содержимое.
 * @param string $align="" Выравнивание.
 * @return HTML.
 */

function AddBox($content,$align="")
{
  return empty($align)?"<div class=\"box\">$content</div>":"<div class=\"box\" align=\"$align\">$content</div>";
}

/**
 * Контейнер HTML.
 */

class A_HTMLContent
{
/**
 * HTML содержимое.
 */

  public $content;

/**
 * Конструктор.
 *
 * @param string $content HTML содержимое.
 */

  function __construct($content="")
  {
    $this->content=$content;
  }

/**
 * Добавляет содержимое в контейнер.
 *
 * @param string $content HTML содержимое.
 */

  function AddContent($content)
  {
    $this->content.=$content;
  }

/**
 * Загружает из файла содержимое в контейнер.
 *
 * @param string $file Путь к файлу.
 */

  function LoadContent($file)
  {
	$this->content.=file_get_contents($file);
  }

/**
 * Добавляет HTML код надписи.
 *
 * @param string $text Текст надписи.
 * @param string $align="left" Выравнивание текста.
 */

  function AddLabel($text,$align="left")
  {
    $this->content.=AddLabel($text,$align);
  }

/**
 * Добавляет HTML код ссылки.
 *
 * @param string $name Текст ссылки.
 * @param string $link Ссылка.
 * @param string $title="" Подпись.
 */

  function AddLink($name,$link,$title="")
  {
    $this->content.=AddLink($name,$link,$title);
  }

/**
 * Добавляет HTML код картинки.
 *
 * @param string $image Путь к картинке.
 * @param string $width=0 Ширина.
 * @param string $height=0 Высота.
 * @param string $alt="" Подпись.
 */

  function AddImage($image,$width=0,$height=0,$alt="")
  {
    $this->content.=AddImage($image,$width,$height,$alt);
  }

/**
 * Добавляет HTML код картинки-ссылки.
 *
 * @param string $image Путь к картинке.
 * @param string $link Ссылка.
 * @param string $title="" Подпись.
 * @param string $width=0 Ширина.
 * @param string $height=0 Высота.
 * @param string $attr="" Дополнительные аттрибуты.
 */

  function AddImageButtonLink($image,$link,$title="",$width=0,$height=0,$attr="")
  {
    $this->content.=AddImageButtonLink($image,$link,$title,$width,$height,$attr);
  }

/**
 * Добавляет HTML код картинки-кнопки.
 *
 * @param string $image Путь к картинке.
 * @param string $onclick JavaScript обработчик нажатия.
 * @param string $title="" Подпись.
 * @param string $width=0 Ширина.
 * @param string $height=0 Высота.
 * @param string $attr="" Дополнительные аттрибуты.
 */

  function AddImageButton($image,$onclick,$title="",$width=0,$height=0,$attr="")
  {
    $this->content.=AddImageButton($image,$onclick,$title,$width,$height,$attr);
  }

/**
 * Добавляет HTML код кнопки-ссылки.
 *
 * @param string $caption Надпись на кнопке.
 * @param string $link Ссылка.
 * @param string $width="120px" Ширина кнопки.
 */

  function AddButtonLink($caption,$link,$width="120px")
  {
    $this->content.=AddButtonLink($caption,$link,$width);
  }

/**
 * Добавляет HTML код кнопки.
 *
 * @param string $caption Надпись на кнопке.
 * @param string $onclick JavaScript обработчик нажатия.
 * @param string $width="120px" Ширина кнопки.
 */

  function AddButton($caption,$onclick,$width="120px")
  {
    $this->content.=AddButton($caption,$onclick,$width);
  }

/**
 * Добавляет HTML код области.
 *
 * @param string $id Аттрибут id.
 * @param string $content="" Содержимое.
 * @param boolean $visible=true Видимость.
 */

  function AddDiv($id,$content="",$visible=true)
  {
    $this->content.=AddDiv($id,$content,$visible);
  }

/**
 * Добавляет HTML код области текста с действием на клик.
 *
 * @param string $text Текст.
 * @param string $onclick JavaScript обработчик нажатия.
 * @param string $title="" Подпись.
 */

  function AddClickText($text,$onclick,$title="")
  {
    $this->content.=AddClickText($text,$onclick,$title);
  }

/**
 * Добавляет HTML код области с CSS классом box.
 *
 * @param string $content="" Содержимое.
 * @param string $align="" Выравнивание.
 */

  function AddBox($content,$align="")
  {
    $this->content.=AddBox($content,$align);
  }

/**
 * Добавляет в HTML код JavaScript.
 *
 * @param string $script Путь к файлу или текст скрипта.
 * @param string $mode="file" Принимает значения: file - подключается файл, code - встраивается код.
 */

  function AddJScript($script,$mode="file")
  {
    switch($mode)
    { case "file": $this->content.="<script type=\"text/javascript\" src=\"/$script\"></script>"; break;
	  case "code": $this->content.="<script type=\"text/javascript\">$script</script>"; break;
	}
  }

/**
 * Добавляет в HTML код CSS.
 *
 * @param string $style Путь к файлу или текст CSS стилей.
 * @param string $mode="file" Принимает значения: file - подключается файл, code - встраивается код.
 */

  function AddStyle($style,$mode="code")
  {
    switch($mode)
    { case "file": $this->content.="<style>";
 	               $this->LoadContent($style);
	               $this->content.="</style>"; break;
	  case "code": $this->content.="<style>$style</style>"; break;
	}
  }

/**
 * Переопределяемый метод для дополнительных операций над содержимым.
 */

  function createData()
  {
  }

/**
 * Возвращает содержимое.
 */

  function getContent()
  {
    $this->createData();
	return $this->content;
  }

/**
 * Выводит содержимое.
 */

  function display()
  {
    print $this->getContent();
  }
}