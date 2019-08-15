<?php 

class ConfigurationReader
{
	private $config = null ;

	function __construct()
    {
        $config = require_once  __DIR__ . '/Config.php';
        $this->config = $config;
    }

    public function getConfig($configName)
    {
    	return $this->config[$configName] ;
    }

}