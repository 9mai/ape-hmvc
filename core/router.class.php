<?php

interface routeInterface {
    public function __construct();
    public static function &getInstance();
    public static function getURI();
    public static function getURL();
    public static function getBaseURL();
}

Class Router implements routeInterface 
{

    private $module = DEFAULT_MODULE;
    private $controller = DEFAULT_CONTROLLER;
    private $method = DEFAULT_METHOD;
    private $url = array();
    private static $instance;
    
    function __construct()
    {
        $uri = trim(strtolower($this->getURI()),'/');
        if(function_exists('filter_var')) {
            $uri = filter_var($uri, FILTER_SANITIZE_URL);
        } else {
            $uri = preg_replace("/([^a-z0-9+_(\x80-\xff)\/])/i",'',$uri);
        }
        
        if ($routes = Loader::loadConfig('routes')) {
            $params = array();
            foreach ($routes as $k=>$v) {
                $params[] = preg_replace('#^'.$k.'$#', $v, $uri);
            }
            if ($params) {
                $uri = trim(implode('/', array_filter($params)), '/');
                unset($params);
            }
        }
        unset($routes);
        $parts = explode('/', $uri);
        // dems private vars!
        $path_parts = array();
        foreach($parts as $p){
            if (strpos($p, '__') !== false) {
                // remove *all* preceeding underscores
                $p = preg_replace("/^_+[^a-z]/", "", $p); 
            }
            $path_parts[] = $p;
        }
        if (isset($path_parts['0'])) {
            $this->module = $path_parts['0'];
        }
        $key = array_search($this->module,$path_parts);
        $this->url['params'] = array($this->module);
        if (isset($path_parts[($key)])) {
            $this->url['params'] = array_slice($path_parts, ($key ));
        }
        if (isset($this->url['params']['1'])) {
            $this->controller = $this->url['params']['1'];
        }
        if (isset($this->url['params']['2'])) {
            $this->method = $this->url['params']['2'];
        }
        $this->url['base_url'] = self::getBaseURL();
        $this->url['uri'] = implode('/', $this->url['params']);
        if (strpos($this->url['uri'],'page') !== false) {
            if (preg_match("/page([0-9]+)/", $this->url['uri'], $match)) {
                $this->url['page_num'] = (int)$match['1'];
            }
        }
        self::$instance = $this;
    }
    
    public static function &getInstance()
    {
        if (self::$instance === null) {
              self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Short cut to object properties.
     * Example: $module = Router::getProperty('module'); // controller, method, url
     * @access public
     * @param string $prop module, controller, etc.
     * @return mixed null or property
     */
    public static function getProperty($prop)
    {
        $r = self::getInstance();
        
        return (isset($r->$prop) ? $r->$prop : null);
    }
    
    public static function getURI()
    {        
        return (
            self::getVar('PATH_INFO', 'server') ? 
            self::getVar('PATH_INFO', 'server') :
            '/'.DEFAULT_MODULE
        );
    }
    
    public static function getURL()
    {
        return self::getBaseURL().self::getURI();
    }
    
    public static function getBaseURL()
    {
        $self = self::getVar('PHP_SELF', 'server');
        $server = self::getVar('HTTP_HOST', 'server');
        $server .= rtrim(str_replace(strstr($self, 'index.php'), '', $self), '/');
        $https = self::getVar('HTTPS', 'server');
        $pre = 'http://';
        if ($https == 'on') {
            $pre = 'https://';
        }
        
        return $pre.$server;
    }
    
    /**
     * Get values by period delimited indexes from arrays
     * @access public
     * @param string $type get,globals,post,input,etc
     * @param string $index period delimited index string
     * @param array $input user input array
     * @return mixed array value or false on failure
     */
    public static function getVar($var, $array, $default = null)
    {    
        switch($array){
            case 'post': $array = $_POST; break;
            case 'cookie': $array = $_COOKIE; break;
            case 'files': $array = $_FILES; break;
            case 'get': $array = $_GET; break;
            case 'globals': $array = $GLOBALS; break;
            case 'post': $array = $_POST; break;
            case 'request': $array = $_REQUEST; break;
            case 'server': $array = $_SERVER; break;
            case 'session': $array = $_SESSION; break;
        }
        
        if (isset($array[$var])) {
            return $array[$var];
        }
        
        return (is_null($default) ? null : $default);
    }
    
    /**
     * Safer Redirect
     * @access public
     * @param string $redirecturl where to go
     * @param string $status http status, default 302
     * @return bool whether the string was a string
     */
    public static function redirect($url = '/', $status = null)
    {
        $url = str_replace(array('\r','\n','%0d','%0a'), '', $url);
    
        if (headers_sent()) {
            return false;
        }
    
        // trap session vars before redirect
        session_write_close();
    
        if(is_null($status)){
            $status = '302';
        }
        
        // push a status to the browser if necessary
        if ((int)$status > 0) {
            switch($status){
                case '301': $msg = '301 Moved Permanently'; break;
                case '307': $msg = '307 Temporary Redirect'; break;
                case '401': $msg = '401 Access Denied'; break;
                case '403': $msg = '403 Request Forbidden'; break;
                case '404': $msg = '404 Not Found'; break;
                case '405': $msg = '405 Method Not Allowed'; break;
                case '302':
                default: $msg = '302 Found'; break; // temp redirect
            }
            if (isset($msg)) {
                header('HTTP/1.1 '.$msg);
            }
        }
        if (preg_match('/^https?/', $url)) {
            header("Location: $url");
            exit;
        }
        // strip leading slashies
        $url = preg_replace('!^/*!', '', $url);
        header("Location: ".self::getBaseURL().'/'.$url);
        exit;
    }
}