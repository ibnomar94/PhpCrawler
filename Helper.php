<?php

include "Connector.php";

class Helper
{
    public $linkCounter = 0;
    private $connector;

    function __construct()
    {
        $this->connector = new Connector();
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
        $this->connector->addNewUrl($url);
    }

    public function checkIfUrlIsVisited($url)
    {
        return  $this->connector->checkIfUrlExists($url);
    }

    public function printUrlValue($url)
    {
        echo $this->getCounterValue().' => '.$url;
        echo "\n";
    }
}
