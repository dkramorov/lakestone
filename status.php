<?php

header("HTTP/1.0 500 Internal Server Error");

// Version
define('VERSION', '2.3.0.2');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('status');
?>
