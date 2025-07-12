<?php
define('IN_SS', true);
include("../inc/init.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$uid = $ss->get_input('uid', 1);

if(isset($ss->input['action']) && $ss->input['action'] == 'do_delete' && $ss->request_method == 'post')
{
$query = $db->delete_query("updates", "uid='{$uid}'");

header('Location: '.$ss->settings['adminurl'].'/updates/index.php');
exit;
}

$query = $db->simple_select("updates", "uid", "uid='{$uid}'");
$total = $db->num_rows($query);

if($total == 0)
{
header('Location: '.$ss->settings['adminurl'].'');
exit;
}

$title = 'Delete Updates';
include_once('../header.php');

echo '<h2>Delete Updates</h2>
<div class="toptitle">
<form method="post" action="#">
<div>Do you want to delete update record?</div>
<div><input type="hidden" name="action" value="do_delete" />
<input type="submit" value="Delete" /> <a href="'.$ss->settings['adminurl'].'/updates/index.php">No</a></div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <a href="'.$ss->settings['adminurl'].'/updates">Updates</a> &#187; <b>Delete Updates</b></div>';

include_once('../footer.php');