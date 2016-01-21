<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Mysql_Adapter implements Adapter{

    /**
     * ���ݿ������ַ�����ʾ
     *
     * @access private
     * @var resource
     */
    private $_dbLink;

    /**
     * �ж��������Ƿ����
     *
     * @access public
     * @return boolean
     */
    public static function isAvailable()
    {
        return function_exists("mysql_connect");
    }

    /**
     * ���ݿ����Ӻ���
     *
     * @param Typecho_Config $config ���ݿ�����
     * @return resource
     */
    public function connect(Config $config)
    {}


    /**
     * ִ�����ݿ��ѯ
     *
     * @param string $query ���ݿ��ѯSQL�ַ���
     * @param mixed $handle ���Ӷ���
     * @param integer $op ���ݿ��д״̬
     * @param string $action ���ݿ⶯��
     * @return resource
     */
    public function query($query, $handle, $op = Typecho_Db::READ, $action = NULL)
    {}

    /**
     * �����ݲ�ѯ������һ����Ϊ����ȡ��,�����ֶ�����Ӧ�����ֵ
     *
     * @param resource $resource ��ѯ����Դ����
     * @return array
     */
    public function fetch($resource)
    {
        return mysql_fetch_assoc($resource);
    }

    /**
     * �����ݲ�ѯ������һ����Ϊ����ȡ��,�����ֶ�����Ӧ��������
     *
     * @param resource $resource ��ѯ����Դ����
     * @return object
     */
    public function fetchObject($resource)
    {
        return mysql_fetch_object($resource);
    }

    /**
     * ����ת�庯��
     *
     * @param string $string ��Ҫת����ַ���
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
    {}

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