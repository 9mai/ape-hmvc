<?php

Abstract Class controlInterface {
    abstract public function __construct();
    abstract public function __toString();
}

Class Controller Extends controlInterface {

    private $output = null;

    function __construct()
    {        
        // check if controller file exists
        $module = Router::getProperty('module');
        $controller = Router::getProperty('controller');
        $controller_file = APP_PATH.'/modules/'.$module
                      .'/controllers/'.$controller.'.controller.php';
        if (!file_exists($controller_file)) {
            $data = array(
                'message' => $module.'::'.$controller.' not found.'
            );
            return ($this->output = Loader::loadError('404', $data));
        }
        // require it.
        require($controller_file);

        // check that class and method exist
        $class = ucfirst($module).'_'
                .ucfirst($controller).'_'
                .'Controller';
        $method = Router::getProperty('method');
        
        if (!class_exists($class)) {
            $data = array(
                'message' => $class.' does not exist'
            );
            return ($this->output = Loader::loadError('404', $data));
        }
        
        $class_methods = array_map('strtolower', get_class_methods($class));        
        if (!in_array($method, $class_methods) AND !in_array('__call', $class_methods)) {
            $data = array(
                'message' => '"'.$method.'" not found in '.$class.'.'
            );
            return ($this->output = Loader::loadError('404', $data));
        }
        
        // create it, barf if necessary.
        try {
            $c = new $class();
        } catch(Exception $e){
            $data = array(
                'message' => $e->getMessage()
            );
            return ($this->output = Loader::loadError('general', $data));
        }
        
        // autoload helpers and such.
        $conf = Loader::loadConfig('core', 'autoload');
        if ($conf) {
            foreach ($conf as $key => $val) {
                if (!isset($val['0'])) { 
                    continue;
                }
                foreach($val as $k => $v) {
                    switch ($key) {
                        case 'helpers': Loader::loadHelper($v); break;
                        case 'plugins': Loader::LoadPlugin($v); break;
                        case 'libraries': 
                            foreach($v as $lib => $args){
                                Loader::loadLibrary($lib, $args); 
                            }
                        break;
                    }
                }
            }
        }
        
        // call method, pass url segments as arguments.
        $url = Router::getProperty('url');
        $params = array_slice($url['params'], 3);
        if ($params) {
            $this->output = call_user_func_array(
                array(&$c, $method), 
                $params
            );
        } else {
            $this->output = $c->$method();
        }
    }
    
    function __toString()
    {
        return $this->output;
    }
}