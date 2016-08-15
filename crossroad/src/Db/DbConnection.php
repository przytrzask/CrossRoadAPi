<?php

namespace Sda\Crossroad\Db;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;


/**
 * Class DbConnection
 * @package Sda\Trystar\Db
 */
class DbConnection
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * DbConnection constructor.
     * @param array $dbSettings
     * @throws DBALException
     */
    public function __construct(array $dbSettings)
    {
        $this->conn = DriverManager::getConnection($dbSettings, new Configuration());
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->conn;
    }
}