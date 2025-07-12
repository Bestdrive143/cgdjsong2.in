<?php
error_reporting(0);
if(empty($title))
{
$title = $ss->settings['title'];
}
else
{
$title .= " - {$ss->settings['title']}";
}

$title = escape($title);

echo '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="title" content="'.$ss->settings['title'].' :: '.$title.', HD Videos, funny videos, Free HD Videos, Ringtones, Wallpapers, Themes, Games, Softwares, Mp3 Songs, Videos" />
<meta name="robots" content="index, follow" />
<meta name="language" content="en" />
<meta name="keywords" content="songs, desh bhakti songs, old Sonngs, ringtones, wallpapers, '.$title.'">
<meta name="description" content="Free Mobile Ringtones, Desh Bhakti Songs, Old Sonngs, Bollywood Songs, Wallpaper, Videos, Animations And More services  '.$title.'">
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport" />
<meta name="viewport" content="width=device-width" />
<title>'.$title.' :: Funny videos, Free HD Videos, Ringtones, Wallpapers, Themes, Games, Softwares, Mp3 Songs, Videos</title>
<link rel="shortcut icon" href="'.$ss->settings['url'].'/assets/images/favicon.ico" />
<link href="'.$ss->settings['url'].'/assets/css/css.css" type="text/css" rel="stylesheet"/>
</head>
<body>


<div class="logo" align="center"><a href="'.$ss->settings['url'].'"><img src="'.$ss->settings['logo'].'" width="55%" height="45" alt="'.$ss->settings['title'].'"></img></a></div>

<div align="center"><b><style type="text/css">b.drop-shadow { text-shadow: 2px 2px 3px aqua }</style><font color="blue"><b class="drop-shadow"><b><font size="4">Dj YuYu</font> </b></b></font> </b></div>';
