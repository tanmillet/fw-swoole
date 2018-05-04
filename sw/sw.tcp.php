<?php

class Sw_Http {

    const HOST = '0.0.0.0';
    const PORT = 9981;

    public $http = null;

    public function __construct()
    {
        $this->http = new swoole_http_server(self::HOST, self::PORT);

        $this->http->set(
            [
                'enable_static_handler' => true,
                'document_root' => "/home/work/hdtocs/swoole_mooc/thinkphp/public/static",
                'worker_num' => 4,
                'task_worker_num' => 4,
            ]
        );

    }
}


new Sw_Http();