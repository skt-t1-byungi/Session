<?php
namespace SktT1Byungi\Session;

use BadMethodCallException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use SessionHandlerInterface;
use SktT1Byungi\Session\handler\File as FileHandler;

class Session
{
    private static $baseHandler = FileHandler::class;

    private static $instance = null;

    private static $defaultOptions = [
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'use_only_cookies' => true,
    ];

    private $handler;

    private $name;

    private $id;

    private $options = [];

    private function __cosntruct()
    {
        if (static::$instance !== null) {
            throw LogicException("sesssion is singleton, can`t create instance more.");
        }
    }

    public function __callStatic($method, $params)
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        if (method_exists(static::$instance, $method)) {
            return call_user_func_array([static::$instance, $method], $params);
        }

        if (static::$instance->isStarted() && function_exists($helper = "array_{$method}")) {
            return call_user_func_array($helper, array_unshift($params, static::$instance->getStorage()));
        }

        throw BadMethodCallException("not eixsts method : {$method}");
    }

    public function start(SessionHandlerInterface $handler = null, $options = [])
    {
        if ($this->isStarted()) {
            throw RuntimeException("session is already started.");
        }

        if ($handler) {
            $this->setHandler($handler);
        }

        $this->setOptions($options);

        foreach ($this->options as $key => $value) {
            ini_set(sprintf("session.%s", $key), $value);
        }

        if ($this->id) {
            session_id($this->id);
        }

        if ($this->name) {
            session_name($this->name);
        }

        session_set_save_handler($this->getHandler());
        session_start();

        return $this;
    }

    public function isStarted()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function getStorage()
    {
        return $this->isStarted() ? $_SESSION : [];
    }

    public function setHandler(SessionHandlerInterface $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    public function getHandler()
    {
        if (!$this->handler) {
            $handlerClass = static::$baseHandler;
            $this->setHandler(new $handlerClass);
        }

        return $this->handler;
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge(static::$defaultOptions, $options);
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function close()
    {
        if ($this->isStarted()) {
            session_clse();
        }

        return $this;
    }

    public function middleware(SessionHandlerInterface $handler = null, $options = [])
    {
        $self = $this;

        return function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next
        ) use ($self, $handler, $options) {

            $self->start($handler, $options);

            return $next($request->withAttribute('session', $self->getStorage()), $response);
        };
    }
}
