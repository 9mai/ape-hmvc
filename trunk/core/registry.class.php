<?php

Class Registry {

    private static $registry = array();
    
    public static function get($key, $default = null)
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
    
    public static function add($key, $value, $replace = true)
    {
        if (self::exists($key) AND $replace == false) {
            trigger_error($key.' already set. Use replace method.', E_USER_WARNING);
            return false;
        }

        self::$registry[$key] = $value;
        return true;
    }
    
    public static function addArray($arr, $replace = true)
    {
        if (is_array($arr)) {
            foreach ($arr as $k=>$v) {
                self::add($k, $v, $replace);
            }
        }
        
        return true;
    }
    
    public static function replace($key, $value)
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