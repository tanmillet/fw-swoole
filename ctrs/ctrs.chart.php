<?php

class Ctrs_Chart {
    public function index()
    {
        // 登录
        if (empty($_POST['game_id'])) {
            return Libs_Tools::show(Libs_Conf::get('error', 'code'), 'error');
        }
        if (empty($_POST['content'])) {
            return Libs_Tools::show(Libs_Conf::get('error', 'code'), 'error');
        }

        $data = [
            'user' => "用户" . rand(0, 2000),
            'content' => $_POST['content'],
        ];

        $clients = Libs_Predis::getInstance()->sMembers(Libs_Conf::get("chart_game_key","redis"));
        foreach ($clients as $fd) {
            $_POST['http_server']->push($fd, json_encode($data));
        }
        //  todo
//        foreach ($_POST['http_server']->ports[1]->connections as $fd) {
//            $_POST['http_server']->push($fd, json_encode($data));
//        }

        return Libs_Tools::show(Libs_Conf::get('success', 'code'), 'ok', $data);
    }


}
