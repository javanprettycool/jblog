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
     * @var Typecho_Db_Adapter
     */
    private $_adapter;
    /**
     * Ĭ������
     *
     * @access private
     * @var Typecho_Config
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