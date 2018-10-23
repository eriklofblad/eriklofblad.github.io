<?php

try{
    require_once __DIR__ . "/UserData.php";

    $userdata = new UserData;

    $password = $userdata->getPassword("statdxpassword","bkr9", "testnyckel123");

    $response = [
        "status" => "success",

    ];


}catch(Exception $e){
    echo $e->getMessage();
}


?>