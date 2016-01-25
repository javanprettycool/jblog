<?php



class Request{

    private static $_instance = NULL;

    private $_pathInfo = NULL;

    private $_server = NULL;

    private $_requestUri;

    private $_requestRoot;

    private $_baseUrl;

    private $_ip;

    private static $_httpParams;



    public function __construct()
    {

    }


    public static function getInstance(){
        if (NULL === self::$_instance){
            self::$_instance = new Request();
        }
        return self::$_instance;
    }

    public static function setInstance(){

    }


    public function getPathInfo($inputEncoding = NULL, $outputEncoding = NULL)
    {
        if (!empty($this->_pathInfo)){
            return $this->_pathInfo;
        }



    }

    public function getRequestUri()
    {
        if (!empty($this->_requestUri)){
            return $this->_requestUri;
        }

        $requestUri = '/';

        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (
            // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
            isset($_SERVER['IIS_WasUrlRewritten'])
            && $_SERVER['IIS_WasUrlRewritten'] == '1'
            && isset($_SERVER['UNENCODED_URL'])
            && $_SERVER['UNENCODED_URL'] != ''
        ) {
            $requestUri = $_SERVER['UNENCODED_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if (isset($_SERVER['HTTP_HOST']) && strstr($requestUri, $_SERVER['HTTP_HOST'])) {
                $parts       = @parse_url($requestUri);

                if (false !== $parts) {
                    $requestUri  = (empty($parts['path']) ? '' : $parts['path'])
                        . ((empty($parts['query'])) ? '' : '?' . $parts['query']);
                }
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }

        return $this->_requestUri = $requestUri;

    }

    public function getBaseUrl()
    {
        if (!empty($this->_baseUrl)){
            return $this->_baseUrl;
        }

        $filename = isset($_SERVER['SCRIPT_FILENAME']) ? basename($_SERVER['SCRIPT_FILENAME']) : '';

        if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
            $baseUrl = $_SERVER['SCRIPT_NAME'];
        } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
            $baseUrl = $_SERVER['PHP_SELF'];
        } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
            $baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path    = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
            $file    = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
            $segs    = explode('/', trim($file, '/'));
            $segs    = array_reverse($segs);
            $index   = 0;
            $last    = count($segs);
            $baseUrl = '';
            do {
                $seg     = $segs[$index];
                $baseUrl = '/' . $seg . $baseUrl;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
        }

        $finalBaseUrl = NULL;
        $requestUri = $this->getRequestUri();

        if (0 === strpos($requestUri, $baseUrl)) {
            // full $baseUrl matches
            $finalBaseUrl = $baseUrl;
        } else if (0 === strpos($requestUri, dirname($baseUrl))) {
            // directory portion of $baseUrl matches
            $finalBaseUrl = rtrim(dirname($baseUrl), '/');
        } else if (!strpos($requestUri, basename($baseUrl))) {
            // no match whatsoever; set it blank
            $finalBaseUrl = '';
        } else if ((strlen($requestUri) >= strlen($baseUrl))
            && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0)))
        {
            // If using mod_rewrite or ISAPI_Rewrite strip the script filename
            // out of baseUrl. $pos !== 0 makes sure it is not matching a value
            // from PATH_INFO or QUERY_STRING
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return ($this->_baseUrl = (NULL === $finalBaseUrl) ? rtrim($baseUrl, '/') : $finalBaseUrl);


    }

}