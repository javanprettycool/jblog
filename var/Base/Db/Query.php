<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Query{
    /** 数据库关键字 */
    const KEYWORDS = '*PRIMARY|AND|OR|LIKE|BINARY|BY|DISTINCT|AS|IN|IS|NULL';

    /**
     * 默认字段
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
     * 数据库适配器
     *
     * @var Typecho_Db_Adapter
     */
    private $_adapter;

    /**
     * 查询语句预结构,由数组构成,方便组合为SQL查询字符串
     *
     * @var array
     */
    private $_sqlPreBuild;

    /**
     * 前缀
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
     * 过滤数组键值
     *
     * @access private
     * @param string $str 待处理字段值
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
     * 过滤表前缀,表前缀由table.构成
     *
     * @param string $string 需要解析的字符串
     * @return string
     */
    public function filterPrefix($string)
    {
        return (0 === strpos("table.", $string)) ? substr_replace($string, $this->_prefix, 0, 6) : $string;
    }

    /**
     * 获取查询字串属性值
     *
     * @access public
     * @param string $attributeName 属性名称
     * @return string
     */
    public function getAttribute($attributeName)
    {
        return isset($this->_sqlPreBuild[$attributeName]) ? $this->_sqlPreBuild[$attributeName] : NULL;
    }

    /**
     * 清除查询字串属性值
     *
     * @access public
     * @param string $attributeName 属性名称
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
     * 查询行数限制
     *
     * @param integer $limit 需要查询的行数
     * @return Typecho_Db_Query
     */
    public function limit($limit)
    {
        $this->_sqlPreBuild['limit'] = intval($limit);
        return $this;
    }

    /**
     * 查询行数偏移量
     *
     * @param integer $offset 需要偏移的行数
     * @return Typecho_Db_Query
     */
    public function offset($offset)
    {
        $this->_sqlPreBuild['offset'] = intval($offset);
        return $this;
    }

    /**
     * 分页查询
     *
     * @param integer $page 页数
     * @param integer $pageSize 每页行数
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