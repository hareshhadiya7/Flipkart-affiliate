<?php
/*
Plugin Name: Flipkart Affiliate link converter 
Plugin URI: https://github.com/hareshhadiya7/Flipkart-affiliate
Description: Add your Flipkart Affiliate affid to all flipkart URLs
Version: 1.0
Author: Haresh Hadiya
Author URI: https://freeshoppingdeal.com/

*/

yourls_add_action('pre_redirect', 'flo_flipkartAffiliate');

function flo_flipkartAffiliate($args) {
	// insert your personal settings here
	$affid = 'Your Affid';
	
	$campaign = 'Camp Name';
	
	// get url from arguments; create dictionary with all regex patterns and their respective affiliate affid as key/value pairs
	$url = $args[0];
	$patternaffidPairs = array(
		'/^http(s)?:\\/\\/(www\\.)?flipkart.com+/ui' => $affid,
		
	);
	
	// check if URL is a supported Flipkart URL
	foreach ($patternaffidPairs as $pattern => $affid) {
		if (preg_match($pattern, $url) == true) {
			// matched URL, now modify URL
			$url = cleanUpURL($url);
			$url = addaffidToURL($url, $affid);
			$url = addCampaignToURL($url, $campaign);

			// redirect
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $url");
			
			// now die so the normal flow of event is interrupted
			die();
		}
	}
}

function cleanUpURL($url) {
	// check if last char is an "/" (in case it is, remove it)
	if (substr($url, -1) == "/") {
		$url = substr($url, 0, -1);
	}
	
	// remove existing affiliate affid if needed
	$existingaffid;
	if (preg_match('/affid=.+&?/ui', $url, $matches) == true) {
		$existingaffid = $matches[0]; 
	}
	if ($existingaffid) {
		$url = str_replace($existingaffid, "", $url);
	}
	
	// remove existing campaign if needed
	$existingCampagin;
	if (preg_match('/Source=.+&?/ui', $url, $matches) == true) {
		$existingCampagin = $matches[0]; 
	}
	if ($existingCampagin) {
		$url = str_replace($existingCampagin, "", $url);
	}
	
	return $url;
}

function addaffidToURL($url, $affid) {
	// add our affid to the URL
	if (strpos($url, '?') !== false) { 
		// there's already a query string in our URL, so add our affid with "&"
		// add affid depending on if we need to add a "&" or not
		if (substr($url, -1) == "&") {
			$url = $url.'affid='.$affid;
		} else {
			$url = $url.'&affid='.$affid;
		}
	} else { // start a new query string
		$url = $url.'?affid='.$affid;
	}

	return $url;
}

function addCampaignToURL($url, $campaign) {
	if (empty($campaign)) {
		return $url;
	}
	$url = $url.'&Source='.$campaign;
	
	return $url;
}

?>
