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
        $option = $this->widget("Option");

        Common::$charset = $this->options->charset;

        Common::$exceptionHandle = "ExceptionHandle";

        $pathInfo = $this->request->getPatInfo();
    }



}