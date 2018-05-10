<?php

/**
 * Class Ctrs_Home
 */
class Ctrs_Im {

    public function index()
    {
        // 登录
        if (empty($_POST['content'])) {
            return Libs_Tools::show(Libs_Conf::get('error', 'code'), 'error');
        }

        $data = [
            'user' => "用户" . rand(0, 2000),
            'talk_time' => date("Y-m-d H:i:s"),
            'content' => $_POST['content'],
        ];

        try{
            $clients = Libs_Predis::getInstance()->sMembers(Libs_Conf::get("im_key","redis"));
            foreach ($clients as $fd) {
                $_POST['http_server']->push($fd, json_encode($data));
            }
        }catch (Exs_Fd $exs_Fd){
            $this->writeLog($exs_Fd->getMessage());
        }

        return Libs_Tools::show(Libs_Conf::get('success', 'code'), 'ok', $data);
    }


    /**
     * 记录日志
     */
    public function writeLog($msg = "")
    {
        $datas = array_merge(['date' => date("Ymd H:i:s")], $msg);

        $logs = "";
        foreach ($datas as $key => $value) {
            $logs .= $key . ":" . $value . " ";
        }
        swoole_async_writefile('../runtime/log/im/' . date("d") . "_access_im.log", $logs . PHP_EOL, function ($filename) {

        }, FILE_APPEND);

    }
}