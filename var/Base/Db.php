<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Db{
    /** 读取数据库 */
    const READ = 1;

    /** 写入数据库 */
    const WRITE = 2;

    /** 升序方式 */
    const SORT_ASC = 'ASC';

    /** 降序方式 */
    const SORT_DESC = 'DESC';

    /** 表内连接方式 */
    const INNER_JOIN = 'INNER';

    /** 表外连接方式 */
    const OUTER_JOIN = 'OUTER';

    /** 表左连接方式 */
    const LEFT_JOIN = 'LEFT';

    /** 表外连接方式 */
    const RIGHT_JOIN = 'RIGHT';

    /** 数据库查询操作 */
    const SELECT = 'SELECT';

    /** 数据库更新操作 */
    const UPDATE = 'UPDATE';

    /** 数据库插入操作 */
    const INSERT = 'INSERT';

    /** 数据库删除操作 */
    const DELETE = 'DELETE';

    /**
     * 数据库适配器
     * @var Adapter
     */
    private $_adapter;
    /**
     * 默认配置
     *
     * @access private
     * @var Config
     */
    private $_config;

    /**
     * 连接池
     *
     * @access private
     * @var array
     */
    private $_pool;

    /**
     * 已经连接
     *
     * @access private
     * @var array
     */
    private $_connectedPool;

    /**
     * 前缀
     *
     * @access private
     * @var string
     */
    private $_prefix;

    /**
     * 适配器名称
     *
     * @access private
     * @var string
     */
    private $_adapterName;

    /**
     * 实例化的数据库对象
     * @var Db
     */
    private static $_instance;



    public function __construct($adapterName, $prefix = "jblog")
    {
        $this->_adapterName = $adapterName;

        $adapterName = "Db_Adapter_" . $adapterName;

        if (call_user_func(array($adapterName, "isAvailbale"))){
            throw new Db_Exception("Adapter {$adapterName} is not available");
        }


        $this->_prefix = $prefix;


        $this->_pool = array();
        $this->_connectedPool = array();
        $this->_config = array();

        $this->_adapter = new $adapterName();
    }

    /**
     * 获取SQL词法构建器实例化对象
     *
     * @return Query
     */
    public function sql()
    {
        return new Query($this->_adapter, $this->_prefix);
    }


    public static function get()
    {
        if (empty(self::$_instance)){
            throw new Db_Exception("Missing Database Object");
        }
        return self::$_instance;
    }


    public static function set(Db $db)
    {
        self::$_instance = $db;
    }

    public function select()
    {
        $args = func_get_args();
        return call_user_func(array($this->sql(), "select"), $args ? $args : NULL);
    }


    public function delete()
    {

    }

    public function update()
    {

    }


    public function  query()
    {

    }

    /**
     * 一次取出所有行
     *
     * @param mixed $query 查询对象
     * @param array $filter 行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中
     * @return array
     */
    public function fectchAll($query, array $filter = NULL)
    {
        $resource = $this->query($query, self::READ);
        $result = array();

        if (!empty($filter))
        {
            list($object, $method) = $filter;
        }

        while ($row = $this->_adapter->fetch($resource)){
            $result[] = $filter ? call_user_func(array(&$object, $method), $row) : $row;
        }

        return $result;
    }

    /**
     * 转义参数
     *
     * @param array $values
     * @access protected
     * @return array
     */
    protected function quoteValues(array $values)
    {
        foreach ($values as &$value) {
            if (is_array($value)) {
                $value = '(' . implode(',', array_map(array($this->_adapter, 'quoteValue'), $value)) . ')';
            } else {
                $value = $this->_adapter->quoteValue($value);
            }
        }

        return $values;
    }





}