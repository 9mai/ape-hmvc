<?php

Abstract Class registryInterface {
    abstract public static function &get($key);
    abstract public static function getAll();
    abstract public static function add($key, &$value);
    abstract public static function replace($key, &$value);
    abstract public static function remove($index);
    abstract public static function clear();
    abstract public static function exists($key = null);
}

Abstract Class Registry extends registryInterface {

    private static $registry = array();
    
    public static function &get($key, $default = null)
    {
        $return = $default;
        if (self::exists($key)) {
            $return = self::$registry[$key];
        }
        
        return $return;
    }
    
    public static function getAll()
    {
        return self::$registry;
    }
    
    public static function add($key, &$value, $replace = true)
    {
        if (self::exists($key) AND $replace == false) {
            trigger_error($key.' already set. Use replace method.', E_USER_ERROR);
            return false;
        }

        self::$registry[$key] = $value;
        return true;
    }
    
    public static function replace($key, &$value)
    {
        self::$registry[$key] = $value;
        return true;
    }
    
    public static function remove($index)
    {
        if (!is_array($index) AND self::exists($index)) {
            unset(self::$registry[$index]);
        }
        
        return true;
    }
    
    public static function clear()
    {
        self::$registry = array();
    }
    
    public static function exists($key = null)
    {
        return isset(self::$registry[$key]);
    }
    
}