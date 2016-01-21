<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Db{
    /** ��ȡ���ݿ� */
    const READ = 1;

    /** д�����ݿ� */
    const WRITE = 2;

    /** ����ʽ */
    const SORT_ASC = 'ASC';

    /** ����ʽ */
    const SORT_DESC = 'DESC';

    /** �������ӷ�ʽ */
    const INNER_JOIN = 'INNER';

    /** �������ӷ�ʽ */
    const OUTER_JOIN = 'OUTER';

    /** �������ӷ�ʽ */
    const LEFT_JOIN = 'LEFT';

    /** �������ӷ�ʽ */
    const RIGHT_JOIN = 'RIGHT';

    /** ���ݿ��ѯ���� */
    const SELECT = 'SELECT';

    /** ���ݿ���²��� */
    const UPDATE = 'UPDATE';

    /** ���ݿ������� */
    const INSERT = 'INSERT';

    /** ���ݿ�ɾ������ */
    const DELETE = 'DELETE';

    /**
     * ���ݿ�������
     * @var Adapter
     */
    private $_adapter;
    /**
     * Ĭ������
     *
     * @access private
     * @var Config
     */
    private $_config;

    /**
     * ���ӳ�
     *
     * @access private
     * @var array
     */
    private $_pool;

    /**
     * �Ѿ�����
     *
     * @access private
     * @var array
     */
    private $_connectedPool;

    /**
     * ǰ׺
     *
     * @access private
     * @var string
     */
    private $_prefix;

    /**
     * ����������
     *
     * @access private
     * @var string
     */
    private $_adapterName;

    /**
     * ʵ���������ݿ����
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
     * ��ȡSQL�ʷ�������ʵ��������
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
     * һ��ȡ��������
     *
     * @param mixed $query ��ѯ����
     * @param array $filter �й���������,����ѯ��ÿһ����Ϊ��һ����������ָ���Ĺ�������
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





}