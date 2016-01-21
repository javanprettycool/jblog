<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Option extends Widget{


    protected $db;


    public function __construct($request, $response, $params)
    {
        parent::__construct($request, $response, $params);

        $this->db = Db::get();
    }

    public function execute()
    {


    }
}