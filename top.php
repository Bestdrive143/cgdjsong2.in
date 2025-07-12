<?php
define('IN_SS', true);
include_once('./inc/init.php');

$title = 'Top Files';
$folder = [];
$folder['name'] = 'Home';
$folder['use_icon'] = 0;
$act = $ss->get_input('act',  1);
include_once('./header.php');

include_once('./assets/ads/bcategory.php');

$options = ['order_by' => 'hits DESC', 'limit' => 20];

switch($act)
{
case '1':
$verb ='Yesterday';
$date = date("dmY",  TIME_NOW-86400);

$query = $db->simple_select("download_history", "fid, hits", "date='{$date}'", $options);
break;

case '2':
$verb ='Week';
$date = date("dmY",  TIME_NOW-604800);

$query = $db->simple_select("download_history", "fid, hits", "date>={$date}", $options);
break;

case '3':
$verb = 'Month';
$date = date("dmY",  TIME_NOW-2592000);

$query = $db->simple_select("download_history", "fid, hits", "date>={$date}", $options);
break;

default:
$verb =' Today ';
$date = date("dmY",  TIME_NOW);

$query = $db->simple_select("download_history", "fid, hits", "date='{$date}'", $options);
break;
}

// Category title
echo '<div id="category"><h2>'.$verb.' Top20 Files</h2></div>';

include_once('./assets/ads/acategory.php');

$top_links = [['value' => '1', 'name' => 'Yesterday'], ['value' => '2', 'name' => 'Week'], ['value' => '3', 'name' => 'Month'], ['value' => 0, 'name' => 'Today']];

echo '<div class="dtype">';

$bar = '';

foreach($top_links as $sort_link)
{
echo ''.$bar.'<a href="'.$ss->settings['url'].'/top/'.$sort_link['value'].'.html">'.$sort_link['name'].'</a>';

$bar = ' | ';
}

echo '</div>';

include_once('./assets/ads/bfilelist.php');

$total = $db->num_rows($query);

if($total != 0)
{
while($top = $db->fetch_array($query))
{
$query2 = $db->simple_select("files", "fid, name, size, path, pid", "fid='{$top['fid']}'");
$file = $db->fetch_array($query2);

if($file['pid'] != 0)
{
$query2 = $db->simple_select("files", "fid, use_icon", "fid='{$file['pid']}'");
$folder = $db->fetch_array($query2);
}

echo '<div class="fl"><a href="'.$ss->settings['url'].'/download/'.$file['fid'].'/'.convert_name($file['name']).'.html" class="fileName"><div><div>';

if(file_exists(SS_ROOT.'/thumbs/'.$file['fid'].'.png'))
{
echo '<img src="'.$ss->settings['url'].'/thumbs/'.$file['fid'].'.png" alt="'.escape($file['name']).'" width="60" height="65" />';
}
else if($folder['use_icon'] == 1 && file_exists(SS_ROOT.'/thumbs/'.$folder['fid'].'.png'))
{
echo '<img src="'.$ss->settings['url'].'/thumbs/'.$folder['fid'].'.png" alt="'.escape($file['name']).'" width="60" height="65" />';
}
else 
{
echo '<img src="'.$ss->settings['url'].'/icon.php?file='.base64_encode($file['path']).'&fid='.$file['fid'].'" alt="'.escape($file['name']).'" width="60" height="65" />';
}

echo '</div><div>'.escape($file['name']).'<br /><span>['.convert_filesize($file['size']).']</span><br /><span>'.$top['hits'].' Hits</span></div></div></a></div>';
}
}
else
{
echo '<div class="toptitle">No top file</div>';
}

include_once('./assets/ads/afilelist.php');

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; Top Files</div>';

include_once('./footer.php');