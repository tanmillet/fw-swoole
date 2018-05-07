<?php

/**
 * Class Ctrs_Home
 */
class Ctrs_Home extends Sys_Ctr {

    public function index()
    {
        echo Libs_Tools::show(200, 'success');
    }
}