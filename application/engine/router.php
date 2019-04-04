<?php
include_once 'application/engine/view.php';

spl_autoload_register(function($className) {
    list($suffix, $filename) = preg_split('/_/', strrev($className), 2);
    $filename = strrev($filename);
    $suffix = strrev($suffix);
    $outsidePath = false;
    switch (strtolower($suffix)) {
        case 'model':
            $folder = 'application/models/';
            $outsitePath = true;
            break;
        case 'library':
            $folder = 'application/libraries/';
            break;
        case 'driver':
            $folder = 'application/libraries/drivers/';
            $outsitePath = true;
            break;
        case 'utils':
            $folder = 'application/utils/';
            break;
        case 'functions':
            $folder = 'application/libraries/functions/';
            break;
        case 'controller':
            $folder = 'application/controllers/';
            break;
    }
    if ($outsidePath):
        $file = SERVER_ROOT . $folder . strtolower($filename) . '.php';
    else:
        $file = $folder . strtolower($filename) . '.php';
    endif;

    if (file_exists($file)) {
        include_once $file ;
    } else {
        die("File '$filename' containing class '$className' not found in '$folder'.");
    }
});

function classNamer($filter) {
    $names = explode('-', strtolower($filter));
    $clean = array();
    foreach ($names as $name):
        $clean[] = ucfirst($name);
    endforeach;
    return implode('', $clean);
}

$params = explode('/', $_SERVER['REQUEST_URI']);
$request = $_SERVER['QUERY_STRING'];
$parsed = explode('&', $request);
$page = array_shift($parsed);
$logid = isset($_SESSION['uid']);

$getVars = array();

foreach ($parsed as $argument) {
    list($variable, $value) = explode('=', $argument);
    $getVars[$variable] = urldecode($value);
}

$page = $params[URL_ARRAY];
$page = classNamer($page);
$page = ($page == '' ) ? 'index' : $page;
$target = SERVER_ROOT . CONTROLLER . '/'. strtolower($page) . '.php';

if (file_exists($target)) {
    include_once($target);
    $class = ucfirst($page) . '_Controller';
    class_exists($class) ? $controller = new $class : die('class does not exist!');
    $output = $controller->main($getVars, $params, $request);
    $response = array(
        'response' => $output
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    die();
} else {
    $response = array(
        'title' => 'ONEWOORKS API FRAMEWORK',
        'message' => 'Route does not exist'
    );
    echo json_encode($response);
    die();
}
