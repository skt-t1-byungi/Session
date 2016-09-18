<?php
namespace SktT1Byungi\Session;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SktT1Byungi\Session\Manager;

class Middleware implements IteratorAggregate, ArrayAccess, Countable
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        Manager::start();

        $request->withAttribute('session', $this);

        return $next($request, $response);
    }

    public function getIterator()
    {
        return new ArrayIterator($_SESSION);
    }

    public function count()
    {
        return count($_SESSION);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $_SESSION[] = $value;
        } else {
            $_SESSION[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
    }
}
