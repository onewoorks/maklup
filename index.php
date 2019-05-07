<?php

//session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");
$params = explode('/', $_SERVER['REQUEST_URI']);

require_once './application/libraries/addon/jwt.php';
require_once './application/libraries/addon/billplz.php';

function ob_html_compress($buf) {
    return preg_replace(array('/<!--(.*)-->/Uis', "/[[:blank:]]+/"), array('', ' '), str_replace(array("\n", "\r", "\t"), '', $buf));
}

//ob_start("ob_html_compress");
define('APPS_NAME', 'pulkam-api');
define('SERVER_ROOT', __DIR__);
define('VIEW', '/application/views');
define('CONTROLLER', '/application/controllers');
define('LIBRARY_ADDON', SERVER_ROOT . '/application/libraries/addon/');
define('MODEL', '/application/models');
define('INCLUDES', '/application/includes');
define('SITE', 'application/');
define('SCRIPTS', 'application/views/scripts/');
define('SITE_ROOT', 'http://localhost/pulkam-api/');
define('URL_ARRAY', '2');
define('TOKEN_SIGNATURE','pulkam2019byonewoorkssolutions');
//define('')

require_once(SERVER_ROOT . '/application/engine/router.php');
// ob_end_flush();