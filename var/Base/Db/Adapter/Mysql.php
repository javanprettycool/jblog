<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Mysql_Adapter extends Adapter{






    /**
     * �ж��������Ƿ����
     *
     * @access public
     * @return boolean
     */
    public static function isAvailable()
    {
        return function_exists('mysql_connect');
    }
}