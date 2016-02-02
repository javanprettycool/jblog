<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */


class Common{
    /**
     * �汾
     */
    const VERSION = "0.1";

    /**
     * Ĭ�ϱ���
     *
     * @access public
     * @var string
     */
    public static $charset = 'UTF-8';

    /**
     * �쳣������
     *
     * @access public
     * @var string
     */
    public static $exceptionHandle;





    public static function init()
    {
        if (function_exists("spl_autoload_register")) {
            spl_autoload_register(array("Common", "__autoLoad"));
        } else {
            function __autoLoad($className)
            {
                Common::__autoLoad($className);
            }
        }


        @set_exception_handler(array('Common', 'exceptionHandle'));
    }


    public static function __autoLoad($className)
    {   //echo 'Base/' . str_replace(array("\\", "_"), "/", $className) . ".php";
        $filename = __DIR__ . "/" . str_replace(array("\\", "_"), "/", $className) . ".php";
        if (file_exists($filename)) {
            @include_once 'Base/' . str_replace(array("\\", "_"), "/", $className) . ".php";
        } else {
            @include_once 'Widget/' . str_replace(array("\\", "_"), "/", $className) . ".php";
        }
    }


    public static function exceptionHandle(Exception $exception)
    {
        echo $exception->getMessage();
    }

    public static function url($path, $prefix)
    {
        $path = (0 === strpos($path, './')) ? substr($path, 2) : $path;
        return rtrim($prefix, '/') . '/' . str_replace('//', '/', ltrim($path, '/'));
    }

    public static function safeUrl($url)
    {
        //~ 针对location的xss过滤, 因为其特殊性无法使用removeXSS函数
        //~ fix issue 66
        $params = parse_url(str_replace(array("\r", "\n", "\t", ' '), '', $url));

        /** 禁止非法的协议跳转 */
        if (isset($params['scheme'])) {
            if (!in_array($params['scheme'], array('http', 'https'))) {
                return '/';
            }
        }

        /** 过滤解析串 */
        $params = array_map(array('Common', '__removeUrlXss'), $params);
        return self::buildUrl($params);
    }

    public static function buildUrl($params)
    {
        return (isset($params['scheme']) ? $params['scheme'] . '://' : NULL)
            . (isset($params['user']) ? $params['user'] . (isset($params['pass']) ? ':' . $params['pass'] : NULL) . '@' : NULL)
            . (isset($params['host']) ? $params['host'] : NULL)
            . (isset($params['port']) ? ':' . $params['port'] : NULL )
            . (isset($params['path']) ? $params['path'] : NULL)
            . (isset($params['query']) ? '?' . $params['query'] : NULL)
            . (isset($params['fragment']) ? '#' . $params['fragment'] : NULL);
    }

    /**
     * 处理XSS跨站攻击的过滤函数
     *
     * @author kallahar@kallahar.com
     * @link http://kallahar.com/smallprojects/php_xss_filter_function.php
     * @access public
     * @param string $val 需要处理的字符串
     * @return string
     */
    public static function removeXSS($val)
    {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08]|[\x0b-\x0c]|[\x0e-\x19])/', '', $val);

        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';

        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

            // &#x0040 @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
            // &#00064 @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags

                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }

        return $val;
    }
}