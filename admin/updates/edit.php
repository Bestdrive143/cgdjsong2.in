<?php
define('IN_SS', true);
include("../inc/init.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$uid = $ss->get_input('uid', 1);
$message = '';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_edit' && $ss->request_method == 'post')
{
$description = $ss->get_input('description');
$status = $ss->get_input('status');

if($status != 'A')
{
$status = 'D';
}

if(!empty($description))
{
$data = ['description' => $db->escape_string($description), 'status' => $status];

$db->update_query("updates", $data, "uid='{$uid}'");

$message = 'Update record edited sucessfully.';
}
else
{
$message = 'Please enter update description.';
}
}

$query = $db->simple_select("updates", "description, status", "uid='{$uid}'");
$update = $db->fetch_array($query);

if(!$update)
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$title = 'Edit Updates';
include_once('../header.php');

echo '<h2>Edit Update</h2>';

if(!empty($message))
{
echo '<div class="toptitle">'.$message.'</div>';
}

echo '<div class="toptitle">
<form method="post" action="#">
<div>Description:</div>
<div><textarea name="description">'.escape($update['description']).'</textarea></div>
<div><input type="checkbox" name="status" value="A" '.($update['status'] == 'A' ? 'checked' : '').'> Active(Show on Index)?</div>
<div><input type="hidden" name="action" value="do_edit" />
<input type="submit" value="Edit" /></div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <a href="'.$ss->settings['adminurl'].'/updates">Updates</a> &#187; <b>Edit Updates</b></div>'; 

include_once('../footer.php');