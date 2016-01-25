<?php
/**
 * Author: Javan
 * Date: 2016/1/22
 * Description:
 */

@set_include_path(get_include_path() . PATH_SEPARATOR .
    dirname(__FILE__) . '/var');

require_once 'Base\Db.php';
require_once 'Base\Common.php';


Common::init();

$db = new Db("Mysql", "jblog_");

$db->addServer(array (
    'host' => 'localhost',
    'user' => 'root',
    'charset' => 'utf8',
    'port' => '3306',
    'database' => 'jblog',
), Db::READ | Db::WRITE);

Db::set($db);

$result = $db->fetchAll(($db->select('slug')->from("table.contents")->where('cid=4')));

var_dump($result);




