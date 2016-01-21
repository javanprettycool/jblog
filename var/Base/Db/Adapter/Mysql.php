<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Mysql_Adapter extends Adapter{






    /**
     * еп╤оййеДфВйг╥Я©исц
     *
     * @access public
     * @return boolean
     */
    public static function isAvailable()
    {
        return function_exists('mysql_connect');
    }
}