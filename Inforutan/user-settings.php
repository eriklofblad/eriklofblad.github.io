<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/secrets/secrets.php";

try{
    $userdata = new UserData;

    $secr = new MDSecrets;

    $client = new MongoDB\Client("mongodb://". $secr->mongo_username . ":" . $secr->mongo_password . "@ds046027.mlab.com:46027/infopanel");

    header('Content-type: application/json; charset=UTF-8');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $userdata->validateUserData($_POST);

        $userdata->uploadUserSettings($client);
    }else if($_SERVER["REQUEST_METHOD"] == "GET"){
        $userdata->getUserSettings($client);
    }
    echo json_encode($userdata->response);
} catch(Exception $e) {
    $response = [
        "status" => "error",
        "statusText" => $e->getMessage()
    ];
    echo json_encode($response);
}

class UserData{

    private $userData = [
        "userName" => "",
        "phoneNumber1" => "",
        "startTab" => "",
        "chooseOnCall" => [],
        "medinetPassword" => "",
        "medinetSite" => "",
        "statdxusername" => "",
        "statdxpassword" => ""
    ];

    public $response = [
        "status" => "",
        "statusText" => ""
    ];

    private $validated = false;

    public function validateUserData($postData){
        if(strlen($postData["userName"]) === 4){
            $this->userName = htmlspecialchars($postData["userName"]);
        }else{
            throw new Exception("Användarnamet är inte ett HSAID");
        }

        if(count($postData["chooseOnCall"]) > 5){
            throw new Exception("Ogiltigt antal Joursiter");
        }

        foreach($this->userData as $key => $value){
            if(isset($postData[$key]) && count($postData[$key]) == 1){
                $this->userData[$key] = htmlspecialchars($postData[$key]);
            }else if(count($postData[$key]) > 1){
                foreach($postData[$key] as $i => $val){
                    $this->userData[$key][] = htmlspecialchars($val);
                }
            }else{
                unset($this->userData[$key]);
            }
        }
        $this->validated = true;
        return true;
    }

    public function uploadUserSettings($mongoClient){
        $userscollection = $mongoClient->selectCollection('infopanel','users');
        if($this->validated){
            $updateResult = $userscollection->updateOne(
                ['userName' => $this->userData["userName"]],
                [
                    '$set' => $this->userData
                ],
                [
                    'upsert' => true
                ]
            );
            if($updateResult->isAcknowledged()){
                $this->response["status"] = "success";
                $this->response["statusText"]="Inställnignar sparade";
                $this->response["updatedData"] = $this->userData;
                $this->response["mongoResult"]["Matched count"] = $updateResult->getMatchedCount();
                $this->response["mongoResult"]["Modified count"] = $updateResult->getModifiedCount();
                $this->response["mongoResult"]["Upserted count"] = $updateResult->getUpsertedCount();
                return true;
            }else{
                throw new Exception("Lyckades inte spara inställningarna");
            }
        }else{
            throw new Exception("Inställningarna var ej validerade");
        }


    }


    public function getUserSettings($mongoClient){
        if(isset($_GET["user"])){
            $userscollection = $mongoClient->selectCollection('infopanel','users');

            $findresult = $userscollection->findOne(['userName' => $_GET["user"]]);

            if($findresult === null){
                throw new Exception("Ingen användare hittad");
            }else{
                $this->response["status"] = "success";
                $this->response["statusText"]="Användare hittad";
                $this->response["userData"] = $findresult;
                return true;
            }
        }else{
            throw new Exception("Inget användarnamn angivet");
        }


    }
}






?>