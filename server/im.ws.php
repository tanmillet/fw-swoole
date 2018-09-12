<?php

class ImWS {

    CONST HOST = "0.0.0.0";
    CONST PORT = 9960;

    public $ws = null;
    public $application;

    static public $instance;

    public function __construct()
    {
        // 获取 key 有值 del
        $this->ws = new swoole_websocket_server(self::HOST, self::PORT);
        $this->ws->set([
            'enable_static_handler' => true,
            'document_root' => "/data/www/ps-fb-msoa/front",
            'worker_num' => 4,
            'task_worker_num' => 4,
            //                'daemonize' => true,
        ]);

        $this->ws->on("start", [
            $this,
            'onStart'
        ]);
        $this->ws->on("open", [
            $this,
            'onOpen'
        ]);
        $this->ws->on("message", [
            $this,
            'onMessage'
        ]);
        $this->ws->on("workerstart", [
            $this,
            'onWorkerStart'
        ]);
        $this->ws->on("request", [
            $this,
            'onRequest'
        ]);
        $this->ws->on("task", [
            $this,
            'onTask'
        ]);
        $this->ws->on("finish", [
            $this,
            'onFinish'
        ]);
        $this->ws->on("close", [
            $this,
            'onClose'
        ]);

        $this->ws->start();
    }

    /**
     * @param $server
     *
     * @process 进程别名
     * 平滑重启只对onWorkerStart或onReceive等在Worker进程中include/require的PHP文件有效，
     * Server启动前就已经include/require的PHP文件，不能通过平滑重启重新加载
     * 对于Server的配置即$serv->set()中传入的参数设置，必须关闭/重启整个Server才可以重新加载
     * Server可以监听一个内网端口，然后可以接收远程的控制命令，去重启所有worker
     */
    public function onStart($server)
    {
        swoole_set_process_name("im_ws_master");
    }

    /**
     * @param $server
     * @param $worker_id
     *
     * @throws Yaf_Exception_StartupError
     * @throws Yaf_Exception_TypeError
     */
    public function onWorkerStart($server, $worker_id)
    {
        define('APPLICATION_PATH', dirname(__DIR__));
        $this->application = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
        ob_start();
        $this->application->bootstrap()->run();
        ob_end_clean();
    }

    /**
     * request回调
     *
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        if ($request->server['request_uri'] == '/favicon.ico') {
            $response->status(404);
            $response->end();
            return;
        }
        //常驻内存变量$_SERVER 清空
        $_SERVER = [];
        if (isset($request->server)) {
            foreach ($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if (isset($request->header)) {
            foreach ($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        //常驻内存变量$_GET 清空
        $_GET = [];
        if (isset($request->get)) {
            foreach ($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        //常驻内存变量$_POST清空
        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }
        //保存swoole_server 对象
        $_POST['http_server'] = $this->ws;

        ob_start();
        $yaf_request = new Yaf_Request_Http($request->server['request_uri']);
        $this->application->getDispatcher()->dispatch($yaf_request);
        $result = ob_get_contents();
        ob_end_clean();

        $response->end($result);
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     *
     * @return mixed
     */
    public function onTask($serv, $taskId, $workerId, $data)
    {
        // 分发 task 任务机制，让不同的任务 走不同的逻辑
        $obj = new Task_Sw();
        $method = $data['method'];
        $flag = $obj->$method($data['data'], $serv);

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
     * 监听ws连接事件
     *
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request)
    {
        Com_Predis::getInstance()->sAdd('chart_game_key', $request->fd);
    }

    /**
     * 监听ws消息事件
     *
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame)
    {
        echo "ser-push-message:{$frame->data}\n";
        $ws->push($frame->fd, "server-push:" . date("Y-m-d H:i:s"));
    }

    /**
     * close
     *
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        Com_Predis::getInstance()->sRem('chart_game_key', $fd);
        echo "clientid : {$fd}   be closed\n";
    }

    /**
     * 记录日志
     */
    public function writeLog()
    {
        //        $datas = array_merge(['date' => date("Ymd H:i:s")], $_GET, $_POST, $_SERVER);
        //
        //        $logs = "";
        //        foreach ($datas as $key => $value) {
        //            $logs .= $key . ":" . $value . " ";
        //        }
        //        swoole_async_writefile('../runtime/log/im/' . date("d") . "_access.log", $logs . PHP_EOL, function ($filename) {
        //
        //        }, FILE_APPEND);

    }

    /**
     * @return ImWS
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ImWS();
        }
        return self::$instance;
    }
}

ImWS::getInstance();