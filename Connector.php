<?php

class Connector
{
    private $dbConfig;
    private $instance = null ;
    private $visitedLinks = array() ;
    const MaxVisitedLinksSize = 2000 ;
    // when an array is this big it is added to the db then is empted 
    function __construct($dbConfig)
    {
        $this->dbConfig = $dbConfig;
        $this->dbConfig = $dbConfig;
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
        $url_hash = crc32($url) ;
        $this->visitedLinks[$url_hash] = $url ;
        $sizeOfVisitedLinks = sizeof($this->visitedLinks) ;

        if( $sizeOfVisitedLinks == $this->maxVisitedLinksSize)
        {
            $this->commitVistedArrayToDb() ;
        }
    }

    public function checkIfUrlExists($url)
    {
        $instance = $this->getInstance() ;
        $url_hash = crc32($url) ;
        $foundFlag ;

        if(array_key_exists($url_hash,$this->visitedLinks)){
            $foundFlag =  true ;
        }else{
            //$foundFlag =  false ;
            $sql = "select id from  {$this->dbConfig['table']} where `link_hash` = {$url_hash};" ;
            $result = $instance->query($sql) ;

            if ($result === false) {
                echo "\nError: \n" . $sql . "\n" . $instance->error;
                exit() ;
            } else {
                if ($result->num_rows == 0) {
                    $foundFlag = false ;
                } else {
                    $foundFlag = true ;
                }
            }
        }

        return $foundFlag ;

    }

    private function commitVistedArrayToDb(){
        $instance = $this->getInstance() ;
        $sql = "INSERT INTO {$this->dbConfig['table']} (`link_hash`, `link_value`)  VALUES ";
        foreach ($this->visitedLinks as $key => $value)
        {
            $sql .= "(
                        '{$key}',
                        '{$value}'
                    ),";
        }
        $sql = substr($sql, 0, -1) ;
        if ($instance->query($sql) === false) 
        {
            echo "\nError: \n" . $sql . "\n" . $instance->error;
            exit() ;
        }
        $this->visitedLinks = array() ;
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
