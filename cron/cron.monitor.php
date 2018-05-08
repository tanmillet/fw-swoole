<?php

class Cron_Monitor {

    public function __construct($port = 9988)
    {
        $shell_cmd = "netstat -anp | grep " . $port . " | grep LISTEN | wc -l";
        $res = shell_exec($shell_cmd);
        if ($res != 1) {
            $mgs = 'start port is running :  Port [ ' . $port . ' ]  is success !';
        } else {
            $mgs = 'start port is running :  Port [ ' . $port . ' ]  is failure !';
        }
        echo $mgs . PHP_EOL;
    }

}

swoole_timer_tick(2000, function () {
    (new Cron_Monitor(9988));
    (new Cron_Monitor(9989));
});