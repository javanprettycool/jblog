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

    public $request;

    public $response;

    public $config;

    /**
     * 队列长度
     *
     * @access public
     * @var integer
     */
    public $length = 0;


    public function __construct($request, $response, $params = NULL)
    {

        $this->request = $request;
        $this->response = $response;
        $this->config = new Config();

        if (!empty($params)) {
            $this->config->setDefault($params);
        }


    }


    public function execute(){}



    public static function widget($name, $request = NULL, $params = NULL, $allowResponse = true){

        $className = $name;

        if (!isset(self::$_widgetPool[$name])){

            if (!class_exists($className)){
                throw new HandleException($className);
            }

            if (!empty($request)){
                $requestObject = new Request();
            } else {
                $requestObject = Request::getInstance();
            }


            $responseObject = $allowResponse ? Response::getInstance() : NULL;


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

            if (method_exists($this, $method)) {
                return $this->$method();
            } else {}
        }
    }

    public function __set($name, $value)
    {
        $this->row[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->row[$name]);
    }

    /**
     * 释放组件
     *
     * @access public
     * @param string $alias 组件名称
     * @return void
     */
    public static function destory($alias)
    {
        if (isset(self::$_widgetPool[$alias])) {
            unset(self::$_widgetPool[$alias]);
        }
    }


    public function have()
    {
        return !empty($this->stack);
    }

    /**
     * 将每一行的值压入堆栈
     *
     * @param array $value 每一行的值
     * @return array
     */
    public function push(array $value)
    {
        //将行数据按顺序置位
        $this->row = $value;
        $this->length ++;

        $this->stack[] = $value;
        return $value;
    }
}