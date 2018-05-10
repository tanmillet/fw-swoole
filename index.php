<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-type: text/html; charset=utf-8");
define('ROOT_PATH', dirname(__FILE__));
define("KEY_PREFIX", 'dmadmin:');
define('DS', DIRECTORY_SEPARATOR);
define('SYS_PATH', 'sys' . DS);

require_once(SYS_PATH . 'sys.init.php');
Sys_Init::init();

require_once(ROOT_PATH . '/helper.php');
//获取上下文执行环境
define('ENV', Libs_Conf::get('ENV', 'app'));
define('ENV_FILE', Libs_Conf::get('ENV', 'app'));

//开发环境开启异常
(Libs_Conf::get('DEBUG', ENV_FILE)) ? ini_set('display_error', 'On') : ini_set('display_error', 'Off');
//if (!get_magic_quotes_gpc()) {
//    $_GET = addslashes_deep($_GET);
//    $_POST = addslashes_deep($_POST);
//    $_COOKIE = addslashes_deep($_COOKIE);
//}
//set_exception_handler('bgnException');
//
//date_default_timezone_set('Asia/Shanghai');
//ini_set('default_charset', "utf-8");
//
//if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
//    define('IS_AJAX', true);
//} else {
//    define('IS_AJAX', false);
//}
//
//$action = isset($_GET['m']) ? $_GET['m'] : 'index';
//$controller = isset($_GET['c']) ? $_GET['c'] : 'home';
//$controllers = Libs_Conf::get('route_map', 'ps');
//$controller = 'Ctrs_' . (isset($controllers[$controller]) ? $controllers[$controller] : 'Home');
//
//(new $controller)->$action();