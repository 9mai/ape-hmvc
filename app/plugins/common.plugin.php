<?php

function aCrypt($str, $decrypt=false, $salt = null)
{
    if (is_null($salt)) {
        $salt = '12345';
    }
    $iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);    
    $key_size = mcrypt_get_key_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
    $key = substr($salt, 0, $key_size);

    if ($decrypt) {
        $return = mcrypt_decrypt(MCRYPT_3DES, $key, base64_decode($str), MCRYPT_MODE_ECB, $iv);
    } else {
        $return = base64_encode(mcrypt_encrypt(MCRYPT_3DES, $key, $str, MCRYPT_MODE_ECB, $iv));
    }
    
    return $return;
}