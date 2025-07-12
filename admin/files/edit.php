<?php
define('IN_SS', true);
include_once("../inc/init.php");

include_once(SS_ROOT."inc/class_watermark.php");

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$fid = $ss->get_input('fid', 1);
$message = '';

$query = $db->simple_select("files", "*", "fid='{$fid}'");
$file = $db->fetch_array($query);

if(!$file)
{
header("Location: {$ss->settings['url']}");
exit;
}

if($file['isdir'] == 1)
{
$verb = 'Folder';
}
else
{
$verb = 'File';
}

$title = 'Edit '.$verb.'';
include_once("../header.php");

echo '<h2>Edit '.$verb.'</h2>';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_edit' && $ss->request_method == 'post')
{
$name = $ss->get_input('name');

if($name != $file['name'])
{
$len = strlen($file['name']);
$path = substr($file['path'], 0, -$len);
$path = "{$path}".convert_filename($name)."";

$query = $db->simple_select("files", "fid", "path='".$db->escape_string($path)."'");
$count = $db->num_rows($query);

if($count != 0)
{
$message = 'File/Folder already exists.';
}
}
else
{
$path = $file['path'];
}

$description = $ss->get_input('description');
$tag = $ss->get_input('tag', 1);
$disporder = $ss->get_input('disporder', 1);
$use_icon = $ss->get_input('use_icon', 1);

$data = ['name' => $db->escape_string($name), 'description' => $db->escape_string($description), 'disporder' => $disporder, 'tag' => $tag, 'path' => $db->escape_string($path), 'use_icon' => $use_icon];

if(empty($message))
{
$query = $db->update_query("files", $data, "fid='".$fid."'");

if($path != $file['path'])
{
rename(SS_ROOT.$file['path'], SS_ROOT.$path);

if($file['isdir'] == 1)
{
$db->query("UPDATE `".TABLE_PREFIX."files` SET `path`=replace(`path`,'".$db->escape_string($file['path'])."','".$db->escape_string($path)."') WHERE `path` LIKE '".$db->escape_string_like($file['path'])."%'");
}
}

if(isset($_FILES['icon']) && $_FILES['icon']['name'] != '')
{
upload_icon('icon', $fid);
}

$message = 'File detail updated sucessfully.';

$file['name'] = $name;
$file['description'] = $description;
$file['disporder'] = $disporder;
$file['tag'] = $tag;
}
}

if(isset($ss->input['action']) && $ss->input['action'] == 'remove' && $ss->request_method == 'get')
{
if(unlink(SS_ROOT.'/thumbs/'.$fid.'.png'))
{
$message = 'Icon has been deleted.';
}
else
{
$message = 'Unable to delete Icon.';
}
}

if(!empty($message))
{
echo '<div class="toptitle">'.$message.'</div>';
}

echo '<div>
<form method="post" action="#" enctype="multipart/form-data">
<div class="toptitle">
<div>Name:</div>
<div><input type="text" name="name" value="'.escape($file['name']).'" maxlength="100" /></div>
</div>
<div class="toptitle">
<div>Description:</div>
<div><textarea name="description" />'.escape($file['description']).'</textarea></div>
</div>';

if($file['isdir'] == 1)
{
echo '<div class="toptitle">
<div>Display Order:</div>
<div><input type="text" name="disporder" value="'.escape($file['disporder']).'" /></div>
</div>
<div class="toptitle">
<div>Use Icon: </div>
<div><input type="radio" name="use_icon" value="1" '.($file['use_icon'] == 1 ? 'checked ' : '').'/> Yes <input type="radio" name="use_icon" value="0" '.($file['use_icon'] == 0 ? 'checked ' : '').'/> No</div>
</div> ';
}

echo '<div class="toptitle">
<div>Icon:</div>
<div><input type="file" name="icon" /></div>';

if(file_exists(''.SS_ROOT.'/thumbs/'.$file['fid'].'.png'))
{
echo '<div><img src="'.$ss->settings['url'].'/thumbs/'.$file['fid'].'.png" alt="" width="80px" height="80px" /><br />
<a href="'.$ss->settings['adminurl'].'/files/edit.php?fid='.$file['fid'].'&action=remove">Delete Icon</a></div>';
}

echo '</div>
<div class="toptitle">
<div>Tag:</div>
<div><input type="radio" name="tag" value="1" '.($file['tag'] == 1 ? 'checked ' : '').'/> New <input type="radio" name="tag" value="2" '.($file['tag'] == 2 ? 'checked ' : '').'/> Update <input type="radio" name="tag" value="0" '.($file['tag'] == 0 ? 'checked ' : '').'/> No Tag</div>
</div>
<div class="toptitle">
<div><input type="hidden" name="action" value="do_edit" />
<input type="submit" value="Edit" /></div>
</div>
</form>
</div>';

if($file['isdir'] == 0)
{
$ext = pathinfo($file['path'], PATHINFO_EXTENSION);

if($ext == 'mp3')
{
echo '<h2>Mp3 Tag Editor</h2>';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_change')
{
$path = SS_ROOT.$file['path'];
mp3tags_writter($path);
}

$tags = get_tags(SS_ROOT.$file['path']);

echo '<div>
<form action="#" method="post" enctype="multipart/form-data">
<div class="toptitle">
<div>Title:</div>
<div><input type="text" name="title" value="'.escape($tags['title']).'" /></div>
</div>
<div class="toptitle">
<div>Artist:</div>
<div><input type="text" name="artist" value="'.escape($tags['artist']).'" /></div>
</div>
<div class="toptitle">
<div>Album:</div>
<div><input type="text" name="album" value="'.escape($tags['album']).'" /></div>
</div>
<div class="toptitle">
<div>Genre:</div>
<div><input type="text" name="genre" value="'.escape($tags['genre']).'" /></div>
</div>
<div class="toptitle">
<div>Year:</div>
<div><input type="text" name="year" value="'.escape($tags['year']).'" /></div>
</div>
<div class="toptitle">
<div>Track:</div>
<div><input type="text" name="track" value="'.escape($tags['track']).'" /></div>
</div>
<div class="toptitle">
<div>Band:</div>
<div><input type="text" name="band" value="'.escape($tags['band']).'" /></div>
</div>
<div class="toptitle">
<div>Publisher:</div>
<div><input type="text" name="publisher" value="'.escape($tags['publisher']).'" /></div>
</div>
<div class="toptitle">
<div>Composer:</div>
<div><input type="text" name="composer" value="'.escape($tags['composer']).'" /></div>
</div>
<div class="toptitle">
<div>Comment:</div>
<div><input type="text" name="comment" value="'.escape($tags['comment']).'" /></div>
</div>';

if(file_exists(SS_ROOT.$ss->settings['mp3_albumart']))
{
echo '<div class="toptitle">
<div>Default Cover album:</div>
<div><img src="'.$ss->settings['url'].'/'.$ss->settings['mp3_albumart'].'" width="80px" height="80px" /></div>
<div><input type="checkbox" name="image_default" value="1"> Use this image ?</div>
</div>';
}

echo '<div class="toptitle">
<div>Upload Image (jpg, png, or gif only):</div>
<div><input type="file" name="image_file" /></div>
</div>
<div class="toptitle">
<div>Import Image from URL (jpg, png, or gif only):</div>
<div><input type="text" name="image_url" value="" /></div>
</div>
<div class="toptitle">
<div><input type="checkbox" name="image_remove" value="1"> Remove Image Album ?</div>
<div><input type="hidden" name="action" value="do_change" />
<input type="submit" value="Submit" /></div>
</div>
</form>
</div>';
}
}

echo '<div class="catRow"><a href="'.$settings['adminurl'].'/files/move.php?fid='.$fid.'">Move '.$verb.'</a></div>';
echo '<div class="catRow"><a href="'.$settings['adminurl'].'/files/delete.php?fid='.$fid.'">Delete '.$verb.'</a></div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <b>Edit '.$verb.'</b></div>';

include_once("../footer.php");