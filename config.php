<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */


@set_include_path(get_include_path() . PATH_SEPARATOR .
    dirname(__FILE__) . '/var');

/** 定义根目录 */
define('__ROOT_DIR__', dirname(__FILE__));

/** 定义插件目录(相对路径) */
define('__PLUGIN_DIR__', '/usr/plugins');

/** 定义模板目录(相对路径) */
define('__THEME_DIR__', '/usr/themes');

/** 后台路径(相对路径) */
define('__ADMIN_DIR__', '/admin/');


/** 载入API支持 */
require_once 'Base/Common.php';

/** 载入Response支持 */
require_once 'Base/Response.php';

/** 载入配置支持 */
require_once 'Base/Config.php';

/** 载入异常支持 */
require_once 'Base/Exception.php';

///** 载入插件支持 */
//require_once 'Base/Plugin.php';

/** 载入国际化支持 */
require_once 'Base/I18n.php';

/** 载入数据库支持 */
require_once 'Base/Db.php';

/** 载入路由器支持 */
require_once 'Base/Router.php';

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
