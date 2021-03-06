<?php

class Cron_Monitor {

    public function __construct($port = 9988)
    {
        $shell_cmd = "netstat -anp | grep " . $port . " | grep LISTEN | wc -l";
        $res = shell_exec($shell_cmd);
        if ($res != 1) {
            $mgs = 'start port is running :  Port [ ' . $port . ' ]  is failure !';
        } else {
            $mgs = 'start port is running :  Port [ ' . $port . ' ]  is success !';
        }
        echo $mgs . PHP_EOL;
    }

}

$timer = swoole_timer_tick(2000, function ($timer_id) {
    (new Cron_Monitor(9988));
    (new Cron_Monitor(9989));
});

//swoole_timer_clear($timer);