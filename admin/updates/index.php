<?php
define('IN_SS', true);
include_once('../inc/init.php');

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$title = 'Latest Updates';
include_once('../header.php');

if(isset($ss->input['page']))
{
$page = (int)$ss->input['page'];
}
else
{
$page = 1;
}

$start = ($page-1)*$ss->settings['updates_per_page'];

$query = $db->simple_select("updates", "uid");
$total = $db->num_rows($query);

echo '<h2>Latest Updates</h2>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/updates/add.php">Add New Updates</a></div>
<div class="updates">';

if($total != 0)
{
$options = ['order_by' => 'uid', 'order_dir' => 'desc', 'limit_start' => $start, 'limit' => $ss->settings['updates_per_page']];

$query = $db->simple_select("updates", "uid, description, created_at, status", "", $options);
while($update = $db->fetch_array($query))
{
echo '<div><b>Description:</b> '.escape($update['description']).'<br/>
<b>Created At:</b> '.date("h:i:s a d-M-y", $update['created_at']).'<br/>
<b>Status:</b> '.(($update['status'] == 'A') ? 'Active' : 'Inactive').'<br/>
<a href="'.$ss->settings['adminurl'].'/updates/edit.php?uid='.$update['uid'].'">Edit</a> | <a href="'.$ss->settings['adminurl'].'/updates/delete.php?uid='.$update['uid'].'">Delete</a></div>';
}
}
else
{
echo '<div>No updates.!</div>';
}
echo '</div>';

$url = "{$ss->settings['adminurl']}/updates/index.php?page={page}";

echo pagination($page, $ss->settings['updates_per_page'], $total, $url);

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <b>Updates</b></div>';

include_once('../footer.php');