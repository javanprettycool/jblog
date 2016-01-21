<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Query{
    /** ���ݿ�ؼ��� */
    const KEYWORDS = '*PRIMARY|AND|OR|LIKE|BINARY|BY|DISTINCT|AS|IN|IS|NULL';

    /**
     * Ĭ���ֶ�
     *
     * @var array
     * @access private
     */
    private static $_default = array(
        'action' => NULL,
        'table'  => NULL,
        'fields' => '*',
        'join'   => array(),
        'where'  => NULL,
        'limit'  => NULL,
        'offset' => NULL,
        'order'  => NULL,
        'group'  => NULL,
        'having'  => NULL,
        'rows'   => array(),
    );

    /**
     * ���ݿ�������
     *
     * @var Typecho_Db_Adapter
     */
    private $_adapter;

    /**
     * ��ѯ���Ԥ�ṹ,�����鹹��,�������ΪSQL��ѯ�ַ���
     *
     * @var array
     */
    private $_sqlPreBuild;

    /**
     * ǰ׺
     *
     * @access private
     * @var string
     */
    private $_prefix;


    public function __construct(Adapter $adapter, $prefix)
    {
        $this->_adapter = $adapter;
        $this->_prefix = $prefix;

        $this->_sqlPreBuild = self::$_default;
    }


    /**
     * ���������ֵ
     *
     * @access private
     * @param string $str �������ֶ�ֵ
     * @return string
     */
    private function filterColumn($str)
    {
        $str = $str . ' 0';
        $length = strlen($str);
        $lastIsAlnum = false;
        $result = '';
        $word = '';
        $split = '';
        $quotes = 0;

        for ($i = 0; $i < $length; $i ++) {
            $cha = $str[$i];

            if (ctype_alnum($cha) || false !== strpos('_*', $cha)) {
                if (!$lastIsAlnum) {
                    if ($quotes > 0 && !ctype_digit($word) && '.' != $split
                        && false === strpos(self::KEYWORDS, strtoupper($word))) {
                        $word = $this->_adapter->quoteColumn($word);
                    } else if ('.' == $split && 'table' == $word) {
                        $word = $this->_prefix;
                        $split = '';
                    }

                    $result .= $word . $split;
                    $word = '';
                    $quotes = 0;
                }

                $word .= $cha;
                $lastIsAlnum = true;
            } else {

                if ($lastIsAlnum) {

                    if (0 == $quotes) {
                        if (false !== strpos(' ,)=<>.+-*/', $cha)) {
                            $quotes = 1;
                        } else if ('(' == $cha) {
                            $quotes = -1;
                        }
                    }

                    $split = '';
                }

                $split .= $cha;
                $lastIsAlnum = false;
            }

        }

        return $result;
    }

    /**
     * ���˱�ǰ׺,��ǰ׺��table.����
     *
     * @param string $string ��Ҫ�������ַ���
     * @return string
     */
    public function filterPrefix($string)
    {
        return (0 === strpos("table.", $string)) ? substr_replace($string, $this->_prefix, 0, 6) : $string;
    }

    /**
     * ��ȡ��ѯ�ִ�����ֵ
     *
     * @access public
     * @param string $attributeName ��������
     * @return string
     */
    public function getAttribute($attributeName)
    {
        return isset($this->_sqlPreBuild[$attributeName]) ? $this->_sqlPreBuild[$attributeName] : NULL;
    }

    /**
     * �����ѯ�ִ�����ֵ
     *
     * @access public
     * @param string $attributeName ��������
     * @return Typecho_Db_Query
     */
    public function cleanAttribute($attributeName)
    {
        if (isset($this->_sqlPreBuild[$attributeName])) {
            $this->_sqlPreBuild[$attributeName] = self::$_default[$attributeName];
        }
        return $this;
    }
    /**
     * ��ѯ��������
     *
     * @param integer $limit ��Ҫ��ѯ������
     * @return Typecho_Db_Query
     */
    public function limit($limit)
    {
        $this->_sqlPreBuild['limit'] = intval($limit);
        return $this;
    }

    /**
     * ��ѯ����ƫ����
     *
     * @param integer $offset ��Ҫƫ�Ƶ�����
     * @return Typecho_Db_Query
     */
    public function offset($offset)
    {
        $this->_sqlPreBuild['offset'] = intval($offset);
        return $this;
    }

    /**
     * ��ҳ��ѯ
     *
     * @param integer $page ҳ��
     * @param integer $pageSize ÿҳ����
     * @return Typecho_Db_Query
     */
    public function page($page, $pageSize)
    {
        $pageSize = intval($pageSize);
        $this->_sqlPreBuild['limit'] = $pageSize;
        $this->_sqlPreBuild['offset'] = (max(intval($page), 1) - 1) * $pageSize;
        return $this;
    }
}