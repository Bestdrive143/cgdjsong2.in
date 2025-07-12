<?php
define('IN_SS', true);
include_once('./inc/init.php');


$folder['name'] = 'Home';
$folder['use_icon'] = 0;
$key = $ss->get_input('find');
$pid = $ss->get_input('pid', 1);
$page = isset($ss->input['page']) ? (int)$ss->input['page'] : 1;
$title = 'Search';
$total = 0;

if($pid == 0)
{
$where = '';
}
else
{
$where = 'pid='.$pid.' AND ';
}

include_once('./header.php');

echo '<h2>Search</h2>';

if(isset($ss->input['action']) && $ss->input['action'] == 'do_search')
{
if(strlen($key) <= 1)
{
$errors[] = 'Search keyword must contain atleast 2 characters.';
}

if(empty($errors))
{
$search_words = explode(" ", $key);

foreach($search_words as $search_word)
{
$where1[] = "`name` LIKE '%$search_word%'";
$where2[] = "`description` LIKE '%$search_word%'";
}
$where_text = "(".implode("AND", $where1).") OR (".implode("AND", $where2).")";

$query = $db->simple_select("files", "fid", "{$where}{$where_text}");
$total = $db->num_rows($query);
}
else
{
show_errors($errors);
}
}

echo '<div class="search">
<form action="'.$ss->settings['url'].'/files/search.html" method="get">Search : <input type="text" name="find" value="'.escape($key).'" /><input type="hidden" name="pid" value="'.$pid.'" /><input type="hidden" name="action" value="do_search" /><input type="submit" value="Search" /></form></div>';

if($total > 0)
{
$start = ($page-1)*$ss->settings['files_per_page'];

$query = $db->simple_select("files", "fid, name, size, isdir, path, tag, dcount", "{$where}{$where_text}", ['order_by' => 'isdir DESC, disporder ASC', 'limit_start' => $start, 'limit' => $ss->settings['files_per_page']]);

while($file = $db->fetch_array($query))
{
if($file['isdir'] == 1)
{
echo '<div class="catRow"><a href="'.$ss->settings['url'].'/categorylist/'.$file['fid'].'/'.convert_name($file['name']).'.html"><div>'.escape($file['name']).'';

if($ss->settings['show_filecount'])
{
$counter = $db->simple_select("files", "fid", "path LIKE '".$db->escape_string_like($file['path'])."%' AND `isdir` = '0'");
echo ' ['.$db->num_rows($counter).'] ';
}

if($file['tag'] == 1)
{
echo ' '.ss_img('new.png', "New").'';
}
else if($file['tag'] == 2)
{
echo ' '.ss_img('updated.png', "Updated").'';
}

echo '</div></a></div>';
}
else
{
echo '<div class="fl"><a href="'.$ss->settings['url'].'/download/'.$file['fid'].'/'.convert_name($file['name']).'.html" class="fileName"><div><div>';

if(file_exists(SS_ROOT.'/thumbs/'.$file['fid'].'.png'))
{
echo '<img src="'.$ss->settings['url'].'/thumbs/'.$file['fid'].'.png" alt="'.escape($file['name']).'" width="60" height="65" />';
}
else if($folder['use_icon'] == 1 && file_exists(SS_ROOT.'/thumbs/'.$folder['fid'].'.png'))
{
echo '<img src="'.$ss->settings['url'].'/thumbs/'.$folder['fid'].'.png" alt="'.escape($file['name']).'" width="60" height="65" />';
}
else 
{
echo '<img src="'.$ss->settings['url'].'/icon.php?file='.base64_encode($file['path']).'&fid='.$file['fid'].'" alt="'.escape($file['name']).'" width="60" height="65" />';
}

echo '</div><div>'.escape($file['name']).'';

if($file['tag'] == 1)
{
echo ' '.ss_img('new.png', "New").'';
}
else if($file['tag'] == 2)
{
echo ' '.ss_img('updated.png', "Updated").'';
}

echo '<br /><span>['.convert_filesize($file['size']).']</span><br /><span>'.$file['dcount'].' Download</span></div></div></a></div>';
}
}
 $url = "{$ss->settings['url']}/search.php?key=".escape($key)."&pid={$pid}";
echo pagination($page, $ss->settings['files_per_page'], $total, $url);
}
else
{
echo '<div class="catRow">No results found</div>';
}

echo '<div class="path"><a href="'.$ss->settings['url'].'">Home</a> &#187; Search</div>';

include_once('./footer.php');