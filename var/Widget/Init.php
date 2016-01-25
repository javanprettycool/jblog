<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Init extends Widget{



    public function __construct($request, $response, $params)
    {
        parent::__construct($request, $response, $params);
    }


    public function execute()
    {
        $options = $this->widget("Option");

        Common::$charset = $options->charset;

        Common::$exceptionHandle = "ExceptionHandle";

        $pathInfo = $this->request->getPathInfo();

        Router::setPathInfo($pathInfo);

        Router::setRoutes($options->routingTable);

        /** 初始化回执 */
        $this->response->setCharset($options->charset);
        $this->response->setContentType($options->contentType);

        /** 默认时区 */
        if (function_exists("ini_get") && !ini_get("date.timezone") && function_exists("date_default_timezone_set")) {
            @date_default_timezone_set('UTC');
        }

        /** 初始化时区 */
        Typecho_Date::setTimezoneOffset($options->timezone);

        /** 开始会话, 减小负载只针对后台打开session支持 */
        if ($this->widget('Widget_User')->hasLogin()) {
            @session_start();
        }

        //ob_start();
    }



}