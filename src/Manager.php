<?php
namespace SktT1Byungi\Session;

use RuntimeException;
use SessionHandlerInterface;
use SktT1Byungi\Session\Handler\File as FileHandler;
use SktT1Byungi\Session\Middleware;

class Manager
{
    private static $DEFAULT_SETTINGS = [
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'use_only_cookies' => true,
    ];

    /**
     * @var self
     */
    private static $instance;

    /**
     * @var SessionHandlerInterface
     */
    private $handler;

    /**
     * @var array
     */
    private $settings = [];

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * @param  SessionHandlerInterface|null $handler
     * @throws RuntimeException
     */
    public function start(SessionHandlerInterface $handler = null)
    {
        if ($this->isStarted()) {
            throw new RuntimeException("session is already started.");
        }

        if ($handler) {
            $this->handler($handler);
        }

        $this->initSettings();
        $this->initHandler();
        session_start();

        return $this;
    }

    /**
     * @return self
     */
    public function close()
    {
        session_write_close();
        return $this;
    }

    /**
     * @return self
     */
    public function destroy()
    {
        if (!$this->isStarted()) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        return $this;
    }

    /**
     * @return boolean
     */
    public function isStarted()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    private function initSettings()
    {
        $settings = array_merge(static::$DEFAULT_SETTINGS, $this->settings);

        foreach ($settings as $key => $value) {
            ini_set(sprintf("session.%s", $key), $value);
        }
    }

    private function initHandler()
    {
        if (!$this->handler) {
            $this->handler = new FileHandler;
        }
        session_set_save_handler($this->handler, true);
    }

    /**
     * @param  array  $settings
     * @return self
     */
    public function settings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
        return $this;
    }

    /**
     * @param SessionHandlerInterface $handler
     */
    public function handler(SessionHandlerInterface $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @param  string $name
     * @return string|self
     */
    public function name($name = null)
    {
        if (is_null($name)) {
            return session_name();
        }
        session_name($name);
        return $this;
    }

    /**
     * @param string $id
     * @return string|self
     */
    public function id($id = null)
    {
        if (is_null($id)) {
            return session_id();
        }
        session_id($id);
        return $this;
    }

    /**
     * @param  SessionHandlerInterface|null $handler
     * @return SktT1Byungi\Session\Middleware
     */
    public function middleware(SessionHandlerInterface $handler = null)
    {
        if ($handler) {
            $this->handler($handler);
        }

        return new Middleware(clone $this);
    }

}
