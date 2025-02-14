<?php
namespace Agora\Core;

use Agora\Core\Http\URI;
use Agora\Core\Http\Session;
use Agora\Core\Config\Config;
use Agora\Core\Database\MySQL;
use Agora\Core\Interfaces\IContext;
use Agora\Core\Interfaces\IDatabase;

class Context implements IContext
{
    private $config;
    private $uri;
    private $session;
    private $db;

    public function __construct($configFile)
    {
        // Initialize config
        $this->config = new Config($configFile);

        // Initialize URI handler
        $this->uri = new URI();

        // Initialize session
        $this->session = new Session();

        // Initialize database connection
        $dbConfig = $this->config->get('db');
        $this->db = new MySQL(
            $dbConfig['dbHost'],
            $dbConfig['dbUser'],
            $dbConfig['dbPassword'],
            $dbConfig['dbDatabase']
        );

        // Connect to database
        $this->db->connectToServer();
    }

    public function getDB(): IDatabase
    {
        return $this->db;
    }

    public function getURI(): URI
    {
        return $this->uri;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getSession(): Session
    {
        return $this->session;
    }
}