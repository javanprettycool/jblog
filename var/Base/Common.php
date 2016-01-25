<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */


class Common{
    /**
     * �汾
     */
    const VERSION = "0.1";

    /**
     * Ĭ�ϱ���
     *
     * @access public
     * @var string
     */
    public static $charset = 'UTF-8';

    /**
     * �쳣������
     *
     * @access public
     * @var string
     */
    public static $exceptionHandle;





    public static function init()
    {
        if (function_exists("spl_autoload_register")) {
            spl_autoload_register(array("Common", "__autoLoad"));
        } else {
            function __autoLoad($className)
            {
                Common::__autoLoad($className);
            }
        }


        @set_exception_handler(array('Common', 'exceptionHandle'));
    }


    public static function __autoLoad($className)
    {   //echo 'Base/' . str_replace(array("\\", "_"), "/", $className) . ".php";
        include_once 'Base/' . str_replace(array("\\", "_"), "/", $className) . ".php";
    }


    public static function exceptionHandle(Exception $exception)
    {
        echo $exception->getMessage();
    }

    public static function url($path, $prefix)
    {

    }
}