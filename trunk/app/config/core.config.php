<?php

$core = array();
$core['site_name'] = 'APE HMVC Framework';

$core['encryption_salt'] = 'xxxxxxxxxxxxx';


// load config in loader class, check these, if anything, load em.
$core['autoload']['helpers'] = array(
 /* 'common' loads: /helpers/common.helper.php */
);
$core['autoload']['libraries'] = array(
    /*array('test' => array('foo'=>'bar', 'faz' => 'bax')) loads: libraries/test.php */
);
$core['autoload']['plugins'] = array();

// hooks and autoload

$root = 'http://'.$_SERVER['HTTP_HOST'];
$root .= str_replace(basename($_SERVER['SCRIPT_NAME']),'',$_SERVER['SCRIPT_NAME']);
$core['base_url'] = $root;
