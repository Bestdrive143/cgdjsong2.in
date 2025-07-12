<?php
@set_time_limit(0);

define('IN_SS', true);
include_once('./inc/init.php');

include_once(SS_ROOT.'inc/class_watermark.php');

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$folder_count = 0;
$files_count = 0;

$title = 'Scan';
include_once('./header.php');

echo '<h2>Scan Folder</h2>';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_scan' && $ss->request_method == 'post')
{
$path = $ss->get_input('path');
$path = rtrim("/files".$path, '/');

$db_files = [];

$query = $db->simple_select("files", "path", "path LIKE '".$db->escape_string_like($path)."%'");
while($file = $db->fetch_array($query))
{
$db_files[] = $file['path'];
}

$folder_files = scaner("..$path");

foreach($folder_files as $f)
{
$f = rtrim($f, '/');

if(!in_array($f, $db_files))
{
$db_files[] = $f;

$query = $db->simple_select("files", "fid", "path='".$db->escape_string(dirname($f))."'");
$dir = $db->fetch_field($query, 'fid');

$name = basename($f);
$name = trim(preg_replace("/[-_]+/", "_", $name));
$name = str_replace('_', ' ', $name);

if(is_dir("..".$f))
{
$add = ["path" => $db->escape_string($f), "name" => $db->escape_string($name), "time" => TIME_NOW, "pid" => (int)$dir, "isdir" => 1];

$new = $db->insert_query("files", $add);
$folder_count += 1;
}
else
{
$add = ["path" => $db->escape_string($f), "name" => $db->escape_string($name), "time" => TIME_NOW, "pid" => (int)$dir, "size" => filesize("..".$f)];

$new = $db->insert_query("files", $add);
$files_count += 1;

$ext = pathinfo($name, PATHINFO_EXTENSION);

if($ext == 'mp4')
{
process_video(SS_ROOT.$f);
}
elseif(in_array($ext, array('png', 'gif', 'jpg', 'jpeg')))
{
process_image(SS_ROOT.$f);
}
elseif($ext == 'mp3')
{
process_audio(SS_ROOT.$f);
}

}
}
}

$tmp_files = array_map("_rtrim", $folder_files);
$extra = array_diff(array_merge($tmp_files, $db_files), array_intersect($tmp_files, $db_files));

foreach($extra as $extra)
{
$db->delete_query("files", "path='".$db->escape_string($extra)."'");
}

echo '<div class="toptitle">New folders: '.$folder_count.'</div>
<div class="toptitle">New files: '.$files_count.'</div>';
}
else
{
echo '<div class="toptitle">
<form method="post" action="#">
<div>Folder: (Name of folder inside files directory)</div>
<div><input type="text" name="path" value="/" /></div>
<div><input type="hidden" name="action" value="do_scan" />
<input type="submit" value="Scan" /></div>
</form>
</div>';
}

include_once('./footer.php');

function _rtrim($v)
{
return rtrim($v, "/");
}

function scaner($path)
{
static $f_arr = [];

@chmod($path, 0777);

$arr = glob($path.'/*');

if(is_array($arr))
{
foreach($arr as $vv)
{
$info = pathinfo($vv, PATHINFO_FILENAME);

if(substr($info, 0, 5) == '128kb' || substr($info, 0, 4) == '64kb' || substr($info, 0, 5) == '192kb')
continue;

if(is_dir($vv))
{
$f_arr[] = substr($vv, 2).'/';
scaner($vv);
}
else
{
$f_arr[] = substr($vv,2);
}
}
}
return $f_arr;
}