<?php 
error_reporting(E_ERROR | E_PARSE); 

include "Constants.php" ;
include "Connector.php" ;
include "Helper.php" ;

$targetUrl = Constants::TARGETURL ;
$connector = new Connector() ;
$helper = new Helper($connector) ;

echo "Crawling Started" ;
getLinksFromUrl($targetUrl , $helper ) ;
echo "Done" ;

function getLinksFromUrl($url , $helper ){

	$helper->addVisitedUrl($url) ;

	if(substr($url, -1) == '/' || substr($url, -1) == '#' ){
		$url = substr($url, 0 , -1 );
	}
	$parsedTargetUrl = parse_url($url) ;
	$rootUrl = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"] ;
	$rootDomain = str_replace("www.", "", $parsedTargetUrl["host"] ) ;
		
	$content = file_get_contents($url) ;
	$document = new DOMDocument;
	$document->loadHTML($content) ;
	$urls = $document->getElementsByTagName('a') ;

	$crawled = array() ;
	foreach ($urls as $tag) {
		$link = $tag->getAttribute('href') ;
		if(!isset($crawled[$link]) && $link !== $url){ // To prevent iterating the same link twice
			$crawled[$link] = '/' ; 

			if (substr($link, 0, 1) == "/" && substr($link, 0, 2) != "//") {
				$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"].$link;
			} else if (substr($link, 0, 2) == "//") {
				$link = $parsedTargetUrl["scheme"].":".$link;
			} else if (substr($link, 0, 2) == "./") {
				$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"].dirname($parsedTargetUrl["path"]).substr($link, 1);
			} else if (substr($link, 0, 1) == "#") {
				$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"].$parsedTargetUrl["path"].$link;
			} else if (substr($link, 0, 3) == "../") {
				$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"]."/".$link;
			} else if (substr($link, 0, 11) == "javascript:") {
				continue;
			} else if (substr($link, 0, 5) != "https" && substr($link, 0, 4) != "http") {
				$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"]."/".$link;
			}

			$parsedLink = parse_url($link) ;
			$domainName = $parsedLink["host"] ;
			$domainName = str_replace("www.", "", $domainName) ;
			if(strpos($domainName, $rootDomain) !== false && !$helper->checkIfUrlIsVisited($link)){
				getLinksFromUrl($link , $helper ) ;
			}
		}else{
			continue;
		}	
	}
	
}
