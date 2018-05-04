<?php
define('ROOT_PATH', dirname(dirname(__FILE__)));
define("KEY_PREFIX", 'dmadmin:');
define("PRIVDATA_DIR", '/data/private');
define("TEMP_DIR", PRIVDATA_DIR . '/tmp');
define("CSV_DIR", '/data/www/facebook_export');
define('IMAGE_DOMAIN', 'http://ps.stosz.com');
include(ROOT_PATH . '/helper.php');

define('ENV', Libs_Conf::get('ENV', 'app'));
set_exception_handler('bgnException'); 