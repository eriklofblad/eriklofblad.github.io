<?php
    $user = $_REQUEST["user"];

    echo file_exists("userData/$user.json");

?>