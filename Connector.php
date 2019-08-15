<?php

class Connector
{
    private $dbConfig;
    private $instance = null ;

    function __construct()
    {
        $config = require_once  __DIR__ . '/config.php';
        $this->dbConfig = $config['database'];
    }

    private function getInstance()
    {
        if ($this->instance == null) {
            $this->initEnvironment() ;
            $this->instance = new mysqli(
                $this->dbConfig['serverName'],
                $this->dbConfig['username'],
                $this->dbConfig['password'],
                $this->dbConfig['dbName']
            );
            if ($this->instance->connect_error) {
                die("Connection failed: " . $this->instance->connect_error);
            }
            return $this->instance ;
        } else {
            return $this->instance ;
        }
    }

    public function addNewUrl($url)
    {
        $instance = $this->getInstance() ;
        $url_hash = crc32($url) ;

        $sql = "INSERT INTO {$this->dbConfig['table']} (`link_hash`, `link_value`) VALUES ({$url_hash}, '{$url}');" ;
        if ($instance->query($sql) === false) {
            echo "\nError: \n" . $sql . "\n" . $instance->error;
            exit() ;
        }
    }

    public function checkIfUrlExists($url)
    {
        $instance = $this->getInstance() ;
        $url_hash = crc32($url) ;

        $sql = "select id from  {$this->dbConfig['table']} where `link_hash` = {$url_hash};" ;
        $result = $instance->query($sql) ;

        if ($result === false) {
            echo "\nError: \n" . $sql . "\n" . $instance->error;
            exit() ;
        } else {
            if ($result->num_rows == 0) {
                return false ;
            } else {
                return true ;
            }
        }
    }

    public function initEnvironment()
    {
        $tmp_conn = new mysqli($this->dbConfig['serverName'], $this->dbConfig['username'], $this->dbConfig['password']);
        if ($tmp_conn->connect_error) {
            die("Connection failed: " . $tmp_conn->connect_error);
        }

        $databaseCreationQuery= "CREATE DATABASE IF NOT EXISTS {$this->dbConfig['dbName']}; " ;
        $databaseCreationResult = $tmp_conn->query($databaseCreationQuery) ;
        if ($databaseCreationResult === false) {
            echo "\nError: \n" . $databaseCreationResult;
            exit() ;
        }

        $tableCreationQuery = "CREATE TABLE IF NOT EXISTS {$this->dbConfig['dbName']}.{$this->dbConfig['table']}  (
					 `id` int(20) NOT NULL AUTO_INCREMENT,
					 `link_hash` bigint(200) NOT NULL,
					 `link_value` varchar(255) NOT NULL,
					 PRIMARY KEY (`id`),
					 UNIQUE KEY `link_hash_2` (`link_hash`),
					 KEY `link_hash` (`link_hash`)
					) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1
					;" ;
        $tableCreationResult = $tmp_conn->query($tableCreationQuery) ;
        if ($tableCreationResult === false) {
            echo "\nError: \n" . $tableCreationResult;
            exit() ;
        }
        $tmp_conn->close();
    }
}
