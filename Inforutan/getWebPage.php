<?php

/* gets the data from a URL */

	//url to the site
    if(isset($_GET["site"]))
    {
		$site = $_GET["site"];
		$cache_file = "cache/cache-".hash('md5', $site).".html";
		//Requires cURL to be installed on server
		if(isset($_GET["cachetime"])){
			$cache_time = $_GET["cachetime"] * 60;
		}else{
			$cache_time = 600;
		}
		if (file_exists($cache_file) && (filemtime($cache_file) > (time() - $cache_time))) { // 600 seconds = 10 min.
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
			if($responseCode >= 200 && $responseCode < 300){
				#convert from charset ISO-8859-1 to UTF-8
				if(strpos($contentType, "ISO-8859-1") != FALSE){
					$html= mb_convert_encoding($html, "UTF-8", "ISO-8859-1");
				}
				if(isset($_GET["newscut"])){
					$firstcut = '<section class="news-list">';
					$secondcut = '</section>';
				}else{
					$firstcut = "<body>";
					$secondcut = "</body>";
				}

				$startpos = stripos($html, $firstcut);
				$endpos = strripos($html, $secondcut) + strlen($secondcut);
				$length = $endpos - $startpos;

				$body = "<!DOCTYPE html><html><head></head><body>" . trim(substr($html, $startpos, $length)) . "</body></html>";

				//$body = preg_replace('~<body[^>]*>(.*?)</body>~si', "", $html) or die("Unable to do preg_replace");
				
				echo $body;
				file_put_contents($cache_file, $body, LOCK_EX);
			}else{
				echo "Error";
			}
			
		}
		
    }
	
	
	

?>
