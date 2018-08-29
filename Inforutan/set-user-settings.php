<?php

$userName = $_POST["userName"];

$userData = json_encode($_POST);



$userDataFile = fopen("userData/$userName.json", "w") or die("Error: Unable to open file!");

fwrite($userDataFile, $userData);
fclose($userDataFile);

echo "Success: Settings saved"

?>