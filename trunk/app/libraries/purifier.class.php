<?php

/**
 * Purifier
 * Purifies input of nastiness and corrects broken HTML
 */
 
/*
    EXAMPLE:
    $var = '<a href="http://sitename.com">link</a> <i>italics</i>';
    $p = new Purifier;
    $noHTML = $p->purify($var); // result: link italics
    $allowStandardHTML = $p->purify($var,true); // result: <a href="http://sitename.com">link</a> <i>italics</i>
    
    // limit HTML to italics
    $p->reset();
    $p->setAttributes('i');
    // optionally: $p->reset()->setAttributes('i');
    $allowItalics = $p->purify($var,true); // result: link <i>italics</i>
    
    require('Purifier.php');
    $var = array('stuff'=>'<a href="http://sitename.com">link</a> <i>italics</i>');
    $p = new Purifier;
    $p->setConfig(
           array('HTML.Allowed'=>'a[href]')
    );
    $noHTML = $p->purifyarray($var); // result: link italics
    $allowStandardHTML = $p->purifyArray($var,true); // result: <a href="http://sitename.com">link</a> italics
    
    print('<xmp>'.print_r($noHTML,true).'</xmp>');
    die('<xmp>'.print_r($allowStandardHTML,true).'</xmp>');
    
*/

Class Purifier {

    private $attributes = 'b,strong,a[href],i,em,ul,ol,li';
    private $iteration = 0;
    private $config = array();
    const   version = '4.2.0';
    
    /**
     * __Construct
     * Loads Purifier Library
     * @access public
     */
    public function __Construct()
    { 
        static $loaded = false;
        
        // file hasn't been included
        if(!$loaded AND !class_exists('HTMLPurifier')){
            require(dirname(__FILE__) . '/Purifier_'.self::version.'/HTMLPurifier.auto.php');
            $loaded = true;
        }
        
    }
    
    /**
     * __Destruct
     */
    public function __Destruct()
    { 
        unset($this);        
    }
    
    /**
     * Reset config and attributes
     * ie: if you need to specify multiple html attributes or change config options
     * each iteration can only deal with 1 html instance and 1 non-html instance
     * @access public
     * @return none
     */
    public function reset()
    {
        $this->iteration++;
        return $this;
    }
    
    /**
     * Purify text from bad/broken user input
     * @access public
     * @param string $var variable to be cleansed
     * @param bool $allowHTML whether html is allowed
     * @return string the clean string
     */
    public function purify($var, $allowHTML=false)
    {
        static $purifier = array();

        if (is_array($var)) {
            return $this->purifyArray($var, $allowHTML);
        }
        
        if (!isset($var['0'])) {
            return $var;
        }
        
        // cache config and object(s)
        // if you need to change attributes/config for html input, call reset method
        if (!isset($this->purifier[$this->iteration][(int)$allowHTML])) {
            $this->purifier[$this->iteration][(int)$allowHTML] = new HTMLPurifier($this->getConfig((bool)$allowHTML));
        }
        
        $var = $this->purifier[$this->iteration][(int)$allowHTML]->purify($var);
        
        // just in case
        if (!$allowHTML) {
            $var = strip_tags($var);
        }
        
        $var = HTMLPurifier_Encoder::cleanUTF8($var);
        
        return self::htmlDecode($var);
    }
    
    /**
     * Clean all values in an array ($_GET/$_POST)
     * @access public
     * @param array $array to be cleaned
     * @return array clean array
     */
    public function purifyArray($array, $allowHTML=false)
    {        
        if(!is_array($array) OR !$array){
            return $this->purify($array,(bool)$allowHTML);
        }
        
        foreach($array as $k=>$v){
            if(is_array($v)){
                $array[$k] = $this->purifyArray($array[$k],(bool)$allowHTML);
            } else {
                $array[$k] = $this->purify($v,(bool)$allowHTML);
            }
        }
        
        return $array;
    }
    
    /**
    *   $obj->setConfig(
    *       array('Core.Encoding'=>UTF-8')
    *   );
    */
    public function setConfig($conf=array())
    {
        if(is_array($conf)){
            $this->config = $conf;
        }
    }
    
    /**
     * Retrieves configuration values for purify method
     * @access private
     * @param bool $allowHTML whether html is allowed
     * @return array configuration directives
     */
    private function getConfig($allowHTML)
    {
        static $cache_path = false;
        
        if (!$cache_path) {
            $path = APP_PATH.'/cache/purifier';
            if (is_dir($path) and is_writable($path)) {
                $cache_path = $path;
                unset($folder,$path);
            } else {
                $cache_path =  dirname(__FILE__).'/Purifier_'.self::version.'/HTMLPurifier/DefinitionCache/Serializer';
            }
        }
        
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $config->set('AutoFormat.AutoParagraph', false); // over-ride using setConfig
        $config->set('Cache.SerializerPath', $cache_path);
        $config->set('URI.DisableExternalResources', true);
        if ($allowHTML === true) {
            $config->set('HTML.Allowed', $this->getAttributes());
        } else {
            $config->set('HTML.Allowed', '');
        }
        if ($this->config) {
            foreach ($this->config as $key=>$val) {
                $config->set($key,$val);
            }
        }
        return $config;
    }
    
    /**
    *   Could also use:
    *   $obj->setConfig(
    *       array('HTML.Allowed'=>'a[href]')
    *   );
    */
	public function setAttributes($attr)
	{
	    if(is_array($attr)){
	        $this->attributes = implode(',',$attr);
	    } elseif(is_string($attr)){
	        $this->attributes = $attr;
	    }
	    
	    return true;
	}
	
	/**
	 * Get attributes
	 * @access public
	 * @return string current attributes
	 */
	public function getAttributes()
	{
	    return $this->attributes;
	}
	
	/**
	 * Encode entities
	 * @access public
	 * @param string $var string to encode
	 * @return string encoded string
	 */
	public static function htmlEncode($var)
	{
	    return trim(htmlentities($var, ENT_QUOTES, 'UTF-8'));
    }

    /**
	 * Dencode entities
	 * @access public
	 * @param string $var string to decode
	 * @return string decoded string
	 */
    public static function htmlDecode($var)
	{
	    return trim(html_entity_decode($var, ENT_QUOTES, 'UTF-8'));
    }
    
}