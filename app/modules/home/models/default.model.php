<?php

Class Home_Default_Model {
    
    function __construct()
    {

    }
    
    // just for sake of example.
    function test($var=null, $array=array())
    {
        return $var.' array: '.print_r($array, true);
    }

}