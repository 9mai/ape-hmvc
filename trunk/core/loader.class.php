<?php

Abstract Class loadInterface {
    abstract public static function loadLayout($layout, $data=array());
    abstract public static function loadView($module, $view, $data=array());
    abstract public static function loadModel($module, $model, $args=array());
    abstract public static function loadConfig($file=null, $item=null);
    abstract public static function loadFile($file,$require=true);
    abstract public static function loadError($type='general', $data=array());
    abstract public static function getOutput($file, $data=array());
}

Abstract Class Loader Extends loadInterface {

    public static function loadLayout($data = array(), $layout = 'default')
    {
        $file = APP_PATH.DS.'layouts'.DS.strtolower($layout).'.layout.php';
        if (file_exists($file)) {
            return self::getOutput($file, $data);
        }
        
        trigger_error('Layout: "'.$layout.'" could not be found.', E_USER_ERROR);
        return false;
    }
    
    public static function loadController($module, $controller, $args=array())
    {
        $class = ucfirst(strtolower($module)).'_'.ucfirst(strtolower($controller)).'_Controller';
        if (class_exists($class)) {
            return new $class($args);
        }
        
        $file = APP_PATH.DS.'modules'.DS.strtolower($module).DS.'controllers'
                .DS.strtolower($controller).'.controller.php'; 
        if (file_exists($file)) {
            self::loadFile($file);
            return new $class($args);
        }
        
        return new stdClass();
    }
    
    public static function loadLibrary($library, $args=array(), $return = true)
    {
        if (class_exists($library)) {
            $obj = new $library($args);
            if($return){
                return $obj;
            }
        }
        
        $file = APP_PATH.DS.'libraries'.DS.$library.'.php'; 
        if (file_exists($file)) {
            self::loadFile($file);
            $obj = new $library($args);
            if($return){
                return $obj;
            }
        }
        
        return new stdClass();
    }
    
    public static function loadView($module, $view, $data=array())
    {
        $file = APP_PATH.DS.'layouts'.DS.'views'.DS.strtolower($module).DS.strtolower($view).'.view.php';
        if (file_exists($file)) {
            return self::getOutput($file, $data);
        }
        
        $file = APP_PATH.DS.'modules'.DS.strtolower($module).DS.'views'.DS.strtolower($view).'.view.php';
        if (file_exists($file)) {
            return self::getOutput($file, $data);
        }
        
        trigger_error('View: "'.$view.'" could not be found.', E_USER_WARNING);
        return false;
    }
    
    public static function loadModel($module, $model, $args=array())
    {
        static $loaded = array();
        
        $model_name = ucfirst(strtolower($module)).'_'.ucfirst(strtolower($model)).'_Model';
        
        if (isset($loaded[$model_name])) {
            return $loaded[$model_name];
        }
        
        $file = APP_PATH.DS.'modules'.DS.strtolower($module).DS.'models'.DS.strtolower($model).'.model.php';
        if (file_exists($file) AND self::loadFile($file)) {
            try {
                $loaded[$model_name] = new $model_name($args);
            } catch(Exception $e) {
                trigger_error('Failed to load model: '.$model_name.' - '.$e->getMessage(), E_USER_ERROR);
                return false;
            }
            return $loaded[$model_name];
        } else {
            trigger_error($module.'_'.$model.' could not be found/loaded.', E_USER_ERROR);
            return false;
        }
    }
    
    public static function loadHelper($helper)
    {
        return self::loadFile(APP_PATH.DS.'helpers'.DS.strtolower($helper).'.helper.php');
    }
    
    public static function loadPlugin($plugin)
    {
        return self::loadFile(APP_PATH.DS.'plugins'.DS.strtolower($plugin).'.plugin.php');
    }
    
    public static function loadConfig($file=null, $item=null)
    {
        static $config;
        
        if (isset($config[$file])) {
            return (isset($config[$file][$item]) ? $config[$file][$item] : $config[$file]);
        }
    
        if (!is_null($file) AND file_exists(APP_PATH.DS.'config'.DS.$file.'.config.php')) {
            include(APP_PATH.DS.'config'.DS.$file.'.config.php');
            if (is_array($$file)) {
                $config[$file] = $$file;
                if (!is_null($item) AND !isset($config[$file][$item])) {
                    trigger_error($item.' could not be found in '.$file, E_USER_ERROR);
                    return false;
                }
                return (isset($config[$file][$item]) ? $config[$file][$item] : $config[$file]);
            }
        } elseif (is_null($file)) {
            return $config;
        }
        
        return array();
    }
    
    public static function loadFile($file, $require=true)
    {    
        static $loaded = array();
        
        if (isset($loaded[$file])) {
            return $loaded[$file];
        }
    
        $loaded[$file] = ($require ? require($file) : include($file));

        return $loaded[$file];
    }
    
    public static function loadError($type='general', $data=array())
    {
        if ((int)$type > 0) {
            switch($type) {
                case '401': $msg = '401 Access Denied'; break;
                case '404': $msg = '404 Not Found'; break;
                case '500': $msg = '500 Internal Server Error'; break;
            }
            if (isset($msg)) {
                header('HTTP/1.1 '.$msg);
                header('Status: '.$msg);
            }
        }
        $content = self::loadView('error', $type, $data);
        $data = array(
            'type' => $type,
            'type_msg' => $msg,
            'content' => $content,
            'data' => $data
        );
        return self::loadLayout($data, 'error');
    }
    
    public static function getOutput($file, $data=array())
    {
        if(!file_exists($file)) {
            return null;
        }   
        
        ob_start();
        if (is_array($data)) {
            extract($data); // I don't like this. could produce undeclared errors. set in registry?
        }
        include($file);
        $return = ob_get_contents();
        ob_end_clean();
        
        return $return;
    }
}