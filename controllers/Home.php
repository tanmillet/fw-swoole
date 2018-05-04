<?php

class Home extends PsApi {
    public function index()
    {
        echo $this->setStatusCode(200)->responseSuccess(['version' => '0.1']);
    }
}