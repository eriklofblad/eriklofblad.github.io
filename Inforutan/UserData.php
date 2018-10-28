<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/secrets/secrets.php";
require_once __DIR__ . "/secrets/pwdencryption.php";

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

    private $placeholderpassword = "PlaceHolder";

    private $validated = false;

    public function validateUserData($postData){
        $encrypt = new Pwdencryption;
        $scr = new Secrets;
        if(strlen($postData["userName"]) === 4){
            $this->userName = htmlspecialchars($postData["userName"]);
        }else{
            throw new Exception("Användarnamet är inte ett HSAID");
        }

        if(isset($postData["chooseOnCall"]) && count($postData["chooseOnCall"]) > 5){
            throw new Exception("Ogiltigt antal Joursiter");
        }

        foreach($this->userData as $key => $value){
            if(isset($postData[$key]) && !is_array($postData[$key])){
                if($key == "statdxpassword" || $key == "medinetPassword"){
                    if(!isset($postData["userKey"]) || strlen($postData["userKey"]) < 10){
                        throw new Exception("Ingen eller för kort krypteringsnyckel");
                    }else{
                        if($postData[$key] != $this->placeholderpassword){
                            $this->userData[$key] = $encrypt->encrypt($postData[$key], $postData["userKey"]);
                        }else{
                            unset($this->userData[$key]);
                        }

                    }
                }else{
                    $this->userData[$key] = htmlspecialchars($postData[$key]);
                }

            }else if(is_array($postData[$key])){
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

    public function uploadUserSettings(){
        $secr = new Secrets;
        $mongoClient = new MongoDB\Client("mongodb://". $secr->mongo_username . ":" . $secr->mongo_password . "@ds046027.mlab.com:46027/infopanel");

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
                if(isset($this->userData["statdxpassword"])){
                    $this->userData["statdxpassword"] = "encrypted";
                }
                if(isset($this->userData["medinetPassword"])){
                    $this->userData["medinetPassword"] = "encrypted";
                }
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


    public function getUserSettings($getData){
        $secr = new Secrets;
        $mongoClient = new MongoDB\Client("mongodb://". $secr->mongo_username . ":" . $secr->mongo_password . "@ds046027.mlab.com:46027/infopanel");
        if(isset($getData["user"])){
            $userscollection = $mongoClient->selectCollection('infopanel','users');

            $findresult = $userscollection->findOne(['userName' => $getData["user"]]);

            if($findresult === null){
                throw new Exception("Ingen användare hittad");
            }else{
                if(isset($findresult["statdxpassword"])){
                    $findresult["statdxpassword"] = $this->placeholderpassword;
                }
                if(isset($findresult["medinetPassword"])){
                    $findresult["medinetPassword"] = $this->placeholderpassword;
                }
                $this->response["status"] = "success";
                $this->response["statusText"]="Användare hittad";
                $this->response["userData"] = $findresult;
                return true;
            }
        }else{
            throw new Exception("Inget användarnamn angivet");
        }


    }

    public function getPassword($pwdType, $userName, $userKey){
        $encrypt = new Pwdencryption;
        $secr = new Secrets;
        $mongoClient = new MongoDB\Client("mongodb://". $secr->mongo_username . ":" . $secr->mongo_password . "@ds046027.mlab.com:46027/infopanel");

        $userscollection = $mongoClient->selectCollection('infopanel','users');

        $findresult = $userscollection->findOne([
            'userName' => $userName
            ],[
            'projection' => [
                '_id' => 0,
                $pwdType => 1
            ]
        ]);
        $decryptedData = $encrypt->decrypt($findresult[$pwdType], $userKey);
        return $decryptedData;
    }
}

?>