<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/secrets/secrets.php";

$secr = new Secrets;

$client = new MongoDB\Client("mongodb://". $secr->mongo_username . ":" . $secr->mongo_password . "@ds046027.mlab.com:46027/infopanel");

$jourcollection = $client->selectCollection('infopanel','jourer');





if(isset($_GET["site"])){

    $sites = [];

    foreach($_GET["site"] as $site){
        $sites[] = ['site' => $site];
    };

    $centerdate = $_GET["centerdate"];
    $centerdateobject = new DateTimeImmutable($centerdate);
    $firstdate = $centerdateobject->add(new DateInterval('P1D'))->format('Y-m-d');
    $lastdate = $centerdateobject->sub(new DateInterval('P1D'))->format('Y-m-d');


    $jourer = $jourcollection->find(
        [   '$and' => [
                ['$or' =>
                [
                    ['startdate' => $centerdate],
                    ['startdate' => $firstdate],
                    ['startdate' => $lastdate]
                ]],
                ['$or' => $sites]
            ]
        ],
        [
            'projection' => [
                '_id' => 0,
                'site' => 1,
                'startdate' => 1,
                'jourtyp'=>1,
                'journamn' => 1,
                'jourtod' => 1,
                'starttime' => 1,
                'stopptime' => 1
            ],
            'sort' => [
                'site'=>-1,
                'jourtyp'=>-1
            ]
        ]
    );

    $jourarray = $jourer->toArray();

    header('Content-type: application/json; charset=UTF-8');

    echo json_encode($jourarray);
}else{
    echo "Error: set site query tag";
}

?>