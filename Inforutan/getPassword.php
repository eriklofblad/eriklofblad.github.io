<?php


try{
    if(isset($_GET["user"]) && isset($_GET["userKey"]) && isset($_GET["pwtype"])){
        require_once __DIR__ . "/UserData.php";

        $userdata = new UserData;

        header('Content-type: application/json; charset=UTF-8');

        $password = $userdata->getPassword("statdxpassword","bkr9","testnyckel123");

        $response = [
            "status" => "success",
            $_GET["pwtype"] => $password
        ];

        echo json_encode($response);
    }else{
        throw new Exception("Otillräcklig query");
    }

}catch(Exception $e){
    $response = [
        'status' => 'error',
        'statusText' => $e->getMessage()
    ];
    echo json_encode($response);
}


?>