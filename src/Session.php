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
    private static $aliasHelperNames = [
        'remove' => 'forget',
    ];

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

        if (method_exists(Arr::class, $method)) {
            return call_user_func_array([Arr::class, $method], array_merge([ & $_SESSION], $params));
        }

        if (array_key_exists($method, static::$aliasHelperNames)) {
            return call_user_func_array([Arr::class, static::$aliasHelperNames[$method]], array_merge([ & $_SESSION], $params));
        }

        throw new BadMethodCallException("not exists {$method} method");
    }

}
