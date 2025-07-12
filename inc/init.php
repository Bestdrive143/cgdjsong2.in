<?php
error_reporting(E_ALL);

// Disallow direct access to this file for security reasons
if(!defined("IN_SS"))
{
die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_SS is defined.");
}

/* Defines the root directory for SS.

	Uncomment the below line and set the path manually
	if you experience problems.

	Always add a trailing slash to the end of the path.

	* Path to your copy of SS
 */
//define('SS_ROOT', "./");

// Attempt autodetection
if(!defined('SS_ROOT'))
{
define('SS_ROOT', dirname(dirname(__FILE__))."/");
}

define("TIME_NOW", time());

if(function_exists('date_default_timezone_set') && !ini_get('date.timezone'))
{
date_default_timezone_set('GMT');
}

require_once SS_ROOT."inc/functions.php";

require_once SS_ROOT."inc/class_core.php";
$ss = new SS;

$not_installed = false;

if(!file_exists(SS_ROOT."inc/config.php"))
{
$not_installed = true;
}
else
{
// Include the required core files
require_once SS_ROOT."inc/config.php";
$ss->config = &$config;

if(!isset($config['database']))
{
$not_installed = true;
}
}

if($not_installed !== false)
{
if(file_exists(SS_ROOT."install/index.php"))
{
header("Location: ./install/index.php");
exit;
}
}

if(empty($config['admin_dir']))
{
$config['admin_dir'] = "vbxpanel";
}

// Trigger an error if the installation directory exists
if(is_dir(SS_ROOT."install") && !file_exists(SS_ROOT."install/lock"))
{
$ss->trigger_generic_error("install_directory");
}

require_once SS_ROOT."inc/db_".$config['database']['type'].".php";

switch($config['database']['type'])
{
case "sqlite":
$db = new DB_SQLite;
break;
case "pgsql":
$db = new DB_PgSQL;
break;
case "mysqli":
$db = new DB_MySQLi;
break;
default:
$db = new DB_MySQL;
}

// Check if our DB engine is loaded
if(!extension_loaded($db->engine))
{
// Throw our super awesome db loading error
$ss->trigger_generic_error("sql_load_error");
}

// Connect to Database
define("TABLE_PREFIX", $config['database']['table_prefix']);
$db->connect($config['database']);
$db->set_table_prefix(TABLE_PREFIX);
$db->type = $config['database']['type'];

// Load Settings
if(file_exists(SS_ROOT."inc/settings.php"))
{
require_once SS_ROOT."inc/settings.php";
}

if(!file_exists(SS_ROOT."inc/settings.php") || empty($settings))
{
if(function_exists('rebuild_settings'))
{
rebuild_settings();
}
else
{
$options = ["order_by" => "title", "order_dir" => "ASC"];

$query = $db->simple_select("settings", "value, name", "", $options);

$settings = [];
while($setting = $db->fetch_array($query))
{
$setting['value'] = str_replace("\"", "\\\"", $setting['value']);
$settings[$setting['name']] = $setting['value'];
}
$db->free_result($query);
}
}

// Fix for people who for some specify a trailing slash on the URL
if(substr($settings['url'], -1) == "/")
{
$settings['url'] = substr($settings['url'], 0, -1);
}

$ss->settings = &$settings;
$ss->parse_cookies();
$ss->settings['adminurl'] = "{$ss->settings['url']}/{$ss->config['admin_dir']}";

session_start();

if(isset($ss->cookies['pass']) && $ss->cookies['pass'] == $ss->settings['adminpass'])
{
$_SESSION['adminpass'] = $ss->settings['adminpass'];
}