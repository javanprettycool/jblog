<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */
class Router
{
    public static $current;

    private static $_routingTable = array();

    private static $_pathInfo = NULL;

    public static function setPathInfo($pathInfo = '/')
    {
        self::$_pathInfo = $pathInfo;
    }

    public static function getPathInfo()
    {
        if (NULL === self::$_pathInfo) {
            self::setPathInfo();
        }

        return self::$_pathInfo;
    }


    public static function dispatch()
    {
        $pathInfo = self::getPathInfo();

        foreach (self::$_routingTable as $key => $route) {
            if (preg_match($route['regx'], $pathInfo, $matches)) {
                self::$current = $key;

                try {
                    $params = NULL;

                    if (!empty($route['param'])) {
                        unset($matches[0]);
                        $params = array_combine($route['param'], $matches);
                    }

                    $widget = Widget::widget($route['widget'], NULL, $params);

                    if (isset($route['action'])) {
                        $method = $route['action'];
                        $widget->{$method}();
                    }

                    return;

                } catch (Exception $e) {
                    if (404 == $e->getCode()) {
                        Widget::destory($route['widget']);
                        continue;
                    }

                    throw $e;

                }
            }
        }
        /** 载入路由异常支持 */
        throw new HandleException("Path '{$pathInfo}' not found", 404);

    }


    public static function url()
    {}

    public static function setRoutes($routes)
    {
        if (isset($routes[0])) {
            self::$_routingTable = $routes[0];
        } else {
            /** 解析路由配置 */
            $parser = new Router_Parser($routes);
            self::$_routingTable = $parser->parse();
        }
    }

    public static function get($routeName)
    {}


}