<?php
class SS {
	/**
	 * Input variables received from the outer world.
	 *
	 * @var array
	 */
	public $input = array();

	/**
	 * Cookie variables received from the outer world.
	 *
	 * @var array
	 */
	public $cookies = array();

	/**
	 * SS settings.
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * SS configuration.
	 *
	 * @var array
	 */
	public $config = array();

	/**
	 * The request method that called this page.
	 *
	 * @var string.
	 */
	public $request_method = "";

	/**
	 * Variables that need to be clean.
	 *
	 * @var array
	 */
	public $clean_variables = array();

	/**
	 * Variables that are to be ignored from cleansing process
	 *
	 * @var array
	 */
	public $ignore_clean_variables = array();

	/**
	 * String input constant for use with get_input().
	 *
	 * @see get_input
	 */
	const INPUT_STRING = 0;
	/**
	 * Integer input constant for use with get_input().
	 *
	 * @see get_input
	 */
	const INPUT_INT = 1;
	/**
	 * Array input constant for use with get_input().
	 *
	 * @see get_input
	 */
	const INPUT_ARRAY = 2;
	/**
	 * Float input constant for use with get_input().
	 *
	 * @see get_input
	 */
	const INPUT_FLOAT = 3;
	/**
	 * Boolean input constant for use with get_input().
	 *
	 * @see get_input
	 */
	const INPUT_BOOL = 4;

	/**
	 * Constructor of class.
	 *
	 * @return SS
	 */
	function __construct()
	{
		// Set up SS
		$protected = array("_GET", "_POST", "_SERVER", "_COOKIE", "_FILES", "_ENV", "GLOBALS");
		foreach($protected as $var)
		{
			if(isset($_POST[$var]) || isset($_GET[$var]) || isset($_COOKIE[$var]) || isset($_FILES[$var]))
			{
				die("Hacking attempt");
			}
		}

		if(defined("IGNORE_CLEAN_VARS"))
		{
			if(!is_array(IGNORE_CLEAN_VARS))
			{
				$this->ignore_clean_variables = array(IGNORE_CLEAN_VARS);
			}
			else
			{
				$this->ignore_clean_variables = IGNORE_CLEAN_VARS;
			}
		}

		// Determine Magic Quotes Status (< PHP 6.0)
		if(version_compare(PHP_VERSION, '6.0', '<'))
		{
			if(@get_magic_quotes_gpc())
			{
				$this->strip_slashes_array($_POST);
				$this->strip_slashes_array($_GET);
				$this->strip_slashes_array($_COOKIE);
			}
			@set_magic_quotes_runtime(0);
			@ini_set("magic_quotes_gpc", 0);
			@ini_set("magic_quotes_runtime", 0);
		}

		// Determine input
		$this->parse_incoming($_GET);
		$this->parse_incoming($_POST);

		if($_SERVER['REQUEST_METHOD'] == "POST")
		{
			$this->request_method = "post";
		}
		else if($_SERVER['REQUEST_METHOD'] == "GET")
		{
			$this->request_method = "get";
		}

		// If we've got register globals on, then kill them too
		if(@ini_get("register_globals") == 1)
		{
			$this->unset_globals($_POST);
			$this->unset_globals($_GET);
			$this->unset_globals($_FILES);
			$this->unset_globals($_COOKIE);
		}
		$this->clean_input();
	}

	/**
	 * Parses the incoming variables.
	 *
	 * @param array The array of incoming variables.
	 */
	function parse_incoming($array)
	{
		if(!is_array($array))
		{
			return;
		}

		foreach($array as $key => $val)
		{
			$this->input[$key] = $val;
		}
	}

	/**
	 * Parses the incoming cookies
	 *
	 */
	function parse_cookies()
	{
		if(!is_array($_COOKIE))
		{
			return;
		}

		$prefix_length = strlen($this->settings['cookieprefix']);

		foreach($_COOKIE as $key => $val)
		{
			if($prefix_length && substr($key, 0, $prefix_length) == $this->settings['cookieprefix'])
			{
				$key = substr($key, $prefix_length);

				// Fixes conflicts with one sS having a prefix and another that doesn't on the same domain
				// Gives priority to our cookies over others (overwrites them)
				if(isset($this->cookies[$key]))
				{
					unset($this->cookies[$key]);
				}
			}

			if(empty($this->cookies[$key]))
			{
				$this->cookies[$key] = $val;
			}
		}
	}

	/**
	 * Strips slashes out of a given array.
	 *
	 * @param array The array to strip.
	 */
	function strip_slashes_array(&$array)
	{
		foreach($array as $key => $val)
		{
			if(is_array($array[$key]))
			{
				$this->strip_slashes_array($array[$key]);
			}
			else
			{
				$array[$key] = stripslashes($array[$key]);
			}
		}
	}

	/**
	 * Unsets globals from a specific array.
	 *
	 * @param array The array to unset from.
	 */
	function unset_globals($array)
	{
		if(!is_array($array))
		{
			return;
		}

		foreach(array_keys($array) as $key)
		{
			unset($GLOBALS[$key]);
			unset($GLOBALS[$key]); // Double unset to circumvent the zend_hash_del_key_or_index hole in PHP <4.4.3 and <5.1.4
		}
	}

	/**
	 * Cleans predefined input variables.
	 *
	 */
	function clean_input()
	{
		foreach($this->clean_variables as $type => $variables)
		{
			foreach($variables as $var)
			{
				// If this variable is in the ignored array, skip and move to next.
				if(in_array($var, $this->ignore_clean_variables))
				{
					continue;
				}

				if(isset($this->input[$var]))
				{
					switch($type)
					{
						case "int":
							$this->input[$var] = $this->get_input($var, SS::INPUT_INT);
							break;
						case "a-z":
							$this->input[$var] = preg_replace("#[^a-z\.\-_]#i", "", $this->get_input($var));
							break;
						case "pos":
							if(($this->input[$var] < 0 && $var != "page") || ($var == "page" && $this->input[$var] != "last" && $this->input[$var] < 0))
								$this->input[$var] = 0;
							break;
					}
				}
			}
		}
	}

	/**
	 * Checks the input data type before usage.
	 *
	 * @param string $name Variable name ($ss->input)
	 * @param int $type The type of the variable to get. Should be one of SS::INPUT_INT, SS::INPUT_ARRAY or SS::INPUT_STRING.
	 *
	 * @return mixed Checked data
	 */
	function get_input($name, $type = SS::INPUT_STRING)
	{
		switch($type)
		{
			case SS::INPUT_ARRAY:
				if(!isset($this->input[$name]) || !is_array($this->input[$name]))
				{
					return array();
				}
				return $this->input[$name];
			case SS::INPUT_INT:
				if(!isset($this->input[$name]) || !is_numeric($this->input[$name]))
				{
					return 0;
				}
				return (int)$this->input[$name];
			case SS::INPUT_FLOAT:
				if(!isset($this->input[$name]) || !is_numeric($this->input[$name]))
				{
					return 0.0;
				}
				return (float)$this->input[$name];
			case SS::INPUT_BOOL:
				if(!isset($this->input[$name]) || !is_scalar($this->input[$name]))
				{
					return false;
				}
				return (bool)$this->input[$name];
			default:
				if(!isset($this->input[$name]) || !is_scalar($this->input[$name]))
				{
					return '';
				}
				return $this->input[$name];
		}
	}

	/**
	 * Triggers a generic error.
	 *
	 * @param string The error code.
	 */
	function trigger_generic_error($code)
	{
		switch($code)
		{
			case "install_directory":
				$message = "The install directory (install/) still exists on your server and is not locked. To access SS please either remove this directory or create an empty file in it called 'lock'.";
				break;
			case "ss_not_installed":
				$message = "Your SS has not yet been installed and configured. Please do so before attempting to browse it.";
				break;
			case "sql_load_error":
				$message = "SS was unable to load the SQL extension. Please contact the SS Group for support.";
				break;
			default:
				$message = "SS has experienced an internal error. Please contact the SS Group for support.";
		}
trigger_error("<strong>[SS] ".$message."</strong>", E_USER_ERROR);
	}
}