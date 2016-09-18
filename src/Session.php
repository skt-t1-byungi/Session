<?php
namespace SktT1Byungi\Session;

use BadMethodCallException;
use RuntimeException;
use SktT1Byungi\Session\Manager;

class Session
{
    public static function __callStatic($method, $params)
    {
        if (!Manager::isStarted()) {
            throw new RuntimeException('session is not started.');
        }

        if ($method === 'collect') {
            return collect($_SESSION);
        }

        if (function_exists('array_' . $method)) {
            return call_user_func_array('array_' . $method, array_merge([ & $_SESSION], $params));
        }

        throw new BadMethodCallException("not exists {$method} method");
    }

}
