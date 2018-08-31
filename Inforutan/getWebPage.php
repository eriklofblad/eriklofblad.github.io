<?php

/* gets the data from a URL */

	//url to the site
    if(isset($_GET["site"]))
    {
		$site = $_GET["site"];
		$cache_file = "cache/".hash('md5', $site).".html";
		//Requires cURL to be installed on server
		if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 600 ))) { // 600 seconds = 10 min.
			$file = file_get_contents($cache_file);
			echo $file;
		}else{
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $site);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$html = curl_exec($ch);
			$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($responseCode == 200){
				//$body = preg_replace("/.*<body[^>]*>|<\/body>.*/si", "", $html);
				if(strpos($contentType, "ISO-8859-1") != FALSE){
					$htmlencoded = mb_convert_encoding($html, "UTF-8", "ISO-8859-1");
					file_put_contents($cache_file, $htmlencoded, LOCK_EX);
					
					echo $htmlencoded;
				}else{
					file_put_contents($cache_file, $html, LOCK_EX);
					echo $html;
				}
			}else{
				echo "Error";
			}
			
		}
		
    }
	
	
	

?>