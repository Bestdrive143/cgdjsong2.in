<?php
define('IN_SS', true);
include_once("../inc/init.php");

include_once(SS_ROOT."/inc/class_watermark.php");

 if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$pid = $ss->get_input('pid', 1);

$title = 'Add Folder';
$errors = [];

include_once('../header.php');

echo '<h2>Add Folder</h2>';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_add' && $ss->request_method == 'post')
{
$name = $ss->get_input('name');

if(empty($name))
{
$errors[] = 'Folder can not be empty!</div>';
}

$query = $db->simple_select("files", "path", "fid='{$pid}'");
$folder = $db->fetch_array($query);

if(!$folder)
{
$folder['path'] = '/files';
}

$path = "{$folder['path']}/".convert_name($name)."";

$query = $db->simple_select("files", "fid", "path='".$db->escape_string($path)."'");
$count = $db->num_rows($query);

if($count != 0)
{
$errors[] = 'Folder already exists';
}

if(empty($errors))
{
$data = ['name' => $db->escape_string($name), 'description' => $db->escape_string($ss->get_input('description')), 'path' => $db->escape_string($path), 'pid' => $pid, 'time' => TIME_NOW, 'isdir' => 1, 'tag' => $ss->get_input('tag', 1), 'disporder' => $ss->get_input('disporder', 1), 'use_icon' => $ss->get_input('use_icon', 1)];

$fid = $db->insert_query("files", $data);

if($fid)
{
if(!is_dir(SS_ROOT.$path))
{
mkdir(SS_ROOT.$path, 0777, true);
}

if(isset($_FILES['icon']) && $_FILES['icon']['name'] != '')
{
upload_icon('icon', $fid);
}

echo '<div class="toptitle">Folder added successfully<br/><a href="'.$ss->settings['url'].'/categorylist/'.$fid.'/'.convert_name($name).'.html">Go to Added Folder</a></div>';
}

}
else
{
show_errors($errors);
}

}

echo '<div>
<form method="post" action="#" enctype="multipart/form-data">
<div class="toptitle">
<div>Name:</div>
<div><input type="text" name="name" value="New Folder" maxlength="100" /></div>
</div>
<div class="toptitle">
<div>Description:</div>
<div><textarea name="description" /></textarea></div>
</div>
<div class="toptitle">
<div>Display Order:</div>
<div><input type="text" name="disporder" value="" /></div>
</div>
<div class="toptitle">
<div>Picture (Inside):</div>
<div><input type="file" name="icon" /></div>
</div>
<div class="toptitle">
<div>Tag:</div>
<div><input type="radio" name="tag" value="1" /> New <input type="radio" name="tag" value="2" /> Update <input type="radio" name="tag" value="0" checked="sahil" /> No Tag</div>
</div>
<div class="toptitle">
<div>Use Folder icon as file icon:</div>
<div><input type="radio" name="use_icon" value="1" checked /> Yes <input type="radio" name="use_icon" value="0" /> No Tag</div>
</div>
<div class="toptitle">
<div><input type="hidden" name="action" value="do_add" />
<input type="submit" value="Add" /></div>
</div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <b>Add Folder</b></div>';

include_once('../footer.php');