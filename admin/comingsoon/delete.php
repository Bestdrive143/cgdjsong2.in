<?php
define('IN_SS', true);
include("../inc/init.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$cid = $ss->get_input('cid', 1);

if(isset($ss->input['action']) && $ss->input['action'] == 'do_delete' && $ss->request_method == 'post')
{
$query = $db->delete_query("comingsoon", "cid='{$cid}'");

header('Location: '.$ss->settings['adminurl'].'/comingsoon/index.php');
exit;
}

$query = $db->simple_select("comingsoon", "cid", "cid='{$cid}'");
$total = $db->num_rows($query);

if($total == 0)
{
header('Location: '.$ss->settings['adminurl'].'');
exit;
}

$title = 'Delete Comingsoon';
include_once('../header.php');

echo '<h2>Delete Comingsoon</h2>
<div class="toptitle">
<form method="post" action="#">
<div>Do you want to delete Comingsoon item #'.$cid.'?</div>
<div><input type="hidden" name="action" value="do_delete" />
<input type="submit" value="Delete" /> <a href="'.$ss->settings['adminurl'].'/comingsoon/index.php">No</a></div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <a href="'.$ss->settings['adminurl'].'/comingsoon">Coming Soon</a> &#187; <b>Delete Comingsoon</b></div>';

include_once('../footer.php');