<?php
namespace Agora\Core\Database;

use Agora\Core\Interfaces\IDatabase;
use \PDO;
use \PDOException;
use \Exception;

class MySQL implements IDatabase
{
    private $host;
    private $dbUser;
    private $dbPass;
    private $dbName;
    private $dbConn;
    private $dbconnectError;

    public function __construct($host, $dbUser, $dbPass, $dbName)
    {
        $this->host = $host;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->dbName = $dbName;
        $this->connectToServer();
    }

    public function connectToServer()
    {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbName . ";charset=utf8mb4";
            $this->dbConn = new PDO($dsn, $this->dbUser, $this->dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            $this->dbconnectError = false;
        } catch (PDOException $e) {
            $this->dbconnectError = true;
            throw new Exception('Could not connect to database: ' . $e->getMessage());
        }
    }

    public function prepare($sql)
    {
        try {
            return $this->dbConn->prepare($sql);
        } catch (PDOException $e) {
            throw new Exception('Prepare statement failed: ' . $e->getMessage());
        }
    }

    public function query($sql)
    {
        try {
            $stmt = $this->dbConn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }

    public function execute($sql)
    {
        try {
            return $this->dbConn->exec($sql);
        } catch (PDOException $e) {
            throw new Exception('Execute failed: ' . $e->getMessage());
        }
    }

    public function lastInsertId()
    {
        return $this->dbConn->lastInsertId();
    }

    public function isError()
    {
        return $this->dbconnectError;
    }

    public function selectDatabase()
    {
        return true;
    }
}