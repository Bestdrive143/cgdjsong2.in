<?php
define('IN_SS', true);
include_once("./inc/init.php");

include_once(SS_ROOT."/inc/class_watermark.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$fid = 0;
$title = 'Upload File';
$pid = $ss->get_input('pid', 0);

if($pid != 0)
{
$query = $db->simple_select("files", "path", "fid='{$pid}'");
$path = $db->fetch_field($query, 'path');
}

if(empty($path))
{
$pid = 0;
}

include_once('./header.php');

echo '<h2>Upload File</h2>';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_upload' && $ss->request_method == 'post')
{
if($pid == 0)
{
$path = '/files'.$ss->get_input('path').'';
}

if(isset($_FILES['file']) && $_FILES['file']['name'] != '')
{
$fid = upload_file('file', $path);
}
elseif(isset($ss->input ['url']) && !empty($ss->input ['url']) && $ss->input ['url'] != 'http://')
{
$fid = import_file($ss->get_input('url'), $path);
}
else
{
echo '<div class="toptitle">No file is selected</div>';
}

if($fid > 0 && isset($_FILES['icon']) && $_FILES['icon']['name'] != '')
{
upload_icon('icon', $fid);
}

}

echo '<div>
<form action="#" method="post"
enctype="multipart/form-data">
<div class="toptitle">
<div>Upload File To:</div>';

if($pid != 0)
{
echo '<div>'.escape($path).'</div>';
}

else
{
echo '<div><select name="path">
<option value="">./</option>';

$query = $db->simple_select("files", "path", "isdir=1");
while($folder = $db->fetch_array($query))
{
$folder2 = substr($folder['path'], 6);

echo '<option value="'.$folder2.'">'.$folder2.'</option>';
}

echo '</select></div>';
}

echo '</div>
<div class="toptitle">
<div>Name:</div>
<div><input type="text" name="name" value="" /></div>
</div>
<div class="toptitle">
<div>Select File:</div>
<div><input type="file" name="file" /></div>
</div>
<div class="toptitle">
<div>Url:</div>
<div><input type="text" name="url" value="http://" /></div>
</div>
<div class="toptitle">
<div>Icon:</div>
<div><input type="file" name="icon" value="" /></div>
</div>
<div class="toptitle">
<div>Description:</div>
<div><textarea name="description"></textarea></div>
</div>
<div class="toptitle">
<div><input type="hidden" name="action" value="do_upload">
<input type="submit" value="Upload" /></div>
</div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <b>Upload File</b></div>';

include_once('./footer.php');