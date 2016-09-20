<?php
namespace SktT1Byungi\Session;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use SktT1Byungi\Session\Manager;

class Session
{
    public static function __callStatic($method, $params)
    {
        if ($method == 'manager') {
            return Manager::getInstance();
        }

        if (!Manager::getInstance()->isStarted()) {
            throw new RuntimeException('session is not started.');
        }

        if ($method === 'collect') {

            if (empty($params[0])) {
                throw new InvalidArgumentException('required 1 argument.');
            }

            return new Collection(static::get($params[0]));
        }

        if (is_callable([Arr::class, $method])) {
            return call_user_func_array([Arr::class, $method], array_merge([ & $_SESSION], $params));
        }

        if ($method === 'remove') {

            if (empty($params[0])) {
                throw new InvalidArgumentException('required 1 argument.');
            }

            return static::forget($params[0]);
        }

        throw new BadMethodCallException("not exists {$method} method");
    }

}
