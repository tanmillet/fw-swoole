<?php

/**
 * Class Sw_Http
 */
class Sw_Http {

    /**
     *
     */
    const HOST = '0.0.0.0';
    /**
     *
     */
    const PORT = 9981;

    /**
     * @var null|swoole_http_server
     */
    public $http = null;

    /**
     * Sw_Http constructor.
     */
    public function __construct()
    {
        $this->http = new swoole_http_server(self::HOST, self::PORT);

        $this->http->set(
            [
                'enable_static_handler' => true,
                'document_root' => "/ws/soft/app/fw-swoole/public/static",
                'worker_num' => 4,
                'task_worker_num' => 4,
            ]
        );

        $this->http->on("workerstart", [$this, 'onWorkerStart']);
        $this->http->on("request", [$this, 'onRequest']);
        $this->http->on("task", [$this, 'onTask']);
        $this->http->on("finish", [$this, 'onFinish']);
        $this->http->on("close", [$this, 'onClose']);

        $this->http->start();
    }

    /**
     * 此事件在Worker进程/Task进程启动时发生。
     * 可以通过$server->taskworker属性来判断当前是Worker进程还是Task进程
     *
     * @param swoole_server $server
     * @param $worker_id
     */
    public function onWorkerStart(swoole_server $server, $worker_id)
    {
        require __DIR__ . '/../index.php';
    }
    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response) {
        $_SERVER  =  []; //常驻内存变量$_SERVER 清空
        if(isset($request->server)) {
            foreach($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if(isset($request->header)) {
            foreach($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        $_GET = []; //常驻内存变量$_GET 清空
        if(isset($request->get)) {
            foreach($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }

        $_POST = [];//常驻内存变量$_POST清空
        if(isset($request->post)) {
            foreach($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

        //保存swoole_server 对象
        $_POST['http_server'] = $this->http;

        ob_start();
        // 执行应用并响应
        //开发环境开启异常
        (Libs_Conf::get('DEBUG', ENV_FILE)) ? ini_set('display_error', 'On') : ini_set('display_error', 'Off');
        if (!get_magic_quotes_gpc()) {
            $_GET = addslashes_deep($_GET);
            $_POST = addslashes_deep($_POST);
            $_COOKIE = addslashes_deep($_COOKIE);
        }
        set_exception_handler('bgnException');
        date_default_timezone_set('Asia/Shanghai');
        ini_set('default_charset', "utf-8");

        if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            define('IS_AJAX', true);
        } else {
            define('IS_AJAX', false);
        }
        if (isset($_SERVER['REDIRECT_URL'])) {
            $uri_info = $_SERVER['REDIRECT_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $uri_info = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['PATH_INFO'])) {
            $uri_info = $_SERVER['PATH_INFO'];
        }
        if (strpos($uri_info, '?')) {
            $uri_info = Libs_Tools::leftString('?', $uri_info);
        }
        $GLOBALS['request_uri_info'] = $uri_info;
        $uri_segment = [];

        if ($uri_info) {
            $uri_info = rtrim($uri_info, "/") . "/";    // 无论是否/结尾,统一按照/结尾
            $aPathInfo = explode('/', trim($uri_info, "/"));    // 获取 pathinfo
            $controller = (isset($aPathInfo[0]) ? $aPathInfo[0] : 'home');    // 获取 control
            array_shift($aPathInfo);
            $action = (isset($aPathInfo[0]) ? $aPathInfo[0] : 'index');   // 获取 action
            array_shift($aPathInfo);
            while ($aPathInfo && is_array($aPathInfo)) {
                $uri_segment[$aPathInfo[0]] = $aPathInfo[1];
                array_shift($aPathInfo);
                array_shift($aPathInfo);
            }
        }

        $controllers = Libs_Conf::get('route_map', 'ps');
        $controller = isset($controllers[$controller]) ? $controllers[$controller] : 'Home';
        $action = $action ? $action : 'index';
        $controller_file = ROOT_PATH . '/controllers/' . $controller . '.php';
        $is_ctr_files = false;
        foreach (glob(ROOT_PATH . '/controllers/' . "*.php") as $filename) {
            if (basename($filename, '.php') == $controller) {
                $is_ctr_files = true;
                break;
            }
        }

        require_once ROOT_PATH . '/controllers/PsApi.php';
        $ps_api = new PsApi();
        if (!file_exists($controller_file) || !$is_ctr_files) {
            echo $ps_api->responseError(10001);
            die();
        }

        require($controller_file);
        if (!class_exists($controller)) {
            echo $ps_api->responseError(10001);
            die();
        }

        session_start();
        $user_info = isset($_SESSION['user']) ? $_SESSION['user'] : null;
        if (isNeedCheckSession($controller, $action) && empty($user_info)) { // 是否需要身份校验
            echo $ps_api->responseError(10002);
            die();
        }

        $class_name = $controller;
        $data['uri_segment'] = $uri_segment;
        $data['current_user_info'] = $user_info;
        $GLOBALS['uid'] = isset($user_info['uid']) ? $user_info['uid'] : 0;
        $o = new $class_name($data);
        if (!method_exists($o, $action)) {
            echo $ps_api->responseError(10001);
            die();
        }

        if (isNeedCheckSession($controller, $action)) { // 判断用户是否有访问的权限
            $auth_model = new Models_Auth();
            $user_auth = $auth_model->isUserCanAccess($user_info['uid'], $controller, $action);
            if (!$user_auth) {
                echo $ps_api->responseError(10037);
                die();
            }
        }

        $o->$action();

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }


    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($serv, $taskId, $workerId, $data)
    {
        // 分发 task 任务机制，让不同的任务 走不同的逻辑
        $obj = new Sw_Task_Com();

        $method = $data['method'];
        $flag = $obj->$method($data['data']);

        return $flag; // 告诉worker
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data)
    {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        echo "clientid:{$fd}\n";
    }
}