<?php
namespace App\Helpers;

class NioHash
{
	public static function of($data=null, $match=null) {
		if(empty($data)) return false;
        $index  = 4;
        $csrf    = csrf_token();
        $ukey    = substr($csrf, -$index, $index);

        $openSSL = (function_exists('openssl_decrypt') && function_exists('openssl_encrypt')) ? true : false;
        if($openSSL===true) {
            $iv = substr(hash('sha256', $ukey), 0, 16);
            $hash = base64_encode(openssl_encrypt($data, 'aes-128-cbc', $ukey, 0, $iv));

            if(!empty($match)) {
                return ($match==$hash) ? true : false;
            }

            return $hash;
        }

        $encode = base64_encode($data.$ukey);

        if(!empty($match)) {
            return ($match==$encode) ? true : false;
        }

        return $encode;
    }

	public static function toID($data=null) {
        if(empty($data)) return false;
        $index  = 4;
        $csrf   = csrf_token();
        $ukey   = substr($csrf, -$index, $index);

        $openSSL = (function_exists('openssl_decrypt') && function_exists('openssl_encrypt')) ? true : false;
        if($openSSL===true) {
            $iv = substr(hash('sha256', $ukey), 0, 16);
            $hash = openssl_decrypt(base64_decode($data), 'aes-128-cbc', $ukey, 0, $iv);
            return ($hash) ? $hash : false;
        }
        return substr(base64_decode($data), 0, -$index);
	}
}
