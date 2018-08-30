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
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$html = curl_exec($ch);
	$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	curl_close($ch);
	if(strpos($contentType, "ISO-8859-1") != FALSE){
		$htmlencoded = mb_convert_encoding($html, "UTF-8", "ISO-8859-1");
		echo $htmlencoded;
	}else{
		echo $html;
	}
	

?>