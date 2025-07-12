<?php
define('IN_SS', true);
include_once('./inc/init.php');

$fid = $ss->get_input('fid');

$query = $db->simple_select("files", "*", "fid={$fid}");
$file = $db->fetch_array($query);

if(!$file)
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$input = SS_ROOT.$file['path'];
$info = pathinfo($input);

$db->query("UPDATE ".TABLE_PREFIX."files SET views=views +1 WHERE fid='".$file['fid']."'");


$title = $file['name'];
include_once('./header.php');

$query = $db->simple_select("files", "*", "fid='{$file['pid']}'");
$folder = $db->fetch_array($query);

echo '<h1>Free Download '.escape($file['name']).'</h2>
<div class="fl" align="center">';

if(file_exists(SS_ROOT.'/thumbs/'.$file['fid'].'.png'))
{
echo '<img src="'.$ss->settings['url'].'/thumbs/'.$file['fid'].'.png" alt="'.escape($file['name']).'" width="120" height="120" />';
}
else if($folder['use_icon'] == 1 && file_exists(SS_ROOT.'/thumbs/'.$folder['fid'].'.png'))
{
echo '<img src="'.$ss->settings['url'].'/thumbs/'.$folder['fid'].'.png" alt="'.escape($file['name']).'" width="60" height="65" />';
}
else
{
echo '<img src="'.$ss->settings['url'].'/icon.php?file='.base64_encode($file['path']).'&fid='.$file['fid'].'" alt="'.escape($file['name']).'" width="100" height="100" />';
}

echo '</div>
<div class="updates">
<div>Name: '.escape($file['name']).'</div>
<div>Size: '.convert_filesize($file['size']).'</div>
<div>Added On: '.date("d-m-y", $file['time']).'</div>
<div>Views: '.escape($file['views']).'</div>
<div>Downloaded: '.escape($file['dcount']).'</div></div>'; 

include_once('./assets/ads/bdown.php');

echo '<div class="fl" align="center"><a class="dwnLink" href="'.$ss->settings['url'].'/file/download/'.$file['fid'].'.html"><b>Download File </b></a></div>';

If($info['extension'] == 'mp3')
{
$output = "{$info['dirname']}/64kb-{$info['filename']}.{$info['extension']}";
$output2 = "{$info['dirname']}/128kb-{$info['filename']}.{$info['extension']}";
$output3 = "{$info['dirname']}/192kb-{$info['filename']}.{$info['extension']}";

if(file_exists($output))
{
$output = str_replace(SS_ROOT, '', $output);

echo '<div class="fl dwnLink" align="center"><a href="'.$ss->settings['url'].'/'.$output.'"><b>[Download as 64Kbps]</b></a></div>';
}

if(file_exists($output2))
{
$output2 = str_replace(SS_ROOT, '', $output2);

echo '<div class="fl dwnLink" align="center"><a href="'.$ss->settings['url'].'/'.$output2.'"><b>[Download as 128Kbps]</b></a></div>';
}

if(file_exists($output3))
{
$output3 = str_replace(SS_ROOT, '', $output3);

echo '<div class="fl dwnLink" align="center"><a href="'.$ss->settings['url'].'/'.$output3.'"><b>[Download as 192Kbps]</b></a></div>';
}

/*
$zip = "{$folder['path']}/".convert_name($folder['name']).".zip";

if(!file_exists($zip))
{
$zipp = new ZipArchive();

if($zipp->open(SS_ROOT.$zip, ZIPARCHIVE::CREATE)===TRUE)
{ 
$query = $db->simple_sekect("files", "path", "pid='".$folder['fid']."' AND isdir=1");
while($ad = $db->fetch_array($query))
{
$zipp->addFile(SS_ROOT.$ad['path']);
}
$zipp->close();
}
}

echo '<div class="fl dwnLink" align="center"><a class="dwnLink" href="'.$ss->settings['url'].''.$zip.'"><b>[Download Full Album 320kbps.zip]<br/>Size : '.convert_filesize(filesize($zip)).'</b></a></div>';
*/

}

include_once('./assets/ads/adown.php');

if($ss->settings['related_files'])
{
echo '<h2>Related Files</h2>';

$options = ['order_by' => 'time DESC', 'limit' => $ss->settings['related_files_per_page']];

$query = $db->simple_select("files", "fid, name, tag, size, path,  dcount", "pid='{$file['pid']}' AND isdir='0'", $options);
while($rfile = $db->fetch_array($query))
{
echo '<div class="fl"><a href="'.$ss->settings['url'].'/download/'.$rfile['fid'].'/'.convert_name($rfile['name']).'.html" class="fileName"><div><div>';

if(file_exists(SS_ROOT.'/thumbs/'.$rfile['fid'].'.png'))
{
echo '<img src="'.$ss->settings['url'].'/thumbs/'.$rfile['fid'].'.png" alt="'.escape($rfile['name']).'" width="60" height="65" />';
}
else if($folder['use_icon'] == 1 && file_exists(SS_ROOT.'/thumbs/'.$folder['fid'].'.png'))
{
echo '<img src="'.$ss->settings['url'].'/thumbs/'.$folder['fid'].'.png" alt="'.escape($rfile['name']).'" width="60" height="65" />';
}
else 
{
echo '<img src="'.$ss->settings['url'].'/icon.php?file='.base64_encode($rfile['path']).'&fid='.$rfile['fid'].'" alt="'.escape($rfile['name']).'" width="60" height="65" />';
}

echo '</div><div>'.escape($rfile['name']).'';

if($rfile['tag'] == 1)
{
echo ' '.ss_img('new.png', "New").'';
}
else if($rfile['tag'] == 2)
{
echo ' '.ss_img('updated.png', "Updated").'';
}

echo '<br /><span>['.convert_filesize($rfile['size']).']</span><br /><span>'.$rfile['dcount'].' Download</span></div></div></a></div>';
}
}

echo '<h2>Related Tags</h2>
<div class="description">'.$file['name'].' Download, '.$file['name'].' Free Download, '.$file['name'].' All Mp3 Song Download, '.$file['name'].' Movies Full Mp3 Songs, '.$file['name'].' video song download, '.$file['name'].' Mp4 HD Video Song Download, '.$file['name'].' Download Ringtone, '.$file['name'].' Movies Free Ringtone, '.$file['name'].' Movies Wallpapers, '.$file['name'].' HD Video Song Download</div>';


$_dr = '';

echo '<div class="path"><a href="'.$ss->settings['url'].'/">Home</a>';

foreach(explode('/', substr($file['path'], 7)) as $dr)
{
$_dr .= "/".$dr;
$path = "/files{$_dr}";

$query = $db->simple_select("files", "fid, name", "path='".$db->escape_string($path)."'");
$id = $db->fetch_array($query);

if($fid == $id['fid'])
{
echo ' &#187; '.escape($id['name']).'';
}
else
{
echo ' &#187; <a href="'.$ss->settings['url'].'/categorylist/'.$id['fid'].'/'.$ss->settings['sort'].'/1.html">'.escape($id['name']).'</a>';
}
}
echo '</div>';

include_once('./footer.php');
