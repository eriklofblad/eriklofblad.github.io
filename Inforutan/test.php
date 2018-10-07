<?php

require_once __DIR__ . "/vendor/autoload.php";

$client = new MongoDB\Client("mongodb://inforutan:ip10KS02Los3n0rd@ds046027.mlab.com:46027/infopanel");

$jourcollection = $client->selectCollection('infopanel','testcollection3');

$timeNow = new DateTimeImmutable();

$today = $timeNow->format('Y-m-d');
$tomorrow = $timeNow->add(new DateInterval('P1D'))->format('Y-m-d');
$dATomorrow = $timeNow->add(new DateInterval('P2D'))->format('Y-m-d');

$dates = array(
    $today,
    $tomorrow,
    $dATomorrow
);

foreach($dates as $number => $date){
    $deleteResult = $jourcollection->deleteMany(['startdate'=> $date, 'lastModified' => ['$lt' => new MongoDB\BSON\UTCDateTime($timeNow)]]);

    echo "Deleted ". $deleteResult->getDeletedCount() . " Documents from: " . $date;
}


?>