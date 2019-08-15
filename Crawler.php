<?php

error_reporting(E_ERROR | E_PARSE);

include "Helper.php";
include 'ConfigurationReader.php';
include 'Agent.php';

$config = new ConfigurationReader();
$helper = new Helper($config);
$agent = new Agent($helper);

echo "Crawling Started";
$time_start = microtime(true); 

$agent->getLinksFromTargetUrl() ;

$time_end = microtime(true);

$execution_time = ($time_end - $time_start)/60;

echo "\r\n Total Excution Time : {$execution_time}";
echo "Done";




