<?php

class PsApi extends Sys_Ctr {

    /**
     * @var
     */
    protected $status_code;

    /**
     * @var array
     */
    protected static $error_msg = [];


    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @param $status_code
     * @return $this
     */
    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;
        return $this;
    }


    /**
     * @param string $message
     * @return string
     */
    public function responseNotFound($msg_code = '10001')
    {

        return $this->setStatusCode(404)->responseError($msg_code);
    }


    /**
     * @param $err_msg
     * @return string
     */
    public function responseError($msg_code)
    {
        if (!empty(static::$error_msg) || !isset(static::$error_msg[$msg_code])) {
            $messages = Libs_Conf::get('err_code', 'ps');
            static::$error_msg = $messages;
        }

        $err_msg = isset(static::$error_msg[$msg_code]) ? static::$error_msg[$msg_code] : static::$error_msg['99999'];
        return $this->response([
            'status' => 'failed',
            'info' => [
                'status_code' => $this->getStatusCode(),
                'message' => $err_msg,
                'data' => '',
            ],
        ]);
    }


    /**
     * @param $output_data
     * @return string
     */
    public function responseSuccess($output_data = [])
    {
        return $this->response([
            'status' => 'succeed',
            'info' => [
                'status_code' => $this->getStatusCode(),
                'message' => 'succeed',
                'data' => $output_data,
            ],
        ]);
    }


    /**
     * @param $output_data
     * @return string
     */
    public function response($output_data)
    {
        //
        header("Content-type: application/json; charset=utf-8");
        return json_encode($output_data);

    }

}