<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */


class Response{

    /**
     * http code
     *
     * @access private
     * @var array
     */
    private static $_httpCode = array(
        100 => 'Continue',
        101	=> 'Switching Protocols',
        200	=> 'OK',
        201	=> 'Created',
        202	=> 'Accepted',
        203	=> 'Non-Authoritative Information',
        204	=> 'No Content',
        205	=> 'Reset Content',
        206	=> 'Partial Content',
        300	=> 'Multiple Choices',
        301	=> 'Moved Permanently',
        302	=> 'Found',
        303	=> 'See Other',
        304	=> 'Not Modified',
        305	=> 'Use Proxy',
        307	=> 'Temporary Redirect',
        400	=> 'Bad Request',
        401	=> 'Unauthorized',
        402	=> 'Payment Required',
        403	=> 'Forbidden',
        404	=> 'Not Found',
        405	=> 'Method Not Allowed',
        406	=> 'Not Acceptable',
        407	=> 'Proxy Authentication Required',
        408	=> 'Request Timeout',
        409	=> 'Conflict',
        410	=> 'Gone',
        411	=> 'Length Required',
        412	=> 'Precondition Failed',
        413	=> 'Request Entity Too Large',
        414	=> 'Request-URI Too Long',
        415	=> 'Unsupported Media Type',
        416	=> 'Requested Range Not Satisfiable',
        417	=> 'Expectation Failed',
        500	=> 'Internal Server Error',
        501	=> 'Not Implemented',
        502	=> 'Bad Gateway',
        503	=> 'Service Unavailable',
        504	=> 'Gateway Timeout',
        505	=> 'HTTP Version Not Supported'
    );

    const CHARSET = "UFT-8";

    private $charset;

    private static $_instance = NULL;

    public function __construct()
    {

    }


    public static function getInstance(){
        if (NULL === self::$_instance){
            self::$_instance = new Response();
        }
        return self::$_instance;
    }

    /**
     * 设置默认回执编码
     *
     * @access public
     * @param string $charset 字符集
     * @return void
     */
    public function setCharset($charset = null)
    {
        $this->_charset = empty($charset) ? self::CHARSET : $charset;
    }

    /**
     * 获取字符集
     *
     * @access public
     * @return string
     */
    public function getCharset()
    {
        if (empty($this->_charset)) {
            $this->setCharset();
        }

        return $this->_charset;
    }

    /**
     * 在http头部请求中声明类型和字符集
     *
     * @access public
     * @param string $contentType 文档类型
     * @return void
     */
    public function setContentType($contentType = 'text/html')
    {
        header('Content-Type: ' . $contentType . '; charset=' . $this->getCharset(), true);
    }

    /**
     * 设置http头
     *
     * @access public
     * @param string $name 名称
     * @param string $value 对应值
     * @return void
     */
    public function setHeader($name, $value)
    {
        header($name . ': ' . $value, true);
    }
}