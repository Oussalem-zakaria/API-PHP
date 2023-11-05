<?php

namespace App\DataBase;

use Exception;
use mysqli;
use PDO;
use PDOException;

class DataBase
{
    private $hostName = "localhost";
    private $userName = "root";
    private $password = "";
    private $dbName = "php_api_db";

    public function connect()
    {
        try {
            $pdo = new PDO("mysql:host=$this->hostName;dbname=$this->dbName", $this->userName, $this->password);
            $pdo->exec("SET NAMES utf8");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;

        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}
