<?php
define('IN_SS', true);
include_once("./inc/init.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$title = 'Manage Ads';
$message = '';

include_once('./header.php');

echo '<h2>Manage Ads</h2>';

$file = $ss->get_input('file');

if(!empty($file))
{
$ad = SS_ROOT.'assets/ads/'.$file.'.php';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_save' && $ss->request_method == 'post')
{
$fp = fopen($ad, 'w');
fwrite($fp, $ss->input['description']);
$message = 'Ad updated sucessfully.';
}

// Read Ad
$fp = fopen($ad, 'r');
$filesize = filesize($ad);

if($filesize > 0)
{
$content = fread($fp, $filesize);
}
else
{
$content = '';
}
fclose($fp);

if(!empty($message))
{
echo '<div class="toptitle">'.$message.'</div>';
}

echo '<div class="toptitle">
<form method="post" action="#">
<div><textarea name="description">'.htmlentities($content).'</textarea></div>
<div><input type="hidden" name="action" value="do_save" />
<input type="submit" value="Update Ad" /></div>
</form>
</div>';
}
else
{
$links = [['value' => 'header', 'title' => 'Header'], ['value' => 'footer', 'title' => 'Footer'], ['value' => 'acategory', 'title' => 'After Category Title'], ['value' => 'bcategory', 'title' => 'Before Category Title'], ['value' => 'afilelist', 'title' => 'After File List'], ['value' => 'bfilelist', 'title' => 'Before File List'], ['value' => 'adown', 'title' => 'After Download Link'], ['value' => 'bdown', 'title' => 'Before Download Link']];

foreach($links as $link)
{
echo '<div class="catRow"><a href="?file='.$link['value'].'">'.$link['title'].' Ad</a></div>';
}

}

echo '<div class="path"><a href="'.$settings['url'].'">Home</a> &#187; <a href="'.$settings['adminurl'].'">Admincp</a> &#187; <b>Ads</b></div>';

include_once('./footer.php');