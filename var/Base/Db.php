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
     * @var Typecho_Db_Adapter
     */
    private $_adapter;
    /**
     * 默认配置
     *
     * @access private
     * @var Typecho_Config
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
     * @var Typecho_Db
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




    public function get()
    {
        if (empty(self::$_instance)){
            throw new Db_Exception("Missing Database Object");
        }
        return self::$_instance;
    }



}