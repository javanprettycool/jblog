<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */


abstract class Widget{


    private static $_widgetPool = array();

    protected $row = array();

    public $stack = array();

    public $sequence = 0;

    public $request = NULL;

    public $response = NULL;

    public $config = NULL;


    public function __construct($request, $response, $params = NULL)
    {

        $this->request = $request;
        $this->response = $response;


    }


    public function execute(){}



    public static function widget($name, $request = NULL, $params = NULL, $allowResponse = true){

        $className = $name;

        if (!isset(self::$_widgetPool[$name])){

            if (!class_exists($className)){
                throw new Handle_Exception($className);
            }

            if (!empty($request)){

            } else {
                $requestObject = new Request();
            }


            $responseObject = $allowResponse ? Response::getIntance() : NULL;


            $widget = new $className($requestObject, $responseObject, $params);
            $widget->execute();

            self::$_widgetPool[$name] = $widget;

        }


        return self::$_widgetPool[$name];

    }


    public function __get($name)
    {
        if (array_key_exists($name, $this->row)) {
            return $this->row[$name];
        } else {
            $method = "___" . $name;

            if (method_exists($method)) {
                return $this->$method();
            } else {}
        }
    }

    public function __set($name, $value)
    {
        $this->row[$name] = $value;
    }
}