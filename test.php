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

$result = $db->fetchAll(($db->select()->from("table.options")));
//var_dump($result);

$row = array();

foreach($result as $val) {
    $row[$val['name']] = $val['value'];

}

$routingTable = $row['routingTable'];
$routingTable = unserialize($routingTable);
var_dump($routingTable);




