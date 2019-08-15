<?php

include "Connector.php";

class Helper
{
    public $linkCounter = 0;
    private $connector;
    private $configurationReader ;

    function __construct($configurationReader)
    {
        $this->configurationReader = $configurationReader ;
        $this->connector = new Connector($configurationReader->getConfig("database"));
    }

    public function getConfigurationReader()
    {
        return $this->configurationReader ;
    }

    public function incrementValue()
    {
        $this->linkCounter++;
    }

    public function getCounterValue()
    {
        return $this->linkCounter;
    }

    public function addVisitedUrl($url)
    {
        $this->incrementValue();
        $this->printUrlValue($url) ;
        $this->connector->addNewUrl($url);
    }

    public function checkIfUrlIsVisited($url)
    {
        return  $this->connector->checkIfUrlExists($url);
    }

    public function printUrlValue($url)
    {
        echo "\r\n".$this->getCounterValue().' => '.$url;
    }
}
