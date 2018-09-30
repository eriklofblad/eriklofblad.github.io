<?php




if(isset($_REQUEST["debug"])){
    $debug = true;
    $initialtime =  microtime(true);
    $intermediatetime = microtime(true);
}else{
    $debug = false;
}

$db_names = array(
    "ksrtgsolna",
    "ksneurorad",
    "ksfys",
    "ksrtghuddinge",
    "albrtg"
);

$medinetcodes = array(
    "sateet",
    "neuron",
    "tyokoe",
    "dicom",
    "SAOsE1nY"
);

$jour_file = "cache/cache-jourer.json";

$cache_time = 120; //minutes

$cutoff_time = 180; //minutes

$filetime = filemtime($jour_file);


if(!$debug){
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

}else if($debug){
    getMedinetSites($jour_file);

    echo "<br> sluttid "  . (microtime(true) - $initialtime) . "<br>";
}




function getMedinetSites($jour_file){
    global $debug;
    if($debug){
        global $initialtime;
        global $intermediatetime;
    }
    global $db_names;
    global $medinetcodes;


    $curloptions = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false
    );

    $positions = array(
        array("primärjour 1" => "pm-190", "primärjour 2" => "pm-189", "primärjour 3" => "pm-8", "primärjour 4" => "pm-7","solnaHelgDagjour" => "pm-6", "solnaMellanjour" => "pm-9", "solnaDagBakjour"=>"pm-5", "solnaNattBakjour"=>"pm-4"),
        array("neuroHelgDag" => "day-154", "neuroNattJour" => 'day-79', "neuroBakjour" => 'day-80'),
        array("kfSkvall" => 'pm-11', "kfShelg" => 'pm-12'),
        array("Hnattjour"=>'pm-1', "Hnattjour2"=>'pm-2', "Hnattjour3"=>'pm-3',"Hhelg" => "pm-7", "Hhelg2" => 'pm-8', "Hhelg3" => 'pm-16', "Hbakjour"=>"pm-4"),
        array("vardagnatt" => 'day-91', "helgdag" => 'day-92', "helgnatt" => 'day-93')

    );

    $mh = curl_multi_init();

    foreach ($db_names as $i => $db_name) {
        $url = "https://schema.medinet.se/". $db_name . "/schema/" . $medinetcodes[$i];
        $conn[$i] = curl_init($url);
        $medinetpostdata = "yearweek=". date('Y') ."%3A".date('W')."&schedule_type=week_vs_activity&schedule_subtype=days&show-no-weeks=1&confirmMessage=%C3%84r+du+s%C3%A4ker%3F&userId=-100&code=".$medinetcodes[$i]."&customer=".$db_name."&language=se";
        if($debug){echo $medinetpostdata . "<br>";}
        curl_setopt_array($conn[$i], $curloptions);
        curl_setopt($conn[$i], CURLOPT_POST, TRUE);
        curl_setopt($conn[$i], CURLOPT_POSTFIELDS, $medinetpostdata);
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

    foreach ($db_names as $i => $db_name) {
        $res[$i] = curl_multi_getcontent($conn[$i]);
        curl_multi_remove_handle($mh, $conn[$i]);
    }

    curl_multi_close($mh);
    if($debug){
        echo "efter första hämtning "  . (microtime(true) - $intermediatetime) . "<br>";
        $intermediatetime = microtime(true);
    }

    $jourkoder = array(
        array(),
        array(),
        array(),
        array()
    );

    $cutpositions = array(
        array("bakjour", "lunchvakt dt"),
        array("rjour", "ej jour"),
        array("helg", "rlsrond"),
        array("rjour", "punktionsjour"),
        array("jour vardag", "mellanjour helg")
    );

    foreach ($res as $i => $medinetsite){

        $firstcut = stripos($medinetsite, $cutpositions[$i][0]);
        $secondcut = strripos($medinetsite, $cutpositions[$i][1], $firstcut);
        if($debug){
            echo "<br>". $db_names[$i]. "<br>";
            echo $i . $cutpositions[$i][0] . " " . $cutpositions[$i][1] . "<br>";
            echo "first = " . $firstcut . " " . "second = " . $secondcut . "length = " . ($secondcut-$firstcut) . "<br>";
        }
        $medinetsite = substr($medinetsite,$firstcut, $secondcut-$firstcut);
        $n = 0;

        foreach($positions[$i] as $jour => $position){
            $findposition = $position . "-" . date('Y-m-d');
            if($debug){echo $findposition. " ";}
            $test = stripos($medinetsite, $findposition);
            if($debug){echo $test. " ";}
            if($test != false){
                $firstfind = "slotInfo('";
                $firstcut = stripos($medinetsite, $firstfind, $test) + strlen($firstfind);
                //echo $firstcut. " ";
                if(stripos($medinetsite, "</td>", $test) > $firstcut && $firstcut > $test){
                    $secondcut = stripos($medinetsite, "',", $firstcut);
                    $jourkod = substr($medinetsite,$firstcut, $secondcut-$firstcut);
                    $jourkoder[$i][$n] = $jourkod;
                    if($debug){echo $i . ": " . $jourkod . "<br>";}
                    $n++;
                }
                
            }
            
        }
    }
    if($debug){
        echo "efter första processning "  . (microtime(true) - $intermediatetime) . "<br>";
        $intermediatetime = microtime(true);
    }
    getMedinetInfo($jourkoder, $jour_file);

}

function getMedinetInfo($jourkoder, $jour_file){
    global $debug;
    global $initialtime;
    global $intermediatetime;
    global $db_names;

    $site = array(
        "Solna",
        "Neuro",
        "KF",
        "Huddinge",
        "Barn"
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
    if($debug){
        echo "efter andra hämtning "  . (microtime(true) - $intermediatetime) . "<br>";
        $intermediatetime = microtime(true);
    }
    $finalJSON = array(
        Solna => array(),
        Neuro => array(),
        KF => array(),
        Huddinge => array(),
        Barn => array()
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

            
            $firstfind2 = "<td>";
            $firstcut = stripos($response,$firstfind2, $secondcut) + strlen($firstfind2);
            $secondcut = stripos($response, "</td>", $firstcut);
            $jourtid = explode(" - ", substr($response, $firstcut, $secondcut - $firstcut));
            $starttimestamp = strtotime($jourtid[0]);
            $stopptimestamp = strtotime($jourtid[1]);

            $firstcut = stripos($response,$firstfind2, $secondcut) + strlen($firstfind2);
            $secondcut = stripos($response, "</td>", $firstcut);
            $journamninit = substr($response, $firstcut, $secondcut - $firstcut);
            if(substr($journamninit, 0, 2) == "20"){
                $firstcut = stripos($response,$firstfind2, $secondcut) + strlen($firstfind2);
                $secondcut = stripos($response, "</td>", $firstcut);
                $journamninit = substr($response, $firstcut, $secondcut - $firstcut);
            }
            $journamn = substr($journamninit, 0, strrpos($journamninit, ","));
            $journamn = str_ireplace("Nrad-ST", "", $journamn);
            $journamn = str_ireplace("Nrad ST", "", $journamn);
            $journamn = str_ireplace(",", "", $journamn);
            $journamn = str_ireplace("  ", " ", $journamn);

            $starttime = idate('H', $starttimestamp);
            $stopptime = idate('H', $stopptimestamp);
            if(idate('d',$starttimestamp) != idate('d', $stopptimestamp)){
                if($starttime > 12){
                    $jourtod = "Natt";
                }
            }else if($starttime <12){
                $jourtod = "Dag";
            }else if($starttime <17){
                $jourtod = "Kväll";
            }

            if(idate('i', $starttimestamp)!=0){
                $starttime = date('H:i', $starttimestamp);
            }
            if(idate('i', $stopptimestamp)!=0){
                $stopptime = date('H:i', $stopptimestamp);
            }

            // echo $site[$n]. " " .$jourtyp. " ". $jourtod. " " . $starttime . "-" . $stopptime."<br>";
            // echo $journamn. "<br>";
            //echo str_replace("ffffe0","E86745",$response);

            $finalJSON[$site[$n]][]= array("jourtyp"=>utf8_encode($jourtyp), "jourtod"=>$jourtod,"starttime"=>$starttime, "stopptime"=>$stopptime, "journamn"=>utf8_encode($journamn));
        }
    }
    if($debug){
        echo "efter andra processning " . (microtime(true) - $intermediatetime) . "<br>";
        // var_dump($finalJSON);

        echo json_encode($finalJSON);
    }else{
        file_put_contents($jour_file, json_encode($finalJSON) , LOCK_EX);
    }
}

?>