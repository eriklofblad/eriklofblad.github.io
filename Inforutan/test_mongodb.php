<?php

require_once __DIR__ . "/vendor/autoload.php";

$client = new MongoDB\Client("mongodb://inforutan:ip10KS02Los3n0rd@ds046027.mlab.com:46027/infopanel");

$testcollection = $client->selectCollection('infopanel','testcollection2');

$insertOneResult = $testcollection->insertOne([
    'site' => 'Huddinge',
    'jourtyp' => 'Primärjour',
    'jourtod' => 'Natt',
    'starttime' => '15:30',
    'stoptime' => '9',
    'journamn' => 'Shahrzad Ashkani'
]);

var_dump($insertOneResult->getInsertedId());

?>