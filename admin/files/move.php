<?php
define('IN_SS', true);
include_once("../inc/init.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$title = 'Move';
$fid = $ss->get_input('fid', 1);
$message = '';

$query = $db->simple_select("files", "*", "fid={$fid}");
$file = $db->fetch_array($query);

if(!$file)
{
header("Location: {$ss->settings['url']}");
exit;
}

include_once("../header.php");

if(isset($ss->input['action']) && $ss->input['action'] == 'do_move')
{
$path = "/files".$ss->get_input('path');

$query = $db->simple_select("files", "fid", "path='".$db->escape_string($path)."'");
$dirid = $db->fetch_field($query, 'fid');

$real_path = $path."/".basename($file['path']);

$db->update_query("files", ['pid' => $dirid, 'path' => $db->escape_string($real_path)], "fid='".$file['fid']."'");

if($file['path'] != $real_path)
{
if(is_file(SS_ROOT.$file['path']))
{
rename(SS_ROOT.$file['path'], SS_ROOT.$real_path);
}
else
{
dirmv(SS_ROOT.$file['path'], SS_ROOT.$real_path);
$db->query("UPDATE`".TABLE_PREFIX."files` SET `path`=replace(`path`,'".$db->escape_string($file['path'])."','".$db->escape_string($real_path)."') WHERE `path` LIKE '".$db->escape_string_like($file['path'])."%'");
}
$message = 'File/Folder moved sucessfully.';
}
}

echo '<h2>Move Files</h2>';

if(!empty($message))
{
echo '<div class="toptitle">'.$message.'</div>';
}
echo '<div>
<form method="post" action="#">
<div class="toptitle">Move To:
<div><select name="path">
<option value="">./</option>';

$query = $db->simple_select("files", "path", "isdir=1");
while($folder = $db->fetch_array($query))
{
$folder2 = substr($folder['path'], 6);
if(dirname($file['path']) === $folder['path'])
$selected="selected='sahil'";
else
$selected='';

echo '<option value="'.$folder2.'"'.$selected.'>'.$folder2.'</option>';
}
echo '</select>/'.basename($file['path']).'</div>
<div><input type="hidden" name="action" value="do_move" />
<input type="submit" value="Move" /></div>
</div></form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <b>Move</b></div>';

include_once('../footer.php');