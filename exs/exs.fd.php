<?php

/**
 * Class Exs_Args
 */
class Exs_Fd extends Exs_Base {

    /**
     * Exs_Args constructor.
     * @param string $messge
     * @param int $code
     */
    public function __construct($messge = '', $code = 0)
    {
        parent::__construct($this->transformExceptionMsg($messge), $code);
    }

    /**
     * @param string $messge
     * @return string
     */
    public function transformExceptionMsg($messge = '')
    {
        if (!isset($messge)) return $messge;
        //todo

        return $messge;
    }
}