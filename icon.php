<?php
define('IN_SS', true);
include_once('./inc/init.php');
include_once('./inc/class_watermark.php');

$width = 120;
$height = 120;

if(isset($ss->input['file']) && !empty($ss->input['file']))
{
$file = base64_decode($ss->input['file']);
}
else
{
$file = null;
}

if(is_null($file))
{
header('Location: '.$ss->settings['url'].'/assets/images/file.png');
die();
}

$fid = $ss->get_input ('fid');
$file = ".{$file}";

if(!file_exists($file))
{
header('Location: '.$ss->settings['url'].'/assets/images/file.png');
die();
}

$ext = pathinfo($file, PATHINFO_EXTENSION);

if(in_array($ext, array('png', 'gif', 'jpg', 'jpeg')))
{
$name = "./thumbs/{$fid}.png";

if(file_exists($name))
{
header("Location: {$name}", true, 301);
die();
}

list(, , $type, ) = getimagesize($file);

if($type == 1)
{
$funci = 'imagecreatefromgif';
$funco = 'imagegif';
}
if($type == 2)
{
$funci = 'imagecreatefromjpeg';
$funco = 'imagejpeg';
}
if($type == 3)
{
$funci = 'imagecreatefrompng';
$funco = 'imagepng';
}

if(isset($type))
{
$im1 = $funci($file);
$im2 = imagecreatetruecolor($width, $height);
imagecopyresized($im2, $im1, 0, 0, 0, 0, $width, $height, imagesx($im1), imagesy($im1));
header('Content-type: image/gif');
$funco($im2, $name);

if($ss->settings['watermark_thumb'])
{
watermark($name);
}

header('Location: '.$name, true, 301);
die();
}
else
{
header('Location: '.$ss->settings['url'].'/assets/images/filetypes/'.$ext.'.png', true,
301);
die();
}
}

elseif(in_array($ext, array('jar')))
{
$name = './thumbs/'.$fid.'.png';

if(is_file($name))
{
header('Location: '.$name, true, 301);
die();
}

$q = array("icon.png", "ico.png", "i.png", "icono.png", "Icon.png", "Ico.png", "I.png", "Icono.png", "ICON.png", "ICO.png", "I.png", "ICONO.png", "ICON.PNG",
"ICO.PNG", "I.PNG", "ICONO.PNG", "icons/icon.png", "icons/ico.png", "icons/i.png", "icons/icono.png", "i", "I");

include_once('./inc/pclzip.php');

$zip = new PclZip($file);
$ar = $zip->extract(PCLZIP_OPT_BY_NAME, $q, PCLZIP_OPT_EXTRACT_AS_STRING);
$pre = $ar[0]['content'];

if(!empty($pre))
{
$im = imagecreatefromstring($pre);
$im2 = imagecreatetruecolor($width, $height);
imageCopyResized($im2, $im, 0, 0, 0, 0, $width, $height, imagesx($im),
imagesy($im));
imagepng($im2, $name);

if($ss->settings['watermark_thumb'])
{
watermark($name);
}

header('Location: '.$name, true, 301);
die();
}
else
{
header('Location: '.$ss->settings['url'].'/assets/images/filetypes/jar.png', true, 301);
die();
}
}

elseif (in_array($ext, array('3gp', 'avi', 'mp4', 'mpg', 'mpeg')))
{
$name = "./thumbs/".$fid.".gif";

if(is_file($name))
{
header('Location: '.$name, true, 301);
die();
}

if(!class_exists('ffmpeg_movie'))
{
header('Location: '.$ss->settings['url'].'/assets/images/filetypes/'.$ext.'.png', true,
301);
die();
}

$mov = new ffmpeg_movie(realpath($file), false);
$wn = $mov->GetFrameWidth();
$hn = $mov->GetFrameHeight();
$frame = $mov->getFrame(3);
$gd = $frame->toGDImage();
$new = imageCreateTrueColor($width, $height);
imageCopyResized($new, $gd, 0, 0, 0, 0, $width, $height, $wn, $hn);
imageGif($new, $name, 100);

if($ss->settings['watermark_thumb'])
{
watermark ($name);
}

header('Location: ' . $name, true, 301);
die();
}

elseif($ext == 'nth')
{
$name = "./thumbs/{$fid}.gif";

if(file_exists($name))
{
header('Location: '.$name, true, 301);
die();
}

include('./inc/pclzip.php');
$nth = @new PclZip($file);
$content = $nth->extract(PCLZIP_OPT_BY_NAME, 'theme_descriptor.xml',
PCLZIP_OPT_EXTRACT_AS_STRING);
$teg = simplexml_load_string($content[0]['content'])->wallpaper['src'] or $teg =
simplexml_load_string($content[0]['content'])->wallpaper['main_display_graphics'];

if(empty($teg))
{
header('Location: '.$ss->settings['url'].'/assets/images/filetypes/nth.png', true, 301);
die();
}

$image = $nth->extract(PCLZIP_OPT_BY_NAME, trim($teg),
PCLZIP_OPT_EXTRACT_AS_STRING);
$im = array_reverse(explode('.', $teg));
$im = 'imageCreateFrom' . str_ireplace('jpg', 'jpeg', trim($im[0]));
file_put_contents($name, $image[0]['content']);
$f = $im($name);
$h = imagesy($f);
$w = imagesx($f);
$new = imagecreatetruecolor($width, $height);
imagecopyresampled($new, $f, 0, 0, 0, 0, $width, $height, $w, $h);
imageGif($new, $name, 100);

if($ss->settings['watermark_thumb'])
{
watermark ($name);
}

header('Location: '.$name);
}

elseif($ext == 'thm')
{
$name = "./thumbs/{$fid}.gif";

include('./inc/tar.php');
$thm = @new Archive_Tar($file);
$deskside_file = $thm->extractInString('Theme.xml');
$load = simplexml_load_string($deskside_file)->Standby_image['Source'] or $load =
simplexml_load_string($deskside_file)->Desktop_image['Source'] or $load =
simplexml_load_string($deskside_file)->Desktop_image['Source'];
$image = $thm->extractInString(trim($load));
$im = array_reverse(explode('.', $load));
$im = 'imageCreateFrom' . str_ireplace('jpg', 'jpeg', trim($im[0]));
file_put_contents($name, $image);
$f = $im($name);
$h = imagesy($f);
$w = imagesx($f);
$new = imagecreatetruecolor($width, $height);
imagecopyresampled($new, $f, 0, 0, 0, 0, $width, $height,
$w, $h);
imageGif($new, $name, 100);

if($ss->settings['watermark_thumb'])
{
watermark ($name);
}

$ima = getimagesize($name);

if($ima[0] < 1)
{
header('Location: '.$ss->settimgs['url'].'/assets/images/filetypes/thm.png', true, 301);
die();
}

header('Location: ' . $name, true, 301);
die();
}

elseif ($ext == 'apk')
{
include('./inc/pclzip.php');
$zip = new PclZip($file);
$ar = $zip->extract(PCLZIP_OPT_BY_PREG, "/png$/", PCLZIP_OPT_EXTRACT_AS_STRING);
$pre = $ar[0]['content'];
if(!empty($pre))
{
$name = './thumbs/' . $fid . '.png';
$im = imagecreatefromstring($pre);
$im2 = imagecreatetruecolor($width, $height);
imageCopyResized($im2, $im, 0, 0, 0, 0, $width, $height, imagesx($im),
imagesy($im));
imagepng($im2, $name);

if($ss->settings['watermark_thumb'])
{
watermark ($name);
}

header('Location: '.$name, true, 301);
die();
}
else
{
header('Location: '.$settings['url'].'/assets/images/filetypes/apk.png', true, 301);
die();
}
}
else if($ext == 'mp3')
{
include_once('./inc/getid3/getid3.php');
$name = './thumbs/'.$fid.'.png';
$getID3 = new getID3;
$fileinfo = $getID3->analyze($file);
$picture = @$fileinfo['id3v2']['APIC'][0]['data'];
if($picture)
{
$im = imagecreatefromstring($picture);
$im2 = imagecreatetruecolor($width, $height);
imageCopyResized($im2, $im, 0, 0, 0, 0, $width, $height, imagesx($im),
imagesy($im));
imagepng($im2, $name);

if($ss->settings['watermark_thumb'])
{
watermark ($name);
}

header('Location: '.$name, true, 301);
die();
}
else
{
header('Location: '.$ss->settings['url'].'/assets/images/filetypes/mp3.png');
die();
}
}
else
header('Location: '.$settings['url'].'/assets/images/filetypes/'.$ext.'.png', true, 301);