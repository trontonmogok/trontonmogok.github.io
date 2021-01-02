<?php

declare(strict_types=1);


global $isdev, $iscli;

function dk_error_handler(){
	echo "<pre>"; 
	$err= debug_backtrace();
	$arr=	array_shift($err);
	print_r($arr);
	die("</pre> \n");
}

global $dbdks, $isdev, $dbc_classnames;

if ($iscli) set_time_limit(0);
@error_reporting(E_ALL & ~(E_DEPRECATED | E_NOTICE | E_STRICT | E_WARNING));
@error_reporting(E_ALL);
@ini_set("display_errors", "1");
@ini_set("display_startup_errors", "1");

@define('_CACHE_FORCED', true);
@define('ROOT_PATH', __DIR__);

require_once(ROOT_PATH . "/libsw/main-libs.php");
require_once(ROOT_PATH . "/func-github.php");
