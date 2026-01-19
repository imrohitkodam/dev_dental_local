<?php
// namespace administrator\components\com_jmap\framework\seostats\services\google;
/**
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Google stats service
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @since 3.3
 */
class JMapSeostatsServicesGoogleSearch extends JMapSeostats {
	/**
	 * Store the number of curled SERP pages
	 *
	 * @access public
	 * @static
	 * @var string
	 */
	public static $numberIndexedPages;
	
	/**
	 * Store the number of curled SERP page
	 *
	 * @access public
	 * @static
	 * @var string
	 */
	public static $paginationNumber;
	
	/**
	 * Start the request for the SERP and the parsing of results
	 *
	 * @access protected
	 * @return boolean
	 */
	protected static function makeRequest($pageNumber, $query, $result, $customHeaders, $onlyIndexedCount = false) {
		require_once JPATH_ROOT . '/plugins/system/jmap/simplehtmldom.php';
		
		$ref = static::getReference ( $pageNumber, $query );
		$pageSerp = static::getPageSerp ( $pageNumber, $query );
		
		$curledSerp = static::gCurl ( $pageSerp, $ref, $customHeaders );
		if(!$curledSerp) {
			return false;
		}
		
		// Get total number of indexed pages
		preg_match ( '#<div id="resultStats">(.*?)</div>#', $curledSerp, $matchesTotalIndexedPages );
		static::$numberIndexedPages = $matchesTotalIndexedPages;
		static::$paginationNumber = $pageNumber;
		if($onlyIndexedCount) {
			return $matchesTotalIndexedPages;
		}
		
		$links = array();
		$titles = array();
		$descriptions = array();
		
		$simpleHtmlDomInstance = new JMapSimpleHtmlDom();
		$simpleHtmlDomInstance->load( $curledSerp );
		
		foreach ( $simpleHtmlDomInstance->find( 'div.g div.r > a' ) as $index=>$element ) {
			$links[$index] = html_entity_decode($element->getAttribute('href'), ENT_QUOTES, 'UTF-8');
		}
		foreach ( $simpleHtmlDomInstance->find( 'div.g div.r > a h3' ) as $index=>$element ) {
			$titles[$index] = html_entity_decode($element->innertext, ENT_QUOTES, 'UTF-8');
		}
		foreach ( $simpleHtmlDomInstance->find( 'div.g div.s span.st' ) as $index=>$element ) {
			$descriptions[$index] = html_entity_decode($element->innertext, ENT_QUOTES, 'UTF-8');
		}
		
		// Nothing found, try fallback
		if (empty ( $links )) {
			// Fallback attempt to old SERP
			foreach ( $simpleHtmlDomInstance->find( 'h3.r > a' ) as $index=>$element ) {
				$rawLink = html_entity_decode($element->getAttribute('href'), ENT_QUOTES, 'UTF-8');
				if(preg_match('/url=([^&]*)/i', $rawLink, $cleanLink)) {
					$links[$index] = $cleanLink[1];
				}
				$titles[$index] = html_entity_decode($element->innertext, ENT_QUOTES, 'UTF-8');
			}
			
			// Nothing found, return false
			if(empty($links)) {
				return false;
			}
		}
		
		// Parse SERP results
		static::parseResults ( $links, $titles, $descriptions, $pageNumber * 10, $result );
		
		return true;
	}
	
	/**
	 * Get the reference query string, ncr is for no country redirect
	 *
	 * @access protected
	 * @return string
	 */
	protected static function getReference($pageNumber, $query) {
		return 0 == $pageNumber ? 'ncr' : sprintf ( 'search?q=%s&hl=en&prmd=imvns&start=%s0&sa=N', $query, $pageNumber );
	}
	
	/**
	 * Filters the domain
	 *
	 * @access protected
	 * @return boolean
	 */
	protected static function getDomainFilter($domain) {
		return $domain ? "#^(https?://)?[^/]*{$domain}#i" : false;
	}
	
	/**
	 * Format the pagination for the Google query
	 *
	 * @access protected
	 * @return string
	 */
	protected static function getPageSerp($pageNumber, $query) {
		return 0 == $pageNumber ? sprintf ( 'search?q=%s&filter=0', $query ) : sprintf ( 'search?q=%s&filter=0&start=%s0', $query, $pageNumber );
	}

	/**
	 * Parse and format the array structure containing the SERP informations
	 *
	 * @access protected
	 * @return void
	 */
	protected static function parseResults($links, $titles, $descriptions, $pageNumber, $result) {
		$c = 0;
		$skipped = 0;
		foreach ( $links as $indexResult=>$link ) {
			$match = static::parseLink ( $link );
			if(!$match) {
				$skipped++;
				continue;
			}
			
			$title = null;
			if(isset($titles[$indexResult])) {
				$title = $titles[$indexResult];
			}
			
			$description = null;
			if(isset($descriptions[$indexResult])) {
				$description = $descriptions[$indexResult];
			}
			
			$c ++;
			$resCnt = $pageNumber + $c;
			$arrayPageIndex = ($pageNumber / 10) + 1;

			// Format results
			$result->setElement ( $arrayPageIndex, array (
					'url' => $link,
					'headline' => trim ( strip_tags ( $title ) ),
					'description' => $description
			) );
		}
	}
	
	/**
	 * Return the parsed links in the SERP
	 *
	 * @access protected
	 * @return string
	 */
	protected static function parseLink($link) {
		// Is valid and not webmaster link
		return self::isAGoogleWebmasterLink ( $link ) ? false : true;
	}
	
	/**
	 * Detect an invalid Google link
	 *
	 * @access protected
	 * @return boolean
	 */
	protected static function isAGoogleWebmasterLink($url) {
		return preg_match ( '#^https?://www.google.com/(?:intl/.+/)?webmasters#', $url );
	}
	
	/**
	 * Perform the remote query to Google through CURL
	 * 
	 * @access protected
	 * @return string
	 */
	protected static function gCurl($path, $ref, $customHeaders) {
		$url = sprintf ( 'https://www.google.%s/', (@$customHeaders['countrytld'] ? $customHeaders['countrytld'] : JMapSeostatsServices::GOOGLE_TLD));
		$referer = $ref == '' ? $url : ($ref != 'ncr' ? $url . $ref : $ref);
		$url .= $path;
		
		// Randomize the user agent to avoid Google ban
		$userAgents=array(
		        "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/62.0",
		        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/60.0",
			 	"Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/65.0",
				"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20130401 Firefox/64.0",
		        "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36",
		        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.2227.1 Safari/537.36",
			 	"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.1944.0 Safari/537.36",
			 	"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36",
		 		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A",
				"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.1944.0 Safari/537.36",
		 		"Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko",
		 		"Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko",
		 		"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)",
		 		"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)",
		 		"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)",
		 		"Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)");
	    $ua = $userAgents[rand (0, count($userAgents) - 1)];
	    
	    // Format the request header array
		$header = array (
				'Host: www.google.' . (@$customHeaders['countrytld'] ? $customHeaders['countrytld'] : JMapSeostatsServices::GOOGLE_TLD),
				'Connection: keep-alive',
				'Cache-Control: max-age=0',
				'User-Agent: ' . $ua,
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Referer: ' . $referer,
				'Accept-Language: ' . (@$customHeaders['acceptlanguage'] ? $customHeaders['acceptlanguage'] : JMapSeostatsServices::HTTP_HEADER_ACCEPT_LANGUAGE),
				'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' 
		);
		
		// Merge Cookie array if any
		if(isset($customHeaders['cookie'])) {
			array_push($header, 'Cookie: ' . $customHeaders['cookie']);
		}
		
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt ( $ch, CURLOPT_HEADER, true);
		if(!ini_get('open_basedir')) {
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		}
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $ch, CURLOPT_USERAGENT, $ua );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
		
		// Check for proxy settings
		$cParams = JComponentHelper::getParams('com_jmap');
		if ($cParams->get('enable_proxy', 0)) {
			$proxyServer = $cParams->get('proxy_server_ipaddress', '');
			$proxyPort = $cParams->get('proxy_server_port', '');
			$proxyUsername = $cParams->get('proxy_server_username', '');
			$proxyPassword = $cParams->get('proxy_server_password', '');
			if (!empty($proxyServer)) curl_setopt($ch, CURLOPT_PROXY, $proxyServer);
			if (!empty($proxyPort)) curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
			if (!empty($proxyUsername) && !empty($proxyPassword)) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUsername . ':' . $proxyPassword);
		}
		
		$result = curl_exec ( $ch );
		
		$info = curl_getinfo ( $ch );
		$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ( $ch );
		
		// If it's a redirection (3XX) follow the redirect
		if ($httpStatus >= 300 && $httpStatus < 400) {
			$headers = explode("\n", $result);
			// loop through the headers and check for a Location: str
			$j = count($headers);
			for($i = 0; $i < $j; $i++){
				// if we find the Location header strip it and fill the redir var
				if(strpos($headers[$i],"Location:") !== false){
					$redirectionLink = trim(str_replace("Location:","",$headers[$i]));
					$redirectURI = parse_url($redirectionLink);

					parse_str($redirectURI['query'], $queryArray);
					$safeQuery = http_build_query($queryArray);

					$redirectionURI = (trim($redirectURI['path'], '/') . '?' . $safeQuery);
					break;
				}
			}
			if($redirectionURI) {
				return static::gCurl($redirectionURI, $ref, $customHeaders );
			}
		}
		
		return ($info ['http_code'] != 200) ? false : $result;
	}
	
	/**
	 * Returns array, containing detailed results parsed and formatted for any Google search SERP
	 *
	 * @access public
	 * @param string $query The containing the search query.
	 * @param int $pageNumber The SERP page number requested
	 * @return array $customHeaders The custom headers for country and language to get SERP for
	 */
	public static function getSerps($query, $pageNumber = 0, $customHeaders = array()) {
		$q = rawurlencode ( $query );
		$result = new JMapSeostatsHelperArrayhandle ();
		
		static::makeRequest ( $pageNumber / 10, $q, $result, $customHeaders);
		return $result->toArray ();
	}
	
	/**
	 * Returns integer, the number of aestimated indexed links
	 *
	 * @access public
	 * @param string $query The containing the search query.
	 * @return array $customHeaders The custom headers for country and language to get SERP for
	 */
	public static function getSerpsIndexedLinks($query) {
		return static::makeRequest ( 0, $query, array(), array(), true);
	}
	
	/**
	 * Returns integer, the number of aestimated indexed links
	 *
	 * @access public
	 * @param string $query The containing the search query.
	 * @return int The number of the page where the keyword for a given domain is found
	 */
	public static function getRankedPageKeyword($query, $domain, $pageNumber = 0, $numResults = 100, $customHeaders = array()) {
		$query = rawurlencode ( $query );
		$ref = 0 == $pageNumber ? 'ncr' : sprintf ( 'search?q=%s&hl=en&prmd=imvns&start=%s&num=%s&sa=N', $query, $pageNumber, $numResults );
		$pageSerp = sprintf ( 'search?q=%s&filter=0&start=0&num=%s', $query, $numResults );
		
		$curledSerp = static::gCurl ( $pageSerp, $ref, $customHeaders );
		if(!$curledSerp) {
			return false;
		}
		
		$links = array();
		$simpleHtmlDomInstance = new JMapSimpleHtmlDom();
		$simpleHtmlDomInstance->load( $curledSerp );
		
		foreach ( $simpleHtmlDomInstance->find( 'div.g div.r > a' ) as $index=>$element ) {
			$links[$index] = html_entity_decode($element->getAttribute('href'), ENT_QUOTES, 'UTF-8');
		}
		
		// Nothing found, try fallback
		if (empty ( $links )) {
			// Fallback attempt to old SERP
			foreach ( $simpleHtmlDomInstance->find( 'h3.r > a' ) as $index=>$element ) {
				$rawLink = html_entity_decode($element->getAttribute('href'), ENT_QUOTES, 'UTF-8');
				if(preg_match('/url=([^&]*)/i', $rawLink, $cleanLink)) {
					$links[$index] = $cleanLink[1];
				}
			}
			
			// Nothing found, return false
			if(empty($links)) {
				return false;
			}
		}
		
		$numSerpResult = 0;
		$skipped = 0;
		$pageSerpIndex = null;
		foreach ( $links as $indexResult=>$link ) {
			// Found a match in a SERP for this domain?
			if(stripos($link, $domain) !== false) {
				$pageSerpIndex = intval($numSerpResult / 10) + 1;
				break;
			}

			$numSerpResult++;
		}
		
		return $pageSerpIndex;
	}
}