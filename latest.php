<?php
define('IN_SS', true);
include_once('./inc/init.php');

$title = 'Last Added Files';
$folder = [];
$page = isset($ss->input['page']) ? (int)$ss->input['page'] : 1;
$folder['name'] = 'Home';
$folder['use_icon'] = 0;

include_once('./header.php');

include_once('./assets/ads/bcategory.php');

// Category title
echo '<div id="category"><h2>Latest Added Files</h2></div>';

include_once('./assets/ads/acategory.php');

include_once('./assets/ads/bfilelist.php');

$query = $db->simple_select("files", "fid", "isdir='0'");
$total = $db->num_rows($query);

if($total != 0)
{
$start = ($page-1)*$ss->settings['files_per_page'];

$options = ['order_by' => 'time DESC', 'limit_start' => $start, 'limit' => $ss->settings['files_per_page']];

$query = $db->simple_select("files", "fid, name, tag, size, path, pid, dcount", "isdir='0'", $options);
while($file = $db->fetch_array($query))
{
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

echo '</div><div>'.escape($file['name']).'';

if($file['tag'] == 1)
{
echo ' '.ss_img('new.png', "New").'';
}
else if($file['tag'] == 2)
{
echo ' '.ss_img('updated.png', "Updated").'';
}

echo '<br /><span>['.convert_filesize($file['size']).']</span><br /><span>'.$file['dcount'].' Download</span></div></div></a></div>';
}

$url = "{$ss->settings['url']}/newitems/{page}.html";

echo pagination($page, $ss->settings['files_per_page'], $total, $url);
}
else
{
echo '<div class="catRow">No file is added!</div>';
}

include_once('./assets/ads/afilelist.php');

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; Latest Files</div>';

include_once('./footer.php');