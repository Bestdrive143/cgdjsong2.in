<?php
define('IN_SS', true);
include_once('./inc/init.php');

$title = 'Latest Updates';
include_once('./header.php');

$page = isset($ss->input['page']) ? (int)$ss->input['page'] : 1;

$start = ($page-1)*$ss->settings['updates_per_page'];

$query = $db->simple_select("updates", "uid");
$total = $db->num_rows($query);

echo '<h2>Latest Updates</h2>
<div class="updates">';

if($total != 0)
{
$options = ['order_by' => 'uid', 'order_dir' => 'desc', 'limit_start' => $start, 'limit' => $ss->settings['updates_per_page']];

$query = $db->simple_select("updates", "description, created_at", "status='A'", $options);
while($update = $db->fetch_array($query))
{
echo '<div><b>'.date("d M", $update['created_at']).':</b> '.$update['description'].'</div>';
}
}
else
{
echo '<div>No updates.!</div>';
}

echo '</div>';

$url = "{$ss->settings['url']}/latest_updates/{page}.html";

echo pagination($page, $ss->settings['updates_per_page'], $total, $url);

echo '<h2>Related Tags</h2>
<div><span style="font-size:10px;"><span style="color:#006400;">Tags :</span> Download, Free Download, All Mp3 Song Download, Movies Full Mp3 Songs, video song download, Mp4 HD Video Song Download, Download Ringtone, Movies Free Ringtone, Movies Wallpapers, HD Video Song Download</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; Updates</div>';

include_once('./footer.php');