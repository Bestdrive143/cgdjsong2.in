<?php
define('IN_SS', true);
include('../inc/init.php');

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$title = 'Settings Management';
include('../header.php');

echo '<h2>Settings</h2>';

$options = ['order_by'=>'disporder', 'order_dir'=>'asc'];

$query = $db->simple_select("settingsgroups", "*", "", $options);
while($setting = $db->fetch_array($query))
{
echo '<div class="toptitle">&#187; <a href="'.$ss->settings['adminurl'].'/settings/setting.php?gid='.$setting['gid'].'"><b>'.$setting['title'].'</b></a>
<div style="padding: 3px" class="description">'.$setting['description'].'</div>
</div>';
}

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <b>Settings</b></div>';

include('../footer.php');