<?php

require_once __DIR__ . "/vendor/autoload.php";

$client = new MongoDB\Client("mongodb://inforutan:ip10KS02Los3n0rd@ds046027.mlab.com:46027/infopanel");

$testcollection = $client->selectCollection('infopanel','testcollection');

/*

$insertOneResult = $testcollection->insertOne([
    'site' => 'Huddinge',
    'jourtyp' => 'Primärjour',
    'jourtod' => 'Natt',
    'starttime' => '15:30',
    'stoptime' => '9',
    'journamn' => 'Shahrzad Ashkani'
]);

var_dump($insertOneResult->getInsertedId());

*/

$findUpdateTime = $testcollection->findOne(['jourtyp' => 'Mellanjour']);

$lastModified = $findUpdateTime['lastModified']->toDateTime();
$timeNow = new DateTime();
$cacheTime =130;
if($lastModified < $timeNow->sub(new DateInterval('PT'. $cacheTime . 'M'))){
    echo "Cutofftiden är nådd";
}else{
    echo "Cutoff time: ". $timeNow->format('Y-m-d H:i') . "<br>";
}







?>