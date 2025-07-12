<?php
define('IN_SS', true);
include('../inc/init.php');

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$message = '';
$title = "Change Password";

if(isset($ss->input['action']) && $ss->input['action'] == 'do_change' && $ss->request_method == 'post')
{
$len = strlen($ss->input['newpass']);

if(!empty($ss->input['newpass']) && $len >= 5)
{
if($ss->settings['adminpass'] == sha1($ss->input['oldpass']))
{
$db->update_query("settings", ['value' => $db->escape_string(sha1($ss->input['newpass']))], "name='adminpass'");
ss_unsetcookie('pass');

if(function_exists('rebuild_settings'))
{
rebuild_settings();
}

header('Location: '.$ss->settings['adminurl'].'');
exit;
}
else
{
$message = 'Old password incorrect';
}
}
else
{
$message = 'New password must contain atleast 5 characters';
}
}

include('../header.php');

echo "<h2>Change Password</h2>";

if(!empty($message))
{
echo '<div class="toptitle">'.$message.'</div>';
}

echo '<div>
<form method="post" action="#">
<div class="toptitle"><b>Old Password:</b>
<div><input type="password" name="oldpass" value="" /></div></div>
<div class="toptitle"><b>New Password:</b>
<div><input type="password" name="newpass" value="" /></div></div>
<div class="toptitle"><input type="hidden" name="action" value="do_change" />
<input type="submit" name="change" value="Change Password" /></div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <b>Change Password</b></div>';

include('../footer.php');