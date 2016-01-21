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
            function __autoLoad()
            {

            }
        }
    }


    public static function __autoLoad($className)
    {
        @include_once str_replace(array("\\", "_"), "/", $className) . ".php";
    }
}