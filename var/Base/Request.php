<?php



class Request{

    private static $_instance = NULL;

    private $_server = NULL;

    private $_requestUri;

    private $_requestRoot;

    private $_baseUrl;

    private $_ip;

    private static $_httpParams;



    public function __construct()
    {

    }


    public static function getInstance(){
        if (NULL === self::$_instance){
            self::$_instance = new Request();
        }
        return self::$_instance;
    }

    public static function setInstance(){

    }


}