<?php
define('IN_SS', true);
include_once("../inc/init.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$fid = $ss->get_input('fid', 1);

$query = $db->simple_select("files", "fid, path", "fid={$fid}");
$file = $db->fetch_array($query);

if(!$file)
{
header("Location: {$ss->settings['url']}");
exit;
}

if(isset($ss->input['action']) && $ss->input['action'] == 'do_delete' && $ss->request_method == 'post')
{
if(is_dir(SS_ROOT.$file['path']))
{
deleteAll(SS_ROOT.$file['path']);

$query = $db->simple_select("files", "fid", "path LIKE '".$db->escape_string_like($file['path'])."%'");
while($fi = $db->fetch_array($query))
{
if(file_exists(SS_ROOT.'thumbs/'.$fi['fid'].'.png'))
{
unlink(SS_ROOT.'thumbs/'.$fi['fid'].'.png');
}
}

$db->delete_query("files", "path LIKE '".$db->escape_string_like($file['path'])."%'");
}
else
{
@unlink(SS_ROOT.$file['path']);

if(file_exists(SS_ROOT.'thumbs/'.$fid.'.png'))
{
unlink(SS_ROOT.'thumbs/'.$fid.'.png');
}

$db->delete_query("files", "fid='".$file['fid']."'");
}

header('Location: '.$ss->settings['adminurl'].'/files');
exit;
}

$title = 'Delete File/Folder';
include_once("../header.php");

echo '<h2>Delete Files</h2>
<div class="toptitle">
<form method="post" action="#">
<div>Do you want to delete?</div>
<div><input type="hidden" name="action" value="do_delete" />
<input type="submit" value="Delete" /> <a href="'.$ss->settings['adminurl'].'/files">No</a></div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <a href="'.$ss->settings['adminurl'].'/files">File Manager</a> &#187; <b>Delete</b></div>';

include_once("../footer.php");