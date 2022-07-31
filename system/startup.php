<?php
// Error Reporting
//error_reporting(E_ALL);
error_reporting(E_ALL ^ E_DEPRECATED);

// workaround glob(..., GLOB_BRACE)
function glob_brace($path, $opt = NULL) {
    $ret = array();
    if ( preg_match('/{([^}]+)}/', $path, $res) == FALSE )
        return glob($path, $opt);
    foreach ( explode(',', $res[1]) as $name ) {
        $ret = array_merge($ret, glob_brace(str_replace($res[0], $name, $path), $opt));
    }
    if ( sizeof($ret) > 1000 ) die('over recursion');
    return $ret;
};

// debug functions
function dd() {
  call_user_func_array('d', func_get_args());
  exit;
}
function d() {
  echo "\nDebug output ";
  foreach(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3) as $trace) {
    if (in_array($trace['function'], ['d', 'dd'])) {
      echo 'at ' . $trace['file'] . ':' . $trace['line'] . "\n";
    }
  }
  foreach (func_get_args() as $arg) {
    var_dump($arg);
  }
}

// the small tool for the fucking profiling
function now($label = '') {
  if (!defined('DEV_PROFILER') or !DEV_PROFILER) return;
	static $start;
	if ($start === NULL)
		$start = microtime(true);
	if (!empty($label))
		$label = ' (' . $label . ')';
	printf("Now%s is: %0.5f\n<br>\n", $label, microtime(true) - $start);
}

// Check Version
if (version_compare(phpversion(), '5.4.0', '<') == true) {
	exit('PHP5.4+ Required');
}

// Check the master node for cron-jobs
if (
    (!defined('DEV_MODE') or !DEV_MODE)
    and php_sapi_name() == 'cli'
) {
  $project_status = json_decode(file_get_contents('https://www.lakestone.ru/status.php'));
  if (
      !$project_status
      or $project_status->hostname != gethostname()
  ) {
    echo "This node is not master now. Cron-jobs will not doing.\n";
    exit;
  }
}

// Magic Quotes Fix
if (ini_get('magic_quotes_gpc')) {
	function clean($data) {
   		if (is_array($data)) {
  			foreach ($data as $key => $value) {
    			$data[clean($key)] = clean($value);
  			}
		} else {
  			$data = stripslashes($data);
		}

		return $data;
	}

	$_GET = clean($_GET);
	$_POST = clean($_POST);
	$_COOKIE = clean($_COOKIE);
}

if (!ini_get('date.timezone')) {
	date_default_timezone_set('UTC');
}

// Windows IIS Compatibility
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

// Check if SSL
if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || $_SERVER['SERVER_PORT'] == 443) {
	$_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
} else {
	$_SERVER['HTTPS'] = false;
}

// Modification Override
function modification($filename) {
	if (defined('DIR_CATALOG')) {
		$file = DIR_MODIFICATION . 'admin/' .  substr($filename, strlen(DIR_APPLICATION));
	} elseif (defined('DIR_OPENCART')) {
		$file = DIR_MODIFICATION . 'install/' .  substr($filename, strlen(DIR_APPLICATION));
	} else {
		$file = DIR_MODIFICATION . 'catalog/' . substr($filename, strlen(DIR_APPLICATION));
	}

	if (substr($filename, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
		$file = DIR_MODIFICATION . 'system/' . substr($filename, strlen(DIR_SYSTEM));
	}

	if (is_file($file)) {
		return $file;
	}

	return $filename;
}

// Autoloader
if (is_file(DIR_SYSTEM . '../vendor/autoload.php')) {
	require_once(DIR_SYSTEM . '../vendor/autoload.php');
}

function library($class) {
	$file = DIR_SYSTEM . 'library/' . str_replace('\\', '/', strtolower($class)) . '.php';

	if (is_file($file)) {
		include_once(modification($file));

		return true;
	} else {
		return false;
	}
}

spl_autoload_register('library');
spl_autoload_extensions('.php');

// Engine
require_once(modification(DIR_SYSTEM . 'engine/action.php'));
require_once(modification(DIR_SYSTEM . 'engine/controller.php'));
require_once(modification(DIR_SYSTEM . 'engine/event.php'));
require_once(modification(DIR_SYSTEM . 'engine/front.php'));
require_once(modification(DIR_SYSTEM . 'engine/loader.php'));
require_once(modification(DIR_SYSTEM . 'engine/model.php'));
require_once(modification(DIR_SYSTEM . 'engine/registry.php'));
require_once(modification(DIR_SYSTEM . 'engine/proxy.php'));

// Helper
require_once(DIR_SYSTEM . 'helper/general.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');
require_once(DIR_SYSTEM . 'helper/json.php');

function start($application_config) {
	require_once(DIR_SYSTEM . 'framework.php');	
}