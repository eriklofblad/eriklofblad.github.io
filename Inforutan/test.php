<?php

require_once __DIR__ . "/secrets/pwdencryption.php";
require_once __DIR__ . "/secrets/secrets.php";

$encrypt = new Pwdencryption;
$scr = new Secrets;

$base64 = base64_encode(openssl_random_pseudo_bytes(32));
$hex = bin2hex(openssl_random_pseudo_bytes(32));

echo "Base64= " . $base64 . " Length= " . strlen($base64) . "<br>";
echo "Hex= " . $hex . " Length= " . strlen($hex) . "<br>";
?>