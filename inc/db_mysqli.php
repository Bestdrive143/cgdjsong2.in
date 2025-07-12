<?php
class DB_MySQLi
{
public $title = "MySQLi";
public $type;
public $query_count = 0;
public $error_reporting = 1;
public $current_link;
public $table_prefix;
public $engine = "mysqli";
public $db_encoding = "utf8";
public $query_time = 0;

function connect($config)
{
$this->db_encoding = $config['encoding'];
$connect_function = "mysqli_connect";
$persist = "";

if(!empty($config['pconnect']) && version_compare(PHP_VERSION, '5.3.0', '>='))
{
$persist = 'p:';
}

get_execution_time();

// Specified a custom port for this connection?
$port = 0;

if(strstr($config['hostname'],':'))
{
list($hostname, $port) = explode(":", $config['hostname'], 2);
}

if($port)
{
$this->current_link = @$connect_function($persist.$hostname, $config['username'], $config['password'], "", $port);
}
else
{
$this->current_link = @$connect_function($persist.$config['hostname'], $config['username'], $config['password']);
}

$time_spent = get_execution_time();
$this->query_time =   $time_spent;

if(!$this->current_link)
{
$this->error("[READ] Unable to connect to MySQL server");
return false;
}

// Select databases
if(!$this->select_db($config['database']))
{
return -1;
}
else
{
return true;
}
}

function select_db($database)
{
$success = @mysqli_select_db($this->current_link, $database) or $this->error("[READ] Unable to select database", $this->current_link);

if($success && $this->db_encoding)
{
@mysqli_set_charset($this->current_link, $this->db_encoding);
}

return $success;
}

function query($string, $hide_errors=0)
{
get_execution_time();

$query = @mysqli_query($this->current_link, $string);

if($this->error_number() && !$hide_errors)
{
$this->error($string);
exit;
}

$query_time = get_execution_time();
$this->query_time  =  $query_time;
$this->query_count  ;
return $query;
}

function fetch_array($query, $resulttype=MYSQLI_ASSOC)
{
switch($resulttype)
{
case MYSQLI_NUM:
case MYSQLI_BOTH:
break;
default:
$resulttype = MYSQLI_ASSOC;
break;
}

$array = mysqli_fetch_array($query, $resulttype);

return $array;
}

function fetch_field($query, $field, $row=false)
{
if($row !== false)
{
$this->data_seek($query, $row);
}
$array = $this->fetch_array($query);
return $array[$field];
}

function data_seek($query, $row)
{
return mysqli_data_seek($query, $row);
}

function num_rows($query)
{
return mysqli_num_rows($query);
}

function insert_id()
{
$id = mysqli_insert_id($this->current_link);
return $id;
}

function close()
{
@mysqli_close($this->current_link);
}

function error_number()
{
if($this->current_link)
{
return mysqli_errno($this->current_link);
}
else
{
return mysqli_connect_errno();
}
}
function error_string()
{
if($this->current_link)
{
return mysqli_error($this->current_link);
}
else
{
return mysqli_connect_error();
}
}

function error($string = "")
{
if($this->error_reporting)
{
trigger_error("<strong>[SQL] [".$this->error_number()."] ".$this->error_string()."</strong><br />{$string}", E_USER_ERROR);
}
else
{
return false;
}
}

function affected_rows()
{
return mysqli_affected_rows($this->current_link);
}

function num_fields($query)
{
return mysqli_num_fields($query);
}

function simple_select($table, $fields="*", $conditions="", $options=array())
{
$query = "SELECT ".$fields." FROM ".$this->table_prefix.$table;

if($conditions != "")
{
$query .= " WHERE ".$conditions;
}

if(isset($options['group_by']))
{
$query .= " GROUP BY ".$options['group_by'];
}

if(isset($options['order_by']))
{
$query .= " ORDER BY ".$options['order_by'];

if(isset($options['order_dir']))
{
$query .= " ".strtoupper($options['order_dir']);
}
}

if(isset($options['limit_start']) && isset($options['limit']))
{
$query .= " LIMIT ".$options['limit_start'].", ".$options['limit'];
}
else if(isset($options['limit']))
{
$query .= " LIMIT ".$options['limit'];
}
return $this->query($query);
}

function insert_query($table, $array)
{
if(!is_array($array))
{
return false;
}

$fields = "`".implode("`,`", array_keys($array))."`";
$values = implode("','", $array);

$this->query("INSERT  INTO {$this->table_prefix}{$table} (".$fields.") VALUES ('".$values."')");
return $this->insert_id();
}

function insert_query_multiple($table, $array)
{
if(!is_array($array))
{
return false;
}

// Field names
$fields = array_keys($array[0]);
$fields = "`".implode("`,`", $fields)."`";
$insert_rows = array();

foreach($array as $values)
{
$insert_rows[] = "('".implode("','", $values)."')";
}
$insert_rows = implode(", ", $insert_rows);

$this->query("INSERT INTO {$this->table_prefix}{$table} ({$fields}) VALUES {$insert_rows}");
}

function update_query($table, $array, $where="", $limit="", $no_quote=false)
{
if(!is_array($array))
{
return false;
}
$comma = "";
$query = "";
$quote = "'";

if($no_quote == true)
{
$quote = "";
}

foreach($array as $field => $value)
{
$query .= $comma."`".$field."`={$quote}{$value}{$quote}";
$comma = ', ';
}

if(!empty($where))
{
$query .= " WHERE $where";
}

if(!empty($limit))
{
$query .= " LIMIT $limit";
}
return $this->query("UPDATE {$this->table_prefix}$table SET $query");
}

function delete_query($table, $where="", $limit="")
{
$query = "";
if(!empty($where))
{
$query .= " WHERE $where";
}
if(!empty($limit))
{
$query .= " LIMIT $limit";
}
return $this->query("DELETE FROM {$this->table_prefix}$table $query");
}

function escape_string($string)
{
if($this->db_encoding == 'utf8')
{
$string = validate_utf8_string($string, false);
}
elseif($this->db_encoding == 'utf8mb4')
{
$string = validate_utf8_string($string);
}
if(function_exists("mysqli_real_escape_string") && $this->current_link)
{
$string = mysqli_real_escape_string($this->current_link, $string);
}
else
{
$string = addslashes($string);
}
return $string;
}

function free_result($query)
{
return mysqli_free_result($query);
}

function escape_string_like($string)
{
return $this->escape_string(str_replace(array('%', '_') , array('\\%' , '\\_') , $string));
}

function optimize_table($table)
{
$this->write_query("OPTIMIZE TABLE ".$this->table_prefix.$table."");
}

function analyze_table($table)
{
$this->write_query("ANALYZE TABLE ".$this->table_prefix.$table."");
}

function set_table_prefix($prefix)
{
$this->table_prefix = $prefix;
}
}