<?php

require_once __DIR__ . "/UserData.php";

try{
    $userdata = new UserData;

    header('Content-type: application/json; charset=UTF-8');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $userdata->validateUserData($_POST);

        $userdata->uploadUserSettings();
    }else if($_SERVER["REQUEST_METHOD"] == "GET"){
        $userdata->getUserSettings($_GET);
    }
    echo json_encode($userdata->response);
} catch(Exception $e) {
    $response = [
        "status" => "error",
        "statusText" => $e->getMessage()
    ];
    echo json_encode($response);
}


?>