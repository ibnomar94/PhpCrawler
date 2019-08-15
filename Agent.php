<?php 

class Agent
{
	private $helper ;

	function __construct($helper)
	{
		$this->helper = $helper ;
	}

	public function getLinksFromTargetUrl($targetUrl = null)
	{
		if($targetUrl == null){
			$targetUrl = $this->getTargetUrl() ;
		}

		$targetUrl = $this->cleanLinkValue($targetUrl) ;
		$this->helper->addVisitedUrl($targetUrl);

		$parsedTargetUrl = parse_url($targetUrl);
		$rootUrl = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"];
		$rootDomain = str_replace("www.", "", $parsedTargetUrl["host"]);

		$content = file_get_contents($targetUrl);
		$document = new DOMDocument;
		$document->loadHTML($content);
		$urls = $document->getElementsByTagName('a');

		$crawled = array($targetUrl);
		foreach ($urls as $tag) {
			$link = $tag->getAttribute('href');
			// To prevent iterating the same link twice
			if (!isset($crawled[$link]) && $link !== $targetUrl) {
				$crawled[$link] = '/';

				if (substr($link, 0, 1) == "/" && substr($link, 0, 2) != "//") {
					$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"].$link;
				} elseif (substr($link, 0, 2) == "//") {
					$link = $parsedTargetUrl["scheme"].":".$link;
				} elseif (substr($link, 0, 2) == "./") {
					$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"].dirname($parsedTargetUrl["path"]).substr($link, 1);
				} elseif (substr($link, 0, 1) == "#") {
					$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"].$parsedTargetUrl["path"].$link;
				} elseif (substr($link, 0, 3) == "../") {
					$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"]."/".$link;
				} elseif (substr($link, 0, 11) == "javascript:") {
					continue;
				} elseif (substr($link, 0, 5) != "https" && substr($link, 0, 4) != "http") {
					$link = $parsedTargetUrl["scheme"]."://".$parsedTargetUrl["host"]."/".$link;
				}

				$parsedLink = parse_url($link);
				$domainName = $parsedLink["host"];
				$domainName = str_replace("www.", "", $domainName);
				$link = $this->cleanLinkValue($link) ;

				if (strpos($domainName, $rootDomain) !== false && 
					!$this->checkIfUrlIsVisited($link))
				{
					$this->getLinksFromTargetUrl($link);
				}
			} else {
				continue;
			}

		}

	}

	private function getTargetUrl()
	{
		$configurationReader = $this->helper->getConfigurationReader() ;
		return $configurationReader->getConfig("targetUrl") ;
	}

	private function cleanLinkValue($link)
	{
		//echo "\r\n => Before : {$link}";
		if (substr(rtrim($link), -1) == "?") { 
		  $link = substr($link, 0, -1) ;
		}
		if (substr(rtrim($link), -1) == "#") { 
		  $link = substr($link, 0, -1) ;
		}
		if (substr(rtrim($link), -1) == "/") { 
		  $link = substr($link, 0, -1) ;
		}
		//echo "\r\n => After : {$link}";
		return $link ;
	}

	private function checkIfUrlIsVisited($link){
		return $this->helper->checkIfUrlIsVisited($link) ;
	}
}