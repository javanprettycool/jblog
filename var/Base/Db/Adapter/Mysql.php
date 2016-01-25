<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Db_Adapter_Mysql implements Db_Adapter{

    /**
     * 数据库连接字符串标示
     *
     * @access private
     * @var resource
     */
    private $_dbLink;

    /**
     * 判断适配器是否可用
     *
     * @access public
     * @return boolean
     */
    public static function isAvailable()
    {
        return function_exists('mysql_connect');
    }

    /**
     * 数据库连接函数
     *
     * @param Config $config 数据库配置
     * @throws Db_Exception
     * @return resource
     */
    public function connect(Config $config)
    {
        if ($this->_dbLink = @mysql_connect($config->host . (empty($config->port) ? '' : ':' . $config->port),
        $config->user, $config->password, true)) {
            if (@mysql_select_db($config->database, $this->_dbLink)) {
                if ($config->charset) {
                    mysql_query("SET NAME '{$config->charset}'", $this->_dbLink);
                }
                return $this->_dbLink;
            }
        }
    }


    /**
     * 执行数据库查询
     *
     * @param string $query 数据库查询SQL字符串
     * @param mixed $handle 连接对象
     * @param integer $op 数据库读写状态
     * @param string $action 数据库动作
     * @throws Db_Exception
     * @return resource
     */
    public function query($query, $handle, $op = Db::READ, $action = NULL)
    {
        if ($resource = @mysql_query($query instanceof Db_Query ? $query->__toString() : $query, $handle)) {
            return $resource;
        }

        throw new Db_Exception(@mysql_error($this->_dbLink), @mysql_errno($this->_dbLink));
    }

    /**
     * 将数据查询的其中一行作为数组取出,其中字段名对应数组键值
     *
     * @param resource $resource 查询返回资源标识
     * @return array
     */
    public function fetch($resource)
    {
        return mysql_fetch_assoc($resource);
    }

    /**
     * 将数据查询的其中一行作为对象取出,其中字段名对应对象属性
     *
     * @param resource $resource 查询的资源数据
     * @return object
     */
    public function fetchObject($resource)
    {
        return mysql_fetch_object($resource);
    }

    /**
     * 引号转义函数
     *
     * @param string $string 需要转义的字符串
     * @return string
     */
    public function quoteValue($string)
    {
        return '\'' . str_replace(array('\'', '\\'), array('\'\'', '\\\\'), $string) . '\'';
    }

    /**
     * �������Ź���
     *
     * @access public
     * @param string $string
     * @return string
     */
    public function quoteColumn($string)
    {
        return '`' . $string . '`';
    }

    /**
     * �ϳɲ�ѯ���
     *
     * @access public
     * @param array $sql ��ѯ����ʷ�����
     * @return string
     */
    public function parseSelect(array $sql)
    {
        if (!empty($sql['join'])) {
            foreach ($sql['join'] as $val ) {
                list($table, $condition, $op) = $val;
                $sql['table'] = "{$sql['table']} {$op} JOIN {$table} ON {$condition}";
            }
        }

        $sql['limit'] = empty($sql['limit']) ? NULL : 'LIMIT' . $sql['limit'];
        $sql['offset'] = empty($sql['offset']) ? NULL : 'OFFSET' . $sql['offset'];

        return 'SELECT ' . $sql['fields'] . ' FROM ' . $sql['table'] .
        $sql['where'] . $sql['group'] . $sql['having'] . $sql['order'] . $sql['limit'] . $sql['offset'];
    }

    /**
     * ȡ�����һ�β�ѯӰ�������
     *
     * @param resource $resource ��ѯ����Դ����
     * @param mixed $handle ���Ӷ���
     * @return integer
     */
    public function affectedRows($resource, $handle)
    {
        return mysql_affected_rows($handle);
    }

    /**
     * ȡ�����һ�β��뷵�ص�����ֵ
     *
     * @param resource $resource ��ѯ����Դ����
     * @param mixed $handle ���Ӷ���
     * @return integer
     */
    public function lastInsertId($resource, $handle)
    {
        return mysql_insert_id($handle);
    }



}