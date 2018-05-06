<?php

class Task_Swoole {
    /**
     * 异步发送 验证码
     * @param $data
     * @param $serv swoole server对象
     */
    public function sendSms($data, $serv)
    {
        try {
            $response = Libs_Sms::sendSms($data['phone'], $data['code']);
        } catch (\Exception $e) {
            // todo
            return false;
        }

        // 如果发送成功 把验证码记录到redis里面
        if ($response->Code === "OK") {
            Libs_Predis::getInstance()->set(Libs_Redis::smsKey($data['phone']), $data['code'], config('redis.out_time'));
        } else {
            return false;
        }
        return true;
    }

    /**
     * 通过task机制发送赛况实时数据给客户端
     * @param $data
     * @param $serv swoole server对象
     */
    public function pushLive($data, $serv)
    {
        $clients = Libs_Predis::getInstance()->sMembers(Libs_Conf::get("live_game_key","redis"));

        foreach ($clients as $fd) {
            $serv->push($fd, json_encode($data));
        }
    }

}