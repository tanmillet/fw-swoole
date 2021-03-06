<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/10/18
 * Time: 9:06 PM
 */

class Sers_Live {

    CONST HOST = "0.0.0.0";
    CONST PORT = 9988;
    CONST CHART_PORT = 9989;

    public $ws = null;

    public function __construct()
    {
        // 获取 key 有值 del
        $this->ws = new swoole_websocket_server(self::HOST, self::PORT);
        //开启第二个端口进行监听
        $this->ws->listen(self::HOST, self::CHART_PORT, SWOOLE_SOCK_TCP);

        $this->ws->set(
            [
                'enable_static_handler' => true,
                'document_root' => "/apps/ws/fw-swoole/public/static",
//                'document_root' => "/ws/soft/app/fw-swoole/public/static",
                'worker_num' => 4,
                'task_worker_num' => 4,
            ]
        );

        $this->ws->on("start", [$this, 'onStart']);
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on("workerstart", [$this, 'onWorkerStart']);
        $this->ws->on("request", [$this, 'onRequest']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this, 'onClose']);

        $this->ws->start();
    }

    /**
     * @param $server
     * @process 进程别名
     * 平滑重启只对onWorkerStart或onReceive等在Worker进程中include/require的PHP文件有效，
     * Server启动前就已经include/require的PHP文件，不能通过平滑重启重新加载
     * 对于Server的配置即$serv->set()中传入的参数设置，必须关闭/重启整个Server才可以重新加载
     * Server可以监听一个内网端口，然后可以接收远程的控制命令，去重启所有worker
     */
    public function onStart($server)
    {
        swoole_set_process_name("live_master");
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        require '../index.php';
    }

    /**
     * request回调
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
        $this->writeLog();
        $_POST['http_server'] = $this->ws;

        ob_start();
        $action = isset($_GET['m']) ? $_GET['m'] : 'index';
        $controller = isset($_GET['c']) ? $_GET['c'] : 'home';
        $controllers = Libs_Conf::get('route_map', 'ps');
        $controller = 'Ctrs_' . (isset($controllers[$controller]) ? $controllers[$controller] : 'Home');
        (new $controller)->$action();
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
        $obj = new Task_Swoole();
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
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request)
    {
        $fd_info = $ws->connection_info($request->fd);
        $port = isset($fd_info['server_port']) ? $fd_info['server_port'] : 0;
        switch ($port) {
            case self::PORT:
                Libs_Predis::getInstance()->sAdd(Libs_Conf::get('live_game_key', 'redis'), $request->fd);
                break;
            case self::CHART_PORT:
                Libs_Predis::getInstance()->sAdd(Libs_Conf::get('chart_game_key', 'redis'), $request->fd);
                break;
            default:
                break;
        }
    }

    /**
     * 监听ws消息事件
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
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        Libs_Predis::getInstance()->sRem(Libs_Conf::get('live_game_key', 'redis'), $fd);
        Libs_Predis::getInstance()->sRem(Libs_Conf::get('chart_game_key', 'redis'), $fd);
        echo "clientid : {$fd}   be closed\n";
    }

    /**
     * 记录日志
     */
    public function writeLog()
    {
        $datas = array_merge(['date' => date("Ymd H:i:s")], $_GET, $_POST, $_SERVER);

        $logs = "";
        foreach ($datas as $key => $value) {
            $logs .= $key . ":" . $value . " ";
        }
        swoole_async_writefile('../runtime/log/' . date("d") . "_access.log", $logs . PHP_EOL, function ($filename) {

        }, FILE_APPEND);

    }
}

new Sers_Live();