<?php

/**
 * Class Sys_Init
 */
final class Sys_Init {
    private static $_root_paths = ['config', 'public', 'sys', 'models', 'sdk', 'libs', 'sw', 'task','ctrs'];
    private static $_files = [];
    private static $_ext = '.php';
    private static $_init = false;

    /**
     * 项目的初始化
     */
    public static function init()
    {
        if (self::$_init) {
            return;
        }
        self::$_init = true;
        spl_autoload_register([__CLASS__, 'auto_load']);
    }

    /**
     * @author Chris
     *
     * 自定义__autoload
     *
     * @param $class
     * @return bool
     */
    public static function auto_load($class)
    {
        $class = strtolower($class);
        $_num = substr_count($class, '_');
        if (empty($_num)) {
            return false;
        }
        $file = str_replace('_', '.', $class); //文件名，不带.php后缀
        $class_arr = explode("_", $class);
        $first_dir = $class_arr[0];
        array_pop($class_arr); //去尾
        if (in_array($first_dir, self::$_root_paths)) {
            $dirpath = "";
        } else {
            $dirpath = APP_PATH;
        }
        $file_path = implode($class_arr, DS) . DS; //文件路径
        if ($require_file = self::find_file($dirpath, $file_path, $file)) {
            require_once($require_file);
            return true;
        }
        return false;
    }

    /**
     *
     * @author terris
     *
     * 查询文件位置
     *
     * @param $dirpath
     * @param $file_path
     * @param $file
     * @param null $ext
     * @return bool|mixed|string
     */
    public static function find_file($dirpath, $file_path, $file, $ext = NULL)
    {
        $ext = $ext ? ".{$ext}" : self::$_ext;
        $found = false;
        $filePath = ROOT_PATH . DS . $dirpath . $file_path . $file . $ext; //define路径，文件路径，文件名，后缀
        $key = md5($filePath);
        if (isset(self::$_files[$key])) { //已加载过，则无需再次加载
            $found = self::$_files[$key];
        }
        if (is_file($filePath)) { //是有效的php文件
            self::$_files[$key] = $filePath;
            $found = $filePath;
        }
        return $found;
    }
}