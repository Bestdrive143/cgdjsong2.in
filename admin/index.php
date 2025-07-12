<?php
define('IN_SS', true);
include_once("./inc/init.php");

// Login
if(isset($ss->input['action']) && $ss->input['action'] == 'do_login')
{
$pass = sha1($ss->input['password']);

if($pass == $ss->settings['adminpass'])
{
if($ss->get_input('remember', 1) == 1)
{
ss_setcookie("pass", $pass);
}

$_SESSION['adminpass'] = $pass;
}

}

$title = 'Admin Cp';
include_once("./header.php");

if(!is_admin())
{
echo '<h2>Admincp Login</h2>
<div>
<form method="post" action="#">
<div class="toptitle">
<div>Password:</div>
<div><input type="password" name="password" /></div>
</div>
<div class="toptitle">
<div><input type="checkbox" name="remember" value="1" /> Remember Me</div>
<div><input type="hidden" name="action" value="do_login" />
<input type="submit" value="Login" /></div>
</div>
</form>
</div>';
}
else
{
echo '<h2>Admin Cp</h2>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/settings">Settings Manager</a></div>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/updates">Updates Manager</a></div>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/comingsoon">Coming Soon Manager</a></div>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/ads.php">Ads Manager</a></div>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/files">File Manager</a></div>
<h2>Extra Menu</h2>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/settings/change.php">Change Password</a></div>
<div class="catRow"><a href="'.$ss->settings['adminurl'].'/scan.php">Scan Folder</a></div>';
}

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187 <b>Admincp</b></div>';

include_once("./footer.php");