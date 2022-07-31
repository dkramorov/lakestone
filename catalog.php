<?php

// Version
define('VERSION', '2.3.0.2');

// set DOCUMENT_ROOT
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOC_ROOT'];
$_SERVER['SERVER_PORT'] = 443;

// Configuration
@require_once('config.php');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

parse_str(parse_url($_SERVER['REQUEST_URI'])['query'], $_REQUEST);
parse_str(parse_url($_SERVER['REQUEST_URI'])['query'], $_GET);

start('catalog');

?>
