<?php

require ("secure/access.php");

$returnArray = array();

if (empty($_POST["username"]) && empty($_POST["fullname"]) && empty($_POST["email"]) && empty($_POST["id"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing info";
    return;
}

$username = htmlentities($_POST["username"]);
$fullname = htmlentities($_POST["fullname"]);
$email = htmlentities($_POST["email"]);
$id = htmlentities($_POST["id"]);

$file = parse_ini_file("../TwitterClone.ini");

$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

$access = new access($host, $user, $pass, $name);
$access->connect();

$result = $access->updateUser($username, $fullname, $email, $id);

if (!empty($result)) {
    $user = $access->selectUserViaID($id);

    $returnArray["id"] = $user["id"];
    $returnArray["username"] = $user["username"];
    $returnArray["fullname"] = $user["fullname"];
    $returnArray["email"] = $user["email"];
    $returnArray["ava"] = $user["ava"];
    $returnArray["status"] = "200";
    $returnArray["message"] = "Update Successful";
} else {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Couldn't update";
}

$access->disconnect();

echo json_encode($returnArray);