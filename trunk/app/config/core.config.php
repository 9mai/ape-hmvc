<?php

$core = array();
$core['site_name'] = 'Sitename from core.config.php';

$core['encryption_salt'] = '8675309';


// load confgd in loader class, check these, if anything, load em.
$core['autoload']['helpers'] = array();
$core['autoload']['libraries'] = array(
    /*array('test' => array('foo'=>'bar', 'faz' => 'bax'))*/
);
$core['autoload']['plugins'] = array();

// hooks and autoload