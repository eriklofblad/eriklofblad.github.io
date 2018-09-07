<?php

function getMedinetSites(){

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
        array("primärjour 1" => "pm-190", "primärjour 2" => "pm-189", "primärjour 3" => "pm-8", "primärjour 4" => "pm-7"),
        array("neuroNattJour" => 'day-79', "neuroBakjour" => 'day-80'),
        array("kfSkvall" => 'pm-11', "kfShelg" => 'pm-12'),
        array("Hnattjour"=>'pm-1', "Hnattjour"=>'pm-2', "Hnattjour"=>'pm-3', "Hbakjour"=>"pm-4")

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

    getMedinetInfo($jourkoder);

}

function getMedinetInfo($jourkoder){

    $db_names = array(
        "ksrtgsolna",
        "ksneurorad",
        "ksfys",
        "ksrtghuddinge"
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

    foreach ($res as $responsearray){
        foreach($responsearray as $response){
            echo $response;
        }
    }

}

getMedinetSites();

?>