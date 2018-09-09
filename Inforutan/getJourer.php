<?php


$jour_file = "cache/cache-jourer.json";

$cache_time = 120; //minutes

$cutoff_time = 180; //minutes

$filetime = filemtime($jour_file);

if (file_exists($jour_file) && $filetime > (time() - $cache_time*60) && idate('md',$filetime) == idate('md')) {
    $file = file_get_contents($jour_file);
    echo $file;
}else if(file_exists($jour_file) && filemtime($jour_file) > (time() - $cutoff_time*60) && idate('md',$filetime) == idate('md')){
    $file = file_get_contents($jour_file);
    echo $file;
    getMedinetSites($jour_file);
}else{
    getMedinetSites($jour_file);
    $file = file_get_contents($jour_file);
    echo $file;
}

function getMedinetSites($jour_file){

    $curloptions = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false
    );


    $curlurls = array(
        'https://schema.medinet.se/ksrtgsolna/schema/sateet',
        'https://schema.medinet.se/ksneurorad/schema/neuron',
        'https://schema.medinet.se/ksfys/schema/tyokoe',
        'https://schema.medinet.se/ksrtghuddinge/schema/dicom'
    );

    $positions = array(
        array("primärjour 1" => "pm-190", "primärjour 2" => "pm-189", "primärjour 3" => "pm-8", "primärjour 4" => "pm-7","solnaHelgDagjour" => "pm-6", "solnaMellanjour" => "pm-9", "solnaDagBakjour"=>"pm-5", "solnaNattBakjour"=>"pm-4"),
        array("neuroHelgDag" => "day-154", "neuroNattJour" => 'day-79', "neuroBakjour" => 'day-80'),
        array("kfSkvall" => 'pm-11', "kfShelg" => 'pm-12'),
        array("Hnattjour"=>'pm-1', "Hnattjour2"=>'pm-2', "Hnattjour3"=>'pm-3',"Hhelg" => "pm-7", "Hhelg2" => 'pm-8', "Hhelg3" => 'pm-16', "Hbakjour"=>"pm-4")

    );

    $mh = curl_multi_init();

    foreach ($curlurls as $i => $url) {
        $conn[$i] = curl_init($url);
        curl_setopt_array($conn[$i], $curloptions);
        curl_multi_add_handle($mh, $conn[$i]);
    }

    $active = null;
    //execute the handles
    do {
        $status = curl_multi_exec($mh, $active);
        // Check for errors
        if($status > 0) {
            // Display error message
            echo "ERROR!\n " . curl_multi_strerror($status);
        }
    } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

    foreach ($curlurls as $i => $url) {
        $res[$i] = curl_multi_getcontent($conn[$i]);
        curl_multi_remove_handle($mh, $conn[$i]);
    }

    curl_multi_close($mh);

    $jourkoder = array(
        array(),
        array(),
        array(),
        array()
    );

    foreach ($res as $i => $medinetsite){
        $n = 0;
        foreach($positions[$i] as $jour => $position){
            $findposition = $position . "-" . date('Y-m-d');
            $test = stripos($medinetsite, $findposition);
            if($test != false){
                $firstfind = "slotInfo('";
                $firstcut = stripos($medinetsite, $firstfind, $test) + strlen($firstfind);
                if(stripos($medinetsite, "</td>", $test) > $firstcut){
                    $secondcut = stripos($medinetsite, "',", $firstcut);
                    $jourkod = substr($medinetsite,$firstcut, $secondcut-$firstcut);
                    $jourkoder[$i][$n] = $jourkod;
                    $n++;
                }
                
            }
            
        }
    }

    getMedinetInfo($jourkoder, $jour_file);

}

function getMedinetInfo($jourkoder, $jour_file){

    $db_names = array(
        "ksrtgsolna",
        "ksneurorad",
        "ksfys",
        "ksrtghuddinge"
    );

    $site = array(
        "Solna",
        "Neuro",
        "KF",
        "Huddinge"
    );

    $curloptions = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false
    );

    $mh2 = curl_multi_init();

    foreach ($jourkoder as $n => $jourkodarray){
        foreach ($jourkodarray as $i => $jourkod) {
            $conn[$n][$i] = curl_init("https://schema.medinet.se/cgi-bin/eventInfo.pl?event_type=work&event_id=".$jourkod."&db_name=".$db_names[$n]);
            curl_setopt_array($conn[$n][$i], $curloptions);
            curl_multi_add_handle($mh2, $conn[$n][$i]);
        }
    }
    $active = null;
    //execute the handles
    do {
        $status = curl_multi_exec($mh2, $active);
        // Check for errors
        if($status > 0) {
            // Display error message
            echo "ERROR!\n " . curl_multi_strerror($status);
        }
    } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

    foreach ($jourkoder as $n => $jourkodarray){
        foreach ($jourkodarray as $i => $jourkod) {
            $res[$n][$i] = curl_multi_getcontent($conn[$n][$i]);
            curl_multi_remove_handle($mh2, $conn[$n][$i]);
        }
    }
    curl_multi_close($mh2);

    $finalJSON = array(
        Solna => array(),
        Neuro => array(),
        KF => array(),
        Huddinge => array()
    );

    foreach ($res as $n => $responsearray){
        foreach($responsearray as $i => $response){
            $firstfind = '<td class="heading">';
            $firstcut = stripos($response,$firstfind) + strlen($firstfind);
            $test = stripos($response, ">", $firstcut);
            $secondcut = stripos($response, "</td>", $firstcut);
            if($test < $secondcut){
                $firstcut = $test + 1;
            }
            $jourtyp = substr($response, $firstcut, $secondcut - $firstcut);
            $jourtyp = trim($jourtyp);
            $trimjourtyp = stripos($jourtyp, " ");
            if($trimjourtyp > 0){
                $jourtyp = substr($jourtyp, 0, $trimjourtyp);
            }

            if(stripos($jourtyp, "jour") == 0){
                $jourtyp = $jourtyp . "jour";
            }

            
            $firstfind2 = "<td>";
            $firstcut = stripos($response,$firstfind2, $secondcut) + strlen($firstfind2);
            $secondcut = stripos($response, "</td>", $firstcut);
            $jourtid = explode(" - ", substr($response, $firstcut, $secondcut - $firstcut));
            $starttimestamp = strtotime($jourtid[0]);
            $stopptimestamp = strtotime($jourtid[1]);

            $firstcut = stripos($response,$firstfind2, $secondcut) + strlen($firstfind2);
            $secondcut = stripos($response, "</td>", $firstcut);
            $journamninit = substr($response, $firstcut, $secondcut - $firstcut);
            $journamn = substr($journamninit, 0, strrpos($journamninit, ","));
            $journamn = str_ireplace("Nrad-ST", "", $journamn);
            $journamn = str_ireplace("Nrad ST", "", $journamn);
            $journamn = str_ireplace(",", "", $journamn);
            $journamn = str_ireplace("  ", " ", $journamn);

            $starttime = idate('H', $starttimestamp);
            $stopptime = idate('H', $stopptimestamp);
            if(idate('d',$starttimestamp) < idate('d', $stopptimestamp)){
                if($starttime > 12){
                    $jourtod = "natt";
                }
            }else if($starttime < 12){
                $jourtod = "dag";
            }else if($starttime <17){
                $jourtod = "kväll";
            }

            // echo $site[$n]. " " .$jourtyp. " ". $jourtod. " " . $starttime . "-" . $stopptime."<br>";
            // echo $journamn. "<br>";
            //echo str_replace("ffffe0","E86745",$response);

            $finalJSON[$site[$n]][]= array("jourtyp"=>utf8_encode($jourtyp), "jourtod"=>$jourtod,"starttime"=>$starttime, "stopptime"=>$stopptime, "journamn"=>utf8_encode($journamn));
        }
    }
    //var_dump($finalJSON);
    file_put_contents($jour_file, json_encode($finalJSON) , LOCK_EX); ;
}

?>