<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-type: text/html; charset=utf-8");
define('ROOT_PATH', dirname(__FILE__));
define("KEY_PREFIX", 'dmadmin:');
define("PRIVDATA_DIR", '/data/private');
define("TEMP_DIR", PRIVDATA_DIR . '/tmp');
define("CSV_DIR", '/data/www/facebook_export');
define('IMAGE_DOMAIN', 'http://ps.stosz.com');
define('DS', DIRECTORY_SEPARATOR);
define('SYS_PATH', 'sys' . DS);
require_once(SYS_PATH . 'sys.init.php');
Sys_Init::init();

include(ROOT_PATH . '/helper.php');
//获取上下文执行环境
define('ENV', Libs_Conf::get('ENV', 'app'));
define('ENV_FILE', Libs_Conf::get('ENV', 'app'));


