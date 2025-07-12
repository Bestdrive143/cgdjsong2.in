<?php
define('IN_SS', true);
include_once('../inc/init.php');

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$title = '';
$folder = [];
$pid = $ss->get_input('pid', 1);
$page = isset($ss->input['page']) ? (int)$ss->input['page'] : 1;

if($pid != 0)
{
$query = $db->simple_select("files", "fid, name, use_icon, path, description", "fid='{$pid}'");
$folder = $db->fetch_array($query);

if(!is_array($folder))
{
header('Location: '.$ss->settings['url']);
exit;
}

$title = $folder['name'];
$folder['name'] = escape($folder['name']);
}
else
{
$folder['name'] = 'Home';
$folder['use_icon'] = 0;
}

include_once('../header.php');


if($pid == 0)
{
echo '<div id="category"><h2>Select Category</h2></div>';
}
else
{
echo '<div id="category"><h2>'.$folder['name'].'</h2></div>
<div class="description">'.escape($folder['description']).'</div>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/files/edit.php?fid='.$folder['fid'].'">Edit</a></div>';

if(file_exists(SS_ROOT.'/thumbs/'.$folder['fid'].'.png'))
{
echo '<div class="showimage" align="center"><img src="'.$ss->settings['url'].'/thumbs/'.$folder['fid'].'.png" alt="'.$folder['name'].'" height="150" width="150" class="absmiddle"/></div>';
}
}

echo '<div class="catRow"><a href="'.$ss->settings['adminurl'].'/files/add.php?pid='.$pid.'">Add Folder</a></div>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/upload.php?pid='.$pid.'">Upload File</a></div>';

$query = $db->simple_select("files", "fid", "pid='{$pid}'");
$total = $db->num_rows($query);

if($total != 0)
{
$start = ($page-1)*$ss->settings['files_per_page'];

$options = ['order_by' => 'isdir DESC, disporder ASC', 'limit_start' => $start, 'limit' => $ss->settings['files_per_page']];

$query = $db->simple_select("files", "fid, name, isdir, tag, path, size, dcount", "pid='{$pid}'", $options);
while($file = $db->fetch_array($query))
{
if($file['isdir'] == 1)
{
echo '<div class="catRow"><a href="'.$ss->settings['adminurl'].'/files/index.php?pid='.$file['fid'].'"><div>'.escape($file['name']).'';

if($ss->settings['show_filecount'])
{
$counter = $db->simple_select("files", "fid", "path LIKE '".$db->escape_string_like($file['path'])."%' AND `isdir` = '0'");
echo ' ['.$db->num_rows($counter).'] ';
}

if($file['tag'] == 1)
{
echo ' '.ss_img('new.png', "New").'';
}
else if($file['tag'] == 2)
{
echo ' '.ss_img('updated.png', "Updated").'';
}

echo '</div></a></div>';
}
else
{
echo '<div class="fl"><a href="'.$ss->settings['adminurl'].'/files/edit.php?fid='.$file['fid'].'"><div><div>';

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
echo '</div><div>'.escape($file['name']).'<br /><span>['.convert_filesize($file['size']).']</span><br /><span>'.$file['dcount'].' Download</span></div></div></a></div>';
}}

$url = "{$ss->settings['adminurl']}/files/index.php?pid={$pid}&page={page}";
echo pagination($page, $ss->settings['files_per_page'], $total, $url);
}
else
{
echo '<div class="catRow">Folder is empty!</div>';
}


if($pid != 0)
{

$_dr = '';

echo '<div class="path"><a href="'.$ss->settings['url'].'/">Home</a>';

foreach(explode('/', substr($folder['path'], 7)) as $dr)
{
$_dr .= "/".$dr;
$path = "/files{$_dr}";

$query = $db->simple_select("files", "fid, name", "path='".$db->escape_string($path)."'");
$id = $db->fetch_array($query);

if($pid == $id['fid'])
{
echo ' &#187; '.escape($id['name']).'';
}
else
{
echo ' &#187; <a href="'.$ss->settings['adminurl'].'/files/index.php?pid='.$id['fid'].'">'.escape($id['name']).'</a>';
}
}
echo '</div>';
}

include_once('../footer.php');
