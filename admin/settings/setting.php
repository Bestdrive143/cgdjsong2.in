<?php
define('IN_SS', true);
include('../inc/init.php');

if(!is_admin())
{
header('Location: '.$ss->settings['url'].'');
exit;
}

$gid = $ss->get_input('gid', 1);

$query = $db->simple_select("settingsgroups", "title", "gid='{$gid}'");
$settinggroup = $db->fetch_array($query);

if(!is_array($settinggroup))
{
header('Location: '.$ss->settings['adminurl'].'/settings');
exit;
}

$message = '';
$title = "Edit - {$settinggroup['title']}";

if(isset($ss->input['action']) && $ss->input['action'] == 'do_save' && $ss->request_method == 'post')
{
foreach($ss->input as $key => $value)
{
if($key == 'action')
{
continue;
}

$data = ['value' => $db->escape_string($value)];

$db->update_query("settings", $data, "gid='{$gid}' && name='".$db->escape_string($key)."'");
}

if(function_exists('rebuild_settings'))
{
rebuild_settings();
}

$message = 'Settings saved sucessfully.';
}

include('../header.php');

echo "<h2>{$settinggroup['title']}</h2>";

if(!empty($message))
{
echo '<div class="toptitle">'.$message.'</div>';
}

echo '<div>
<form method="post" action="#">
<input type="hidden" name="gid" value="'.$gid.'" />';

$options = ['order_by'=>'disporder', 'order_dir'=>'asc'];

$query = $db->simple_select("settings", "*", "gid='{$gid}'", $options);
while($setting = $db->fetch_array($query))
{
echo '<div class="toptitle"><b>'.$setting['title'].':</b> '.$setting['description'].'';

if($setting['type'] == 'text')
{
echo '<div><input type="text" name="'.$setting['name'].'" value="'.escape($setting['value']).'" /></div>';
}

else if($setting['type'] == 'yesno')
{
echo '<div><span class="green"><input type="radio" name="'.$setting['name'].'" value="1"'.($setting['value'] == 1 ? ' checked="sahil"' : '').' /> Yes</span> <span class="pink"><input type="radio" name="'.$setting['name'].'" value="0"'.($setting['value'] == 0 ? ' checked="sahil"' : '').' /> No</span></div>';
}

else if($setting['type'] == 'select')
{
echo '<div><select name="'.$setting['name'].'">';

$type = explode("\n",$setting['optionscode']);

for($i=1;$i<count($type);$i++)
{
$val = explode("=",$type[$i]);

if(trim($val[0]) != '')
{
echo "<option value='".$val[0]."'".($setting['value'] == $val[0] ? " selected='sahil'" : "").">".escape($val[1])."</option>";
}
}
echo '</select>
</div>';
}
echo '</div>';
}
echo '<div class="toptitle"><input type="hidden" name="action" value="do_save" /><input type="submit" name="save" value="Save" /></div>
</form>
</div>';

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; <a href="'.$ss->settings['adminurl'].'">Admincp</a> &#187; <b>Settings</b></div>';

include('../footer.php');