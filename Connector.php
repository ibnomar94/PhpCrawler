<?php 

class Connector {
	private $servername ;
	private $dbname ;
	private $username  ;
	private $password ;
	private $table ;
	private $instance = null ;


	function __construct(){
		$this->servername = Constants::SERVERNAME ;
		$this->dbname = Constants::DBNAME ;
		$this->username =Constants::USERNAME ;
		$this->password = Constants::PASSWORD;
		$this->table = Constants::TABLE;
	}
	private function getInstance(){
		if($this->instance == null){
			$this->initEnvironment() ;
			$this->instance = new mysqli($this->servername, $this->username, $this->password , $this->dbname);
			if ($this->instance->connect_error) {
			    die("Connection failed: " . $this->instance->connect_error);
			} 
			return $this->instance ;
		}else{
			return $this->instance ;
		}
	}

	public function addNewUrl($url){
		$instance = $this->getInstance() ;
		$url_hash = crc32($url) ;

		$sql = "INSERT INTO {$this->table} (`link_hash`, `link_value`) VALUES ({$url_hash}, '{$url}');" ;
		if ($instance->query($sql) === FALSE) {
		    echo "\nError: \n" . $sql . "\n" . $instance->error;
		    exit() ;
		}
	}

	public function checkIfUrlExists($url){
		$instance = $this->getInstance() ;
		$url_hash = crc32($url) ;

		$sql = "select id from  {$this->table} where `link_hash` = {$url_hash};" ;
		$result = $instance->query($sql) ;

		if ( $result === FALSE) {
		    echo "\nError: \n" . $sql . "\n" . $instance->error;
		    exit() ;
		}else{
			if($result->num_rows == 0 ){
				return FALSE ;
			}else{
				return TRUE ;
			}
		}
	}

	public function initEnvironment(){
		$tmp_conn = new mysqli($this->servername, $this->username, $this->password );
		if ($tmp_conn->connect_error) {
		    die("Connection failed: " . $this->tmp_conn->connect_error);
		} 

		$sql = "CREATE DATABASE IF NOT EXISTS {$this->dbname}; " ;
		$result = $tmp_conn->query($sql) ;
		if ($result === FALSE){
		    echo "\nError: \n" . $sql . "\n" . $result->error;
		    exit() ;
		}

		$sql2 = "CREATE TABLE IF NOT EXISTS {$this->dbname}.{$this->table}  (
					 `id` int(20) NOT NULL AUTO_INCREMENT,
					 `link_hash` bigint(200) NOT NULL,
					 `link_value` varchar(255) NOT NULL,
					 PRIMARY KEY (`id`),
					 UNIQUE KEY `link_hash_2` (`link_hash`),
					 KEY `link_hash` (`link_hash`)
					) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1
					;" ;
		$result2 = $tmp_conn->query($sql2) ;
		if ($result2 === FALSE){
		    echo "\nError: \n" . $sql . "\n" . $result2->error;
		    exit() ;
		}

		$tmp_conn->close();

	}
}