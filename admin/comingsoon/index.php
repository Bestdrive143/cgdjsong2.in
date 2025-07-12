<?php
define('IN_SS', true);
include_once('../inc/init.php');

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$title = 'Coming Soon';
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

$query = $db->simple_select("comingsoon", "cid");
$total = $db->num_rows($query);

echo '<h2>Coming Soon</h2>
<div class="catRowHome"><a href="'.$ss->settings['adminurl'].'/comingsoon/add.php">Add New Item</a></div>
<div class="updates">';

if($total != 0)
{
$options = ['order_by' => 'cid', 'order_dir' => 'desc', 'limit_start' => $start, 'limit' => $ss->settings['updates_per_page']];

$query = $db->simple_select("comingsoon", "created_at, description, status, cid", "", $options);
while($soon = $db->fetch_array($query))
{
echo '<div><b>Description:</b> '.$soon['description'].'<br/>
<b>Created At:</b> '.date("h:i:s d-M-y", $soon['created_at']).'<br/>
<b>Status:</b> '.($soon['status'] == 'A' ? 'Active' : 'Inactive').'<br/>
<a href="'.$ss->settings['adminurl'].'/comingsoon/edit.php?cid='.$soon['cid'].'">Edit</a> | <a href="'.$ss->settings['adminurl'].'/comingsoon/delete.php?cid='.$soon['cid'].'">Delete</a></div>';
}
}
else
{
echo '<div>No Comingsoon item.!</div>';
}
echo '</div>';

$url = "{$ss->settings['adminurl']}/comingsoon/index.php?page={page}";

echo pagination($page, $ss->settings['updates_per_page'], $total, $url);

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187 <b>Comingsoon</b></div>';

include_once('../footer.php');