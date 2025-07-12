<?php
define('IN_SS', true);
include_once("../inc/init.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$message = '';
$title = 'Add Comingsoon';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_add' && $ss->request_method == 'post')
{
$description = $ss->get_input('description');
$status = $ss->get_input('status');

if($status != 'A')
{
$status = 'D';
}

if(!empty($description))
{
$data = ['description' => $db->escape_string($description), 'created_at' => TIME_NOW, 'status' => $status];

$db->insert_query("comingsoon", $data);

$message = 'Coming Soon Item added successfully.';

header('Location: '.$ss->settings['adminurl'].'/comingsoon/index.php');
}
else
{
$message = 'Please enter description.';
}
}

include_once('../header.php');

echo '<h2>Add Coming Soon</h2>';

if(!empty($message))
{
echo '<div class="toptitle">'.$message.'</div>';
}

echo '<div class="toptitle">
<form method="post" action="#">
<div>Description:</div>
<div><textarea name="description"></textarea></div>
<div><input type="checkbox" name="status" value="A" checked /> Active(Show on Index)?</div>
<div><input type="hidden" name="action" value="do_add" />
<input type="submit" value="Add" /></div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <a href="'.$ss->settings['adminurl'].'/comingsoon">Coming Soon</a> &#187 <b>Add Comingsoon</b></div>';

include_once('../footer.php');