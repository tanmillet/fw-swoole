<?php

class Ctrs_Login {
    public function index()
    {
        // phone code
        $phoneNum = intval($_GET['phone_num']);
        $code = intval($_GET['code']);
        if (empty($phoneNum) || empty($code)) {
            return Util::show(config('code.error'), 'phone or code is error');
        }

        // redis code
        try {
            $redisCode = Predis::getInstance()->get(Redis::smsKey($phoneNum));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        if ($redisCode == $code) {
            // 写入redis
            $data = [
                'user' => $phoneNum,
                'srcKey' => md5(Redis::userkey($phoneNum)),
                'time' => time(),
                'isLogin' => true,
            ];
            Predis::getInstance()->set(Redis::userkey($phoneNum), $data);

            return Util::show(config('code.success'), 'ok', $data);
        } else {
            return Util::show(config('code.error'), 'login error');
        }
        // redis.so
    }
}
