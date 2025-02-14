<?php
namespace Agora\Core\Interfaces;

use Agora\Core\Http\URI;
use Agora\Core\Http\Session;
use Agora\Core\Config\Config;

interface IContext
{
    /**
     * Get the database instance
     * @return IDatabase
     */
    public function getDB(): IDatabase;

    /**
     * Get the URI handler instance
     * @return URI
     */
    public function getURI(): URI;

    /**
     * Get the configuration instance
     * @return Config
     */
    public function getConfig(): Config;

    /**
     * Get the session handler instance
     * @return Session
     */
    public function getSession(): Session;
}