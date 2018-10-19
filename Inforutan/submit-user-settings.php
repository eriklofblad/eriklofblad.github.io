<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/secrets/secrets.php";

$userdata = new UserData;

$secr = new MDSecrets;

$client = new MongoDB\Client("mongodb://". $secr->mongo_username . ":" . $secr->mongo_password . "@ds046027.mlab.com:46027/infopanel");

header('Content-type: application/json; charset=UTF-8');

$userdata->validateUserData($_POST) or die (json_encode($userdata->response));

$userdata->uploadUserSettings($client) or die (json_encode($userdata->response));

echo json_encode($userdata->response);

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
        "statusText" => "",
        "updatedData" => "",
        "mongoResult" => [
            "Matched count" => "",
            "Modified count" => "",
            "Upserted count" => ""
        ]
    ];

    private $validated = false;

    public function validateUserData($postData){
        if(strlen($postData["userName"]) === 4){
            $this->userName = htmlspecialchars($postData["userName"]);
        }else{
            $this->response["status"] = "error";
            $this->response["statusText"]="Användarnamet är inte ett HSAID";
            return false;
        }

        if(count($postData["chooseOnCall"]) > 5){
            $this->response["status"] = "error";
            $this->response["statusText"]="Ogiltigt antal Joursiter";
            return false;
        }

        foreach($this->userData as $key => $value){
            if($postData[$key] != null && count($postData[$key]) == 1){
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
                $this->response["status"] = "error";
                $this->response["statusText"]="Lyckades inte spara inställningarna";
                return false;
            }
        }else{
            $this->response["status"] = "error";
            $this->response["statusText"]="Inställningarna var ej validerade";
            return false;
        }


    }
}






?>