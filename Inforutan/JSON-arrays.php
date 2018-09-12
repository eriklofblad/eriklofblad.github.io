<?php


$positions = array(
    "Solna" => array("primärjour 1" => "pm-190", "primärjour 2" => "pm-189", "primärjour 3" => "pm-8", "primärjour 4" => "pm-7","solnaHelgDagjour" => "pm-6", "solnaMellanjour" => "pm-9", "solnaNattBakjour"=>"pm-4", "solnaDagBakjour"=>"pm-5"),
    "Neuro" => array("neuroNattJour" => 'day-79', "neuroBakjour" => 'day-80'),
    "KF" => array("kfSkvall" => 'pm-11', "kfShelg" => 'pm-12'),
    "Huddinge" => array("Hnattjour"=>'pm-1', "Hnattjour2"=>'pm-2', "Hnattjour3"=>'pm-3', "Hbakjour"=>"pm-4", "Hhelg" => "pm-7", "Hhelg2" => 'pm-8', "Hhelg3" => 'pm-16')

);

echo json_encode($positions);



?>