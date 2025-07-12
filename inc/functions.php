<?php

function get_execution_time()
{
static $time_start;

$time = microtime(true);

if(!$time_start)
{
$time_start = $time;
return;
}
// Timer has run, return execution time
else
{
$total = $time-$time_start;
$time_start = 0;

if($total < 0)
$total = 0;
return $total;
}
}

function show_errors($errors)
{
if(!is_array($errors))
{
$errors = (array)$errors;
}

if(!empty($errors))
{
echo '<div class="toptitle">';
foreach($errors as $error)
{
echo '<div>'.$error.'</div>';
}
echo '</div>';
}
}

function escape($string)
{
return htmlentities($string);
}

function convert_name($result)
{
$result = str_replace(' ', '_', $result);
$result = preg_replace("/[^A-Za-z0-9-_]/", "", $result);
$result = trim(preg_replace("/[-_]+/", "_", $result));
return $result;
}

function convert_filename($result)
{
$result = str_replace(' ', '_', $result);
$result = preg_replace("/[^A-Za-z0-9-_\.]/", "", $result);
$result = trim(preg_replace("/[-_]+/", "_", $result));
return $result;
}

function ss_img($image, $alt = '')
{
global $ss;

$img = "<img src=\"{$ss->settings['url']}/assets/images/{$image}\" alt=\"{$alt}\" />";
return $img;
}

function validate_utf8_string($input, $allow_mb4=true, $return=true)
{
// Valid UTF-8 sequence?
if(!preg_match('##u', $input))
{
$string = '';
$len = strlen($input);
for($i = 0; $i < $len; $i++)
{
$c = ord($input[$i]);
if($c > 128)
{
if($c > 247 || $c <= 191)
{
if($return)
{
$string .= '?';
continue;
}
else
{
return false;
}
}
elseif($c > 239)
{
$bytes = 4;
}
elseif($c > 223)
{
$bytes = 3;
}
elseif($c > 191)
{
$bytes = 2;
}
if(($i + $bytes) > $len)
{
if($return)
{
$string .= '?';
break;
}
else
{
return false;
}
}
$valid = true;
$multibytes = $input[$i];
while($bytes > 1)
{
$i++;
$b = ord($input[$i]);
if($b < 128 || $b > 191)
{
if($return)
{
$valid = false;
$string .= '?';
break;
}
else
{
return false;
}
}
else
{
$multibytes .= $input[$i];
}
$bytes--;
}
if($valid)
{
$string .= $multibytes;
}
}
else
{
$string .= $input[$i];
}
}
$input = $string;
}
if($return)
{
if($allow_mb4)
{
return $input;
}
else
{
return preg_replace("#[^\\x00-\\x7F][\\x80-\\xBF]{3,}#", '?', $input);
}
}
else
{
if($allow_mb4)
{
return true;
}
else
{
return !preg_match("#[^\\x00-\\x7F][\\x80-\\xBF]{3,}#", $input);
}
}
}

function ss_setcookie($name, $value="", $expires="", $httponly=false)
{
global $ss;

if(!$ss->settings['cookiepath'])
{
$ss->settings['cookiepath'] = "/";
}

if($expires == -1)
{
$expires = 0;
}
elseif($expires == "" || $expires == null)
{
$expires = TIME_NOW+(60*60*24*365); // Make the cookie expire in a years time
}
else
{
$expires = TIME_NOW+intval($expires);
}

$ss->settings['cookiepath'] = str_replace(array("\n","\r"), "", $ss->settings['cookiepath']);

$ss->settings['cookiedomain'] = str_replace(array("\n","\r"), "", $ss->settings['cookiedomain']);

$ss->settings['cookieprefix'] = str_replace(array("\n","\r", " "), "", $ss->settings['cookieprefix']);

// Versions of PHP prior to 5.2 do not support HttpOnly cookies and IE is buggy when specifying a blank domain so set the cookie manually
$cookie = "Set-Cookie: {$ss->settings['cookieprefix']}{$name}=".urlencode($value);

if($expires > 0)
{
$cookie .= "; expires=".@gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expires);
}

if(!empty($ss->settings['cookiepath']))
{
$cookie .= "; path={$ss->settings['cookiepath']}";
}

if(!empty($ss->settings['cookiedomain']))
{
$cookie .= "; domain={$ss->settings['cookiedomain']}";
}
if($httponly == true)
{
$cookie .= "; HttpOnly";
}

$ss->cookies[$name] = $value;
header($cookie, false);
}

function ss_unsetcookie($name)
{
global $ss;

$expires = -3600;
ss_setcookie($name, "", $expires);
unset($ss->cookies[$name]);
}

function convert_filesize($bytes = 0)
{
if($bytes == 0)
{
return "0.00 B";
}

$units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

$e = floor(log($bytes, 1024));

return round($bytes/pow(1024, $e ), 2).$units[$e];
}

function pagination($page, $per_page, $total_items, $url)
{
global $ss;

if($total_items <= $per_page)
{
return;
}

$pages = ceil($total_items / $per_page);

$pagination = "<div class=\"pgn\" align=\"center\">\n";

if($page > 1)
{
$prev = $page-1;
$prev_page = fetch_page_url($url, $prev);
$pagination .= "<a href=\"{$prev_page}\" class=\"pagination_prev\">Prev</a> \n";
}

// Maximum number of "page bits" to show
if(!$ss->settings['maxmultipagelinks'])
{
$ss->settings['maxmultipagelinks'] = 5;
}

$max_links = $ss->settings['maxmultipagelinks'];

$from = $page-floor($ss->settings['maxmultipagelinks']/2);

$to = $page+floor($ss->settings['maxmultipagelinks']/2);

if($from <= 0)
{
$from = 1;
$to = $from+$max_links-1;
}

if($to > $pages)
{
$to = $pages;
$from = $pages-$max_links+1;

if($from <= 0)
{
$from = 1;
}
}

if($to == 0)
{
$to = $pages;
}

if($from > 2)
{
$first = fetch_page_url($url, 1);
$pagination .= "<a href=\"{$first}\" title=\"Page 1\" class=\"pagination_first\">1</a> ... ";
}

for($i = $from; $i <= $to; ++$i)
{
$page_url = fetch_page_url($url, $i);

if($page == $i)
{
$pagination .= "<span class=\"cur\">{$i}</span> \n";
}
else
{
$pagination .= "<a href=\"{$page_url}\" title=\"Page {$i}\">{$i}</a> \n";
}
}

if($to < $pages)
{
$last = fetch_page_url($url, $pages);
$pagination .= "... <a href=\"{$last}\" title=\"Page {$pages}\" class=\"pagination_last\">{$pages}</a>";
}

if($page < $pages)
{
$next = $page+1;
$next_page = fetch_page_url($url, $next);
$pagination .= " <a href=\"{$next_page}\" class=\"pagination_next\">Next</a> \n";
}

$pagination .= "</div>";
return $pagination;
}


function fetch_page_url($url, $page)
{
if($page <= 1)
{
$find = array(
"-page-{page}",
"&amp;page={page}",
"{page}"
);

// Remove "Page 1" to the defacto URL
$url = str_replace($find, array("", "", $page), $url);
return $url;
}
else if(strpos($url, "{page}") === false)
{
// If no page identifier is specified we tack it on to the end of the URL
if(strpos($url, "?") === false)
{
$url .= "?";
}
else
{
$url .= "&amp;";
}
$url .= "page=$page";
}
else
{
$url = str_replace("{page}", $page, $url);
}
return $url;
}

function is_admin() 
{ 
global $ss; 

if(isset($_SESSION['adminpass']) && $_SESSION['adminpass'] == $ss->settings['adminpass']) 
return true; 
return false; 
} 

function watermark($image)
{
global $ss;

$text = $ss->settings['watermark_text'];
$color = '#FFFFFF';
$font = SS_ROOT.'assets/sahil.ttf';
$font_size = '12';
$angle = 90;
$offset_x = 0;
$offset_y = 0;
$drop_shadow = true;
$shadow_color = '#3E3E3E';
$mode = 1;
$img = new Zubrag_watermark($image);
$img->setShadow($drop_shadow, $shadow_color);
$img->setFont($font, $font_size);
$img->ApplyWatermark($text, $color, $angle, $offset_x, $offset_y);
$img->SaveAsFile($image);
$img->Free();
return true;
}

function upload_icon ($field, $fid)
{
global $ss;

$filetype = $_FILES[$field]['type'];
$up['maxSize'] = 1000*1000;
$up['types'] = array("image/jpeg", "image/png", "image/gif");

if(!in_array($filetype, $up['types']))
{
$errors[] = 'Image type not allowed.';
}

if($_FILES[$field]['size'] > $up['maxSize'])
{
$errors[] = 'File size must be less than 1 mb.';
}

if(empty($errors))
{
$icon = SS_ROOT."thumbs/{$fid}.png";

if(!move_uploaded_file($_FILES[$field]['tmp_name'], $icon))
{
echo '<div clas="toptitle">Icon not uploaded </div>';
}

if($ss->settings ['watermark_thumb'])
watermark($icon, 0, 30);
}
else
{
show_errors($errors);
}
}

function process_video($file)
{
global $ss;

if($ss->settings['watermark_videos'])
{
watermark_video($file);
}
}

function process_audio($file)
{
global $ss;

if($ss->settings['auto_tag'])
{
auto_tag($file);
}

if($ss->settings['auto_bitrate'])
{
bitrate_convert($file);
}
}

function process_image($file)
{
global $ss;

if($ss->settings['watermark_images'])
{
watermark($file);
}
}

function watermark_video($file)
{
global $ss;

$info = pathinfo($file);
$out = "{$info['dirname']}/ss_{$info['basename']}";
exec("ffmpeg -i '".$file."' -i '".SS_ROOT."".$ss->settings['watermark_image']."' -filter_complex 'overlay=10:main_h-overlay_h-10' '".$out."'");

if(file_exists($out))
{
unlink($file);
rename($out, $file);
}
}

function bitrate_convert($file)
{
$info = pathinfo($file);

$out = "{$info['dirname']}/128kb-{$info['basename']}";

$out2 = "{$info['dirname']}/64kb-{$info['basename']}";

$out3 = "{$info['dirname']}/192kb-{$info['basename']}";

exec("ffmpeg -i '".$file."' -ab 128k '".$out."'");

exec("ffmpeg -i '".$file."' -ab 64k '".$out2."'");

exec("ffmpeg -i '".$file."' -ab 192k '".$out3."'");

auto_tag($out);
auto_tag($out2);
auto_tag($out3);
}

function upload_file($field, $path)
{
global $ss, $db;

$name = $ss->get_input('name');
$errors = [];

if(trim($name) == '')
{
$name = preg_match("~(.*)\.(\w)~i", basename($_FILES[$field]['name'])) ? basename($_FILES[$field]['name']) : "file.dat";
}
else
{
$name = preg_match("~(.*)\.(\w)~i", basename($name)) ? basename($name) : "file.dat";
}

$namec = convert_filename($name);

// File path
$file_path = "{$path}/{$namec}";

if(file_exists(SS_ROOT.$file_path))
{
$errors[] = 'File already exists';
}

if(!file_exists($_FILES[$field]['tmp_name']))
{
$errors[] = 'File already exists';
}

$ext = pathinfo($name, PATHINFO_EXTENSION);

if(empty($errors))
{
if(move_uploaded_file($_FILES[$field]['tmp_name'], SS_ROOT.$file_path))
{
$message = '<div class="toptitle">'.$name.' file uploaded.</div>';

$query = $db->simple_select("files", "fid", "path='".$db->escape_string($path)."'");
$dirid = $db->fetch_field($query, 'fid');
if($dirid != 0)
{
$_dr = '';
foreach(explode('/', substr($path,7)) as $dr)
{
$_dr.="/".$dr;
if($_dr != '/')
{
$db->query("UPDATE`".TABLE_PREFIX."files` SET `time`='".time()."' WHERE `path` = '/files$_dr'");
}
}
}
$fid = $db->insert_query("files", ['name'=>$db->escape_string($name), 'path'=>$db->escape_string($file_path), 'pid'=>$dirid, 'time'=>time(),'size'=>filesize(SS_ROOT.$file_path)]);
echo $message;

if($ext == 'mp4')
{
process_video(SS_ROOT.$file_path);
}
elseif(in_array($ext, array('png', 'gif', 'jpg', 'jpeg')))
{
process_image(SS_ROOT.$file_path);
}
elseif($ext == 'mp3')
{
process_audio(SS_ROOT.$file_path);
}

return $fid;
}
else
{
echo '<div class="toptitle">'.$name.' file not uploaded.</div>';
return false;
}
}
else
show_errors ($errors);
}

function import_file($file, $path)
{
global $ss, $db;

$name = $ss->get_input('name');
$errors = [];

if(trim($name) == '')
{
$name = preg_match("~(.*)\.(\w)~i", basename($file)) ? basename($file) : "file.dat";
}
else
{
$name = preg_match("~(.*)\.(\w)~i", basename($name)) ? basename($name) : "file.dat";
}

$namec = convert_filename($name);

// File path
$file_path = "{$path}/{$namec}";

if(file_exists(SS_ROOT.$file_path))
{
$errors[] = 'File already exists';
}


$ext = pathinfo($name, PATHINFO_EXTENSION);

if(empty($errors))
{
if($ext == 'jad')
{
$lines = file($file);

foreach($lines as $line)
{
if(strpos($line, "MIDlet-Jar-URL:") !== FALSE)
{
$url = trim(str_replace("MIDlet-Jar-URL:", "", $line));
}
}

if($url)
{
$name = preg_match("~(.*)\.(\w)~i", basename($url)) ? basename($url) : "game_$i.jar";

$namec = convert_filename($name);
$file_path = "{$path}/{$namec}";

$file = $url;
}

}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $file);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
set_time_limit(3000);
curl_setopt($ch, CURLOPT_TIMEOUT, 3000) or show_errors('time limit exceed... Contact to Developer...(thesahil2@gmail.com) ', $url);
$outfile = fopen(SS_ROOT.$file_path, 'wb');
curl_setopt($ch, CURLOPT_FILE, $outfile) or show_errors('can not write destination file', $url);
curl_exec($ch) or error(' Error in copy source file.. ', $url, 7);
$info = curl_getinfo($ch);
fclose($outfile);

if(file_exists(SS_ROOT.$file_path))
{
$message = '<div class="toptitle">'.escape($name).' file uploaded</div>';

$query = $db->simple_select("files", "fid", "path='".$db->escape_string($path)."'");
$dirid = $db->fetch_field($query, 'fid');

if($dirid != 0)
{
$_dr = '';
foreach(explode('/', substr($path,7)) as $dr)
{
$_dr.="/".$dr;
if($_dr != '/')
{
$db->query("UPDATE`".TABLE_PREFIX."files` SET `time`='".TIME_NOW."' WHERE `path` = '".$db->escape_string('/files'.$_dr.'')."'");
}
}
}
$fid = $db->insert_query("files", ['name' => $db->escape_string($name), 'path' => $db->escape_string($file_path), 'pid' => $dirid, 'time' => TIME_NOW, 'size' => filesize(SS_ROOT.$file_path)]);
echo $message;

if($ext == 'mp4')
{
process_video(SS_ROOT.$file_path);
}
elseif(in_array($ext, array('png', 'gif', 'jpg', 'jpeg')))
{
process_image(SS_ROOT.$file_path);
}
elseif($ext == 'mp3')
{
process_audio(SS_ROOT.$file_path);
}

return $fid;
}
else
{
echo '<div class="toptitle">'.$name.' not uploaded</div>';
return false;
}
}
else
show_errors ($errors);
}


function deleteAll($directory,$empty=false)
{
if(substr($directory,-1)=="/") {
$directory=substr($directory,0,-1);
}
if(!file_exists($directory)||!is_dir($directory)) {
return false;
}elseif(!is_readable($directory)) {
return false;
}else{
$directoryHandle=opendir($directory);
while($contents=readdir($directoryHandle)) {
if($contents!='.'&&$contents!='..') {
$path=$directory."/".$contents;
if(is_dir($path)) {
deleteAll($path);
}else{
unlink($path);
}
}
}
closedir($directoryHandle);
if($empty==false) {
if(!rmdir($directory)) {
return false;
}
}
return true;
}
}

function dirmv($source, $destination)
{
if(is_dir($source))
{
@mkdir($destination);
$directory = dir($source);
while(FALSE!==($readdirectory=$directory->read()))
{
if($readdirectory == '.'||$readdirectory=='..')
{
continue;
}
$PathDir = $source.'/'.$readdirectory;
if(is_dir($PathDir))
{
dirmv($PathDir,$destination.'/'.$readdirectory);
continue;
}
rename($PathDir,$destination.'/'.$readdirectory);
}
$directory->close();
}
else
{
rename($source,$destination);
}
deleteAll($source);
}

function auto_tag($file)
{
global $ss, $db;

$pid = $ss->get_input("pid", 0);

if($pid != 0)
{
$query = $db->simple_select("files", "name", "fid={$pid}");
$artist = $db->fetch_field($query);
}
else
{
$artist= $ss->settings['mp3_artist'];
}

$info = pathinfo($file, PATHINFO_FILENAME);

$img = SS_ROOT."/".$ss->settings['mp3_albumart'];

$mp3['title'][] = "".$info." - ".$ss->settings['title']."";
$mp3['artist'][] = $artist;
$mp3['album'][] = $artist;
$mp3['year'][] = $ss->settings['mp3_year'];
$mp3['tracknumber'][] = $ss->settings['mp3_track'];
$mp3['band'][] = $ss->settings['mp3_band'];
$mp3['genre'][] = $ss->settings['mp3_genre'];
$mp3['publisher'][] = $ss->settings['mp3_publisher'];
$mp3['composer'][] = $ss->settings['mp3_composer'];
$mp3['comment'][] = $ss->settings['mp3_comment'];
$mp3['track'][] = $ss->settings['mp3_track'];
$mp3['url_user'][] = $ss->settings['mp3_url_user'];
$mp3['original_artist'][] = $ss->settings['mp3_original_artist'];
$mp3['encoded_by'][] = $ss->settings['mp3_encoded_by'];

if(is_file($img))
{
if($fd = @fopen($img, 'rb'))
{
$APICdata = fread($fd, filesize($img));
fclose ($fd);

list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($img);

$imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');

if(isset($imagetypes[$APIC_imageTypeID]))
{
$mp3['attached_picture'][0]['data'] = $APICdata;
$mp3['attached_picture'][0]['picturetypeid'] = 0;
$mp3['attached_picture'][0]['description']   = $img;
$mp3['attached_picture'][0]['mime']          = 'image/'.$imagetypes[$APIC_imageTypeID];
}
else
{
echo '<B>invalid image format (only GIF, JPEG, PNG)</B><BR>';
}
}
else
{
echo '<B>cannot open album art</B><BR>';
}
}

return write_tags($file, $mp3);
}

function write_tags($file, $mp3)
{
include_once('getid3/getid3.php');
include_once('getid3/write.php');

$writeTags = new getid3_writetags;
$writeTags->filename = $file;
$writeTags->tagformats = array('id3v1', 'id3v2.3');
$writeTags->overwrite_tags = true;
$writeTags->tag_encoding = 'UTF-8';

$writeTags->tag_data = $mp3;

if($writeTags->WriteTags())
{
echo '<div class="toptitle">Successfully wrote tags to : '.$file.'</div>';

if(!empty($writeTags->warnings))
{
echo '<div class="toptitle">There were some warnings:<br/> '.implode('<br/><br/>', $writeTags->warnings).'</div>';
}
}
else
{
echo '<div class="title">Failed to write tags to '.$file.'<br/>'.implode('<br/><br/>', $writeTags->errors).'</div>';
}
}

function mp3tags_writter($file)
{
global $ss;

$mp3['title'][] = $ss->get_input('title');
$mp3['artist'][] = $ss->get_input('artist');
$mp3['album'][] = $ss->get_input('album');
$mp3['year'][] = $ss->get_input('year');
$mp3['track'][] = $ss->get_input('track');
$mp3['band'][] = $ss->get_input('band');
$mp3['genre'][] = $ss->get_input('genre');
$mp3['publisher'][] = $ss->get_input('publisher');
$mp3['composer'][] = $ss->get_input('composer');
$mp3['comment'][] = $ss->get_input('comment');
$mp3['track'][] = $ss->settings['mp3_track'];
$mp3['url_user'][] = $ss->settings['mp3_url_user'];
$mp3['original_artist'][] = $ss->settings['mp3_original_artist'];
$mp3['encoded_by'][] = $ss->settings['mp3_encoded_by'];

$url = $ss->get_input('image_url');
$img = '';

if($ss->get_input('remove_album', 1) == 0)
{
if(!empty($_FILES['image_file']['name']))
{
$fid = $ss->get_input('fid');

upload_icon('image_file', $fid);

$img = SS_ROOT.'thumbs/'.$fid.'.png';
}
else if(!empty($url))
{
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
set_time_limit(3000);
curl_setopt($ch, CURLOPT_TIMEOUT, 3000) or show_errors('time limit exceed... Contact to Developer...(thesahil2@gmail.com) ', $url);
$outfile = fopen(SS_ROOT.'thumbs/'.$fid.'.png', 'wb');
curl_setopt($ch, CURLOPT_FILE, $outfile) or show_errors('can not write destination file', $url);
curl_exec($ch) or error(' Error in copy source file.. ', $url, 7);
$info = curl_getinfo($ch);
fclose($outfile);

if(file_exists(SS_ROOT.$file_path))
{
$img = SS_ROOT.'thumbs/'.$fid.'.png';
}
}
elseif($ss->get_input('image_default', 1) == 1)
{
$img = SS_ROOT."/".$ss->settings['mp3_albumart'];
}
else
{
include_once('getid3/getid3.php');
$name = SS_ROOT.'thumbs/'.$fid.'.png';
$width = 80;
$height = 80;

$getID3 = new getID3;
$fileinfo = $getID3->analyze($file);
$picture = @$fileinfo['id3v2']['APIC'][0]['data'];
if($picture)
{
$im = imagecreatefromstring($picture);
$im2 = imagecreatetruecolor($width, $height);
imageCopyResized($im2, $im, 0, 0, 0, 0, $width, $height, imagesx($im),
imagesy($im));
imagepng($im2, $name);
$img = $name;
}
}

if(is_file($img))
{
if($fd = @fopen($img, 'rb'))
{
$APICdata = fread($fd, filesize($img));
fclose ($fd);

list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($img);

$imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');

if(isset($imagetypes[$APIC_imageTypeID]))
{
$mp3['attached_picture'][0]['data'] = $APICdata;
$mp3['attached_picture'][0]['picturetypeid'] = 0;
$mp3['attached_picture'][0]['description']   = $img;
$mp3['attached_picture'][0]['mime']          = 'image/'.$imagetypes[$APIC_imageTypeID];
}
else
{
echo '<B>invalid image format (only GIF, JPEG, PNG)</B><BR>';
}
}
else
{
echo 'Not able to open albumart file';
}
}
}

write_tags($file, $mp3);
}

function get_tags($filename)
{
global $ss;

include_once('getid3/getid3.php');

$mp3_tagformat = 'UTF-8';
$mp3Tags = new getID3;
$mp3Tags->setOption(array('encoding'=>$mp3_tagformat, 'tempdir'=>'./temp/'));
$tagInfo = $mp3Tags->analyze($filename);

$result = array();
$audio = $tagInfo['audio'];
$tags = $tagInfo['tags'];
$id3v1 = $tagInfo['id3v1'];
$id3v2 = $tagInfo['id3v2'];
$img1 = $id3v2['APIC'][0];
$img2 = $tagInfo['comments']['picture'][0];

$result['title'] = ($id3v1['title'] ? $id3v1['title'] :
($tags['id3v1']['title'][0] ? $tags['id3v1']['title'][0] :
($tags['id3v2']['title'][0] ? $tags['id3v2']['title'][0] : "")));

$result['artist'] = ($id3v1['artist'] ? $id3v1['artist'] :
($tags['id3v1']['artist'][0] ? $tags['id3v1']['artist'][0] :
($tags['id3v2']['artist'][0] ? $tags['id3v2']['artist'][0] : "")));

$result['album'] = ($id3v1['album'] ? $id3v1['album'] :
($tags['id3v1']['album'][0] ? $tags['id3v1']['album'][0] :
($tags['id3v2']['album'][0] ? $tags['id3v2']['album'][0] : "")));

$result['genre'] = ($id3v1['genre'] ? $id3v1['genre'] :
($tags['id3v1']['genre'][0] ? $tags['id3v1']['genre'][0] :
($tags['id3v2']['genre'][0] ? $tags['id3v2']['genre'][0] : "")));

$result['year'] = ($id3v1['year'] ? $id3v1['year'] :
($tags['id3v1']['year'][0] ? $tags['id3v1']['year'][0] :
($tags['id3v2']['year'][0] ? $tags['id3v2']['year'][0] : "")));

$result['track'] = ($id3v1['track'] ? $id3v1['track'] :
($tags['id3v1']['track'][0] ? $tags['id3v1']['track'][0] :
($tags['id3v2']['track'][0] ? $tags['id3v2']['track'][0] : "")));

$result['comment'] = ($id3v1['comment'] ? $id3v1['comment'] :
($tags['id3v1']['comment'][0] ? $tags['id3v1']['comment'][0] :
($tags['id3v2']['comment'][0] ? $tags['id3v2']['comment'][0] : "")));

$result['band'] = $tags['id3v2']['band'][0];

$result['publisher'] = $tags['id3v2']['publisher'][0];
$result['composer'] = $tags['id3v2']['composer'][0];

$result['img'] = ($img1['data'] ? $img1 : $img2);
$result['img_data'] = ($img1['data'] ? $img1['data'] : $img2['data']);
$result['img_id'] = $img1['picturetypeid'];
$result['img_type'] = $img1['picturetype'];
$result['img_name'] = ($img1['description'] ? $img1['description'] : $ss->settings['title'].'.jpg');
$result['img_mime'] = ($img1['mime'] ? $img1['mime'] : $img2['image_mime']);
return $result;
}