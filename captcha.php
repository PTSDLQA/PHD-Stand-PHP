<?php
/** \file captcha.php
 * Генерация картинки с кодом.
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

ini_set("error_reporting",0);

require_once("system/framework/session.php");

A_Session::start();

$alphabet='123456789';
$length=4;
$correction=array('ravie.ttf' => array('1' => 'comic.ttf'),'forte.ttf' => array('C' => 'comic.ttf','E' => 'comic.ttf','T' => 'comic.ttf'));

$code='';
for($i=0;$i<$length;$i++)
$code.=mb_substr($alphabet,rand(0,mb_strlen($alphabet)-1),1);

A_Session::set("captcha",md5(mb_strtolower($code)));

$bgfiles=array();
$dh=opendir('templates/admin/images/bg/');
while(false !== ($filename=readdir($dh)))
if($filename != '.' && $filename != '..')
$bgfiles[]=$filename;

$randomBackground='templates/admin/images/bg/'.$bgfiles[rand(0,count($bgfiles)-1)];
$image=imagecreatefrompng($randomBackground);

$fontsFiles=array();
$dh=opendir('system/fonts');
while(false !== ($filename=readdir($dh)))
if($filename != '.' && $filename != '..')
$fontsFiles[]=$filename;

$w=imagesx($image);
$h=imagesy($image);
$crd=array();
$crd['x_start']=ceil($w/10);
$crd['y_start']=ceil(10+$h/3);
$crd['x_step']=ceil(($w-$w/8)/$length);
$crd['x_rand']=ceil($w/25);
$crd['y_rand']=ceil($h/10);
$crd['size_from']=ceil($crd['x_step']-$w/20);
$crd['size_to']=ceil($crd['x_step']-$w/15);

for($i=0;$i<$length;$i++)
{
  $symbol=$code[$i];
  $size=rand($crd['size_from'],$crd['size_to']);
  $angle=rand(-30,30);

  $randomFontFileName=$fontsFiles[rand(0,count($fontsFiles)-1)];
  $randomFont='system/fonts/'.$randomFontFileName;

  $r=(string) rand(50,200);
  $g=(string) rand(50,200);
  $b=(string) rand(50,200);

  if(abs($r - $g) % 100<20 && abs($r - $b) % 100<20)
  {
	$c=rand(1,3);
	$trigger=$c == 1 ? 1 : -1;
	$r += $trigger * 50;
	$g -= $trigger * 50;
	$b -= 50;
  }

  if(isset($correction[$randomFontFileName]) &&
  isset($correction[$randomFontFileName][$symbol]) &&
  file_exists('system/fonts/'.$correction[$randomFontFileName][$symbol]))
  $randomFont='system/fonts/'.$correction[$randomFontFileName][$symbol];

  $color=imagecolorallocate($image,$r,$g,$b);

  imagettftext($image,$size,$angle,
  $crd['x_start']+$crd['x_step']*$i+rand(0,$crd['x_rand']),
  $crd['y_start']+rand(0,$crd['y_rand']),
  $color,$randomFont,$symbol);

}

header("Content-type: image/png");
imagePng($image);

imagedestroy($image);