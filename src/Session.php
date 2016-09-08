<?php
namespace Session;

use BadMethodCallException;

class Session
{

    private static $storage;

    private static $defaultSettings = [
        'cookie_secure'    => false,
        'cookie_httponly'  => true,
        'use_only_cookies' => true,
    ];

    public static function __callStatic($method, $params)
    {

        $helper = 'array_' . $method;

        if (!function_exists($helper)) {
            throw BadMethodCallException("not eixsts method : {$helper}");
        }

        return call_user_func_array($helper, array_unshift($params, static::storage));
    }

}
