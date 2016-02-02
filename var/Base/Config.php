<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Config implements Iterator
{
    private $_currentConfig = array();


    public function __construct($config = array())
    {
        $this->setDefault($config);
    }

    public static function factory($config = array())
    {
        return new Config($config);
    }

    public function setDefault($config, $replace = false)
    {
        if (empty($config)) {
            return;
        }

        /** 初始化参数 */
        if (is_string($config)) {
            parse_str($config, $params);
        } else {
            $params = $config;
        }

        /** 设置默认参数 */
        foreach ($params as $name => $value) {
            if ($replace || !array_key_exists($name, $this->_currentConfig)) {
                $this->_currentConfig[$name] = $value;
            }
        }
    }


    public function __get($name)
    {
        return isset($this->_currentConfig[$name]) ? $this->_currentConfig[$name] : NULL;
    }


    public function __set($name, $value)
    {
        $this->_currentConfig[$name] = $value;
    }


    public function __call($name, $args)
    {
        echo $this->_currentConfig[$name];
    }


    public function __isSet($name)
    {
        return isset($this->_currentConfig[$name]);
    }


    public function __toString()
    {
        return serialize($this->_currentConfig);
    }

    /**
     * 重设指针
     *
     * @access public
     * @return void
     */
    public function rewind()
    {
        reset($this->_currentConfig);
    }

    /**
     * 返回当前值
     *
     * @access public
     * @return mixed
     */
    public function current()
    {
        return current($this->_currentConfig);
    }

    /**
     * 指针后移一位
     *
     * @access public
     * @return void
     */
    public function next()
    {
        next($this->_currentConfig);
    }

    /**
     * 获取当前指针
     *
     * @access public
     * @return mixed
     */
    public function key()
    {
        return key($this->_currentConfig);
    }

    /**
     * 验证当前值是否到达最后
     *
     * @access public
     * @return boolean
     */
    public function valid()
    {
        return false !== $this->current();
    }
}