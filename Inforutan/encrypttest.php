<?php

require_once __DIR__ . "/secrets/secrets.php";
require_once __DIR__ . "/secrets/pwdencryption.php";

$encr = new Pwdencryption;

$scr = new Secrets;

$serverkey = $scr->encrypt_serverkey;
$userkey = "ett annat lösenord";
$data = "hejhej";

$inttime = microtime(true);

$key = $encr->makeKey($serverkey, $userkey);

echo "Nyckellängd= " . strlen($key) . "<br>";

$encryptedData = $encr->encrypt($data,$key);

$encrypttime = microtime(true) - $inttime;
$inttime = microtime(true);

$userkey = "ett annat lösenord";

$key = $encr->makeKey($serverkey, $userkey);

$decryptedData = $encr->decrypt($encryptedData, $key);

$decrypttime = microtime(true) - $inttime;

echo $encryptedData . "   Length= " . strlen($encryptedData) . "<br>";
echo "encryption time= " . $encrypttime . "<br>";
echo $decryptedData . "<br>";
echo "decryption time= ". $decrypttime;

?>