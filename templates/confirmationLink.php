<?php

require ("../secure/access.php");

if (empty($_GET["token"])) { echo "Missing info"; }

$token = htmlentities($_GET["token"]);

$file = parse_ini_file("../../TwitterClone.ini");

$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

$access = new access($host, $user, $pass, $name);
$access->connect();

$id  = $access->getUserID("emailTokens", $token);

if (empty($id["id"])) {
    echo "User with this token not found";
    return;
}

$result = $access->emailConfirmationStatus(1, $id["id"]);

if ($result) {
    $access->deleteToken("emailTokens", $token);
    echo "Thank You, email confirmed";
}

$access->disconnect();