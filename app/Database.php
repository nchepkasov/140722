<?php

namespace App;

class Database
{
    private static $instance;

    private $conn;

    private function __construct ()
    {
        $this->conn = \Doctrine\DBAL\DriverManager::getConnection (
            array (
                'dbname'    => DB_NAME,
                'user'      => DB_USER,
                'password'  => DB_PASS,
                'host'      => DB_HOST,
                'driver'    => 'pdo_mysql',
            ), 
            new \Doctrine\DBAL\Configuration ()
        );        
    }

    public static function getConn ()
    {
        if (self::$instance == null)
        {
            $className = __CLASS__;

            self::$instance = new $className ();
        }

        return self::$instance->conn;
    }
}

