<?php
define('IN_SS', true);
include("../inc/init.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$cid = $ss->get_input('cid', 1);
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

$db->update_query("comingsoon", $data, "cid='{$cid}'");

$message = 'Coming Soon item edited sucessfully.';
}
else
{
$message = 'Please enter description.';
}
}

$query = $db->simple_select("comingsoon", "description, status", "cid='{$cid}'");
$soon = $db->fetch_array($query);

if(!$soon)
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$title = 'Edit Comingsoon';
include_once('../header.php');

echo '<h2>Edit Comingsoon</h2>';

if(!empty($message))
{
echo '<div class="toptitle">'.$message.'</div>';
}

echo '<div class="toptitle">
<form method="post" action="#">
<div>Description:</div>
<div><textarea name="description">'.escape($soon['description']).'</textarea></div>
<div><input type="checkbox" name="status" value="A" '.($soon['status'] == 'A' ? 'checked' : '').' /> Active(Show on Index)?</div>
<div><input type="hidden" name="action" value="do_edit" />
<input type="submit" name="edit" value="Edit" /></div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <a href="'.$ss->settings['adminurl'].'/comingsoon">Coming Soon</a> &#187; <b>Edit Comingsoon</b></div>';

include_once('../footer.php');