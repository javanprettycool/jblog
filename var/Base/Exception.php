<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */



class Handle_Exception extends Exception{
    public function __construct($message = "", $code = 0)
    {
        $this->message = $message;
        $this->code = $code;
    }
}