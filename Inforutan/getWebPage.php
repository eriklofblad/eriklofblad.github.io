<?php

/* gets the data from a URL */

	//url to the site
    if(isset($_GET["site"]))
    {
        $site = $_GET["site"];
    }
	
	//Requires cURL to be installed on server
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $site);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$html = curl_exec($ch);
	curl_close($ch);
	
	$dom = new DOMDocument;
 
	echo $html;

?>