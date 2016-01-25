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
     * @var Db_Adapter
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

        $adapterName = 'Db_Adapter_' . $adapterName;

        if (!call_user_func(array($adapterName, "isAvailable"))){
            throw new Db_Exception("Adapter {$adapterName} is not available");
        }


        $this->_prefix = $prefix;


        $this->_pool = array();
        $this->_connectedPool = array();
        $this->_config = array();

        $this->_adapter = new $adapterName();
    }

    /**
     * ��ȡSQL�ʷ�������ʵ��������
     *
     * @return Db_Query
     */
    public function sql()
    {
        return new Db_Query($this->_adapter, $this->_prefix);
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
        return call_user_func_array(array($this->sql(), "select"), $args ? $args : array('*'));
    }


    public function delete()
    {

    }

    public function update()
    {

    }


    public function  query($query, $op = self::READ, $action = self::SELECT)
    {
        if ($query instanceof Db_Query) {
            $action = $query->getAttribute('action');
            $op = ($action == self::INSERT || $action == self::DELETE
                || $action == self::UPDATE) ? self::WRITE : self::READ;
        } else if (!is_string($query)) {
            return $query;
        }

        if (!isset($this->_connectedPool[$op])) {
            if (empty($this->_pool[$op])) {
                throw new Db_Exception("Miss db connection");
            }

            $selectConnection = rand(0, count($this->_pool[$op]) - 1);
            $selectConnectionConfig = $this->_config[$this->_pool[$op][$selectConnection]];
            $selectConnectionHandle = $this->_adapter->connect($selectConnectionConfig);
            $this->_connectedPool[$op] = &$selectConnectionHandle;
        }

        $handle = $this->_connectedPool[$op];
        //var_dump($query);
        $resource = $this->_adapter->query($query, $handle);

        //var_dump($action);
        if ($action) {
            switch ($action) {
                case self::UPDATE:
                case self::DELETE:
                    return $this->_adapter->affectedRows($resource, $handle);
                case self::INSERT:
                    return $this->_adapter->lastInsertId($resource, $handle);
                case self::SELECT:
                default:
                    return $resource;
            }

        } else {
            return $resource;
        }
    }

    /**
     * һ��ȡ��������
     *
     * @param mixed $Db_Query ��ѯ����
     * @param array $filter �й���������,����ѯ��ÿһ����Ϊ��һ����������ָ���Ĺ�������
     * @return array
     */
    public function fetchAll(Db_Query $query, array $filter = NULL)
    {   //var_dump($query);
        $resource = $this->query($query, self::READ);
        $result = array();
        var_dump($resource);
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
     * ת�����
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


    public function addServer($config, $op)
    {
        $this->_config[] = Config::factory($config);
        $key = count($this->_config) - 1;

        /** 将连接放入池中 */
        switch ($op) {
            case self::READ:
            case self::WRITE:
                $this->_pool[$op][] = $key;
                break;
            default:
                $this->_pool[self::READ][] = $key;
                $this->_pool[self::WRITE][] = $key;
                break;
        }

        //var_dump($this->_pool);
    }




}