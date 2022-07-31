<?php

class Curl {

    protected $_useragent = 'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1';
    protected $_url;
    protected $_followlocation;
    protected $_timeout;
    protected $_maxRedirects;
    protected $_cookieFileLocation = './lk.cookie.txt';
    protected $_post;
    protected $_postFields;
    protected $_curlFiles;
    protected $_referer = "https://www.lakestone.ru"; # todo Исправить на реальный хост
    //protected $_referer = "https://dev-roman.lakestone.ru"; # todo Исправить на реальный хост
    protected $_ssl_verifypeer = false;
    protected $_ssl_verifyhost = false;

    protected $_session;
    protected $_webpage;
    protected $_includeHeader;
    protected $_noBody;
    protected $_status;
    protected $_binaryTransfer;

    public $authentication = 0;
    public $auth_name = '';
    public $auth_pass = '';
    public $httpheader;

    public function useAuth($use)
    {
        $this->authentication = 0;
        if ($use == true) $this->authentication = 1;
    }

    public function setName($name)
    {
        $this->auth_name = $name;
    }

    public function setPass($pass)
    {
        $this->auth_pass = $pass;
    }

    public function getName()
    {
        return $this->auth_name;
    }

    public function getPass()
    {
        return $this->auth_pass;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function __construct($url, $followlocation = true, $timeOut = 30, $maxRedirecs = 4, $binaryTransfer = false, $includeHeader = false, $noBody = false)
    {
        $this->_url = $url;
        $this->_followlocation = $followlocation;
        $this->_timeout = $timeOut;
        $this->_maxRedirects = $maxRedirecs;
        $this->_noBody = $noBody;
        $this->_includeHeader = $includeHeader;
        $this->_binaryTransfer = $binaryTransfer;

        $this->_cookieFileLocation = dirname(__FILE__) . '/cookie.txt';
    }

    public function setReferer($referer)
    {
        $this->_referer = $referer;
    }

    public function setCookiFileLocation($path)
    {
        $this->_cookieFileLocation = $path;
    }

    public function setPostPramas($postFields)
    {
        $this->_post = true;
        $this->_postFields = $postFields;
    }

    public function setGetPramas($getFields)
    {
        $this->_get = true;
        $this->_getFields = $getFields;
    }

    public function getGetPramas()
    {
        return $this->_getFields;
    }

    public function setUserAgent($userAgent)
    {
        $this->_useragent = $userAgent;
    }

    public function getFiles($files)
    {
        $this->_curlFiles = $files;
    }

    public function createCurl($url = 'nul')
    {
        if ($url != 'nul') {
            $this->_url = $url;
        }

        if ($this->_get ?? false) {
            $this->_url = $this->_url . '?' . http_build_query($this->_getFields);
        }

        $s = curl_init();

        curl_setopt($s, CURLOPT_URL, $this->_url);
        #curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:'));
        curl_setopt($s, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($s, CURLOPT_MAXREDIRS, $this->_maxRedirects);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION, $this->_followlocation);
        curl_setopt($s, CURLOPT_COOKIEJAR, $this->_cookieFileLocation);
        curl_setopt($s, CURLOPT_COOKIEFILE, $this->_cookieFileLocation);
        curl_setopt($s, CURLOPT_SSL_VERIFYPEER, $this->_ssl_verifypeer);
        curl_setopt($s, CURLOPT_SSL_VERIFYHOST, $this->_ssl_verifyhost);

        if (eRU($this->httpheader)) {
            curl_setopt($s, CURLOPT_HTTPHEADER, $this->httpheader);
        } else {
            curl_setopt($s, CURLOPT_HTTPHEADER, ['Expect:']);
        }

        if ($this->authentication == 1) {
            curl_setopt($s, CURLOPT_USERPWD, $this->auth_name . ':' . $this->auth_pass);
        }

        if (eRU($this->_curlFiles)) {
            foreach ($this->_curlFiles as $key => $file) {
                $this->_postFields[$key] = new \CurlFile($file['tmp_name'], 'file/exgpd', $file["name"]);
            }
        }

        if ($this->_post) {
            curl_setopt($s, CURLOPT_POST, true);
            curl_setopt($s, CURLOPT_POSTFIELDS, $this->_postFields);
        }

        if ($this->_includeHeader) {
            curl_setopt($s, CURLOPT_HEADER, true);
        }

        if ($this->_noBody) {
            curl_setopt($s, CURLOPT_NOBODY, true);
        }

        curl_setopt($s, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($s, CURLOPT_REFERER, $this->_referer);

        #curl_setopt($s,CURLOPT_VERBOSE, 1);
        #curl_setopt($s,CURLOPT_STDERR, fopen($_SERVER["DOCUMENT_ROOT"] . '/curl.txt', 'a+'));

        $this->_webpage = curl_exec($s);
        $this->_status = curl_getinfo($s, CURLINFO_HTTP_CODE);

        curl_close($s);
    }

    public function getHttpStatus()
    {
        return $this->_status;
    }

    public function getContent()
    {
        return $this->_webpage;
    }

    public function getContentJsonDecode()
    {
        return json_decode($this->getContent(), true);
    }

    public function __tostring()
    {
        return $this->_webpage;
    }
}