<?php
namespace Agora\Core\Http;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $this->isKeySet($key) ? $_SESSION[$key] : null;
    }

    public function isKeySet($key)
    {
        return isset($_SESSION[$key]);
    }

    public function unsetKey($key)
    {
        if ($this->isKeySet($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy()
    {
        session_destroy();
    }
}