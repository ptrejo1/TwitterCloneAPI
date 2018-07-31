<?php

require ("secure/access.php");

if (empty($_POST["username"]) || empty($_POST["password"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required info";
    echo json_encode($returnArray);
    return;
}

$username = htmlentities($_POST["username"]);
$password = htmlentities($_POST["password"]);

$file = parse_ini_file("../TwitterClone.ini");

$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

$access = new access($host, $user, $pass, $name);
$access->connect();

$user = $access->getUser($username);

if (empty($user)) {
    $returnArray["status"] = "403";
    $returnArray["message"] = "User not found";
    echo json_encode($returnArray);
    return;
}

$secured_password = $user["password"];
$salt = $user["salt"];

if ($secured_password == sha1($password . $salt)) {
    $returnArray["status"] = "200";
    $returnArray["message"] = "Logged in successfully";
    $returnArray["id"] = $user["id"];
    $returnArray["username"] = $user["username"];
    $returnArray["email"] = $user["email"];
    $returnArray["fullname"] = $user["fullname"];
    $returnArray["ava"] = $user["ava"];
} else {
    $returnArray["status"] = "403";
    $returnArray["message"] = "Wrong password";
}

$access->disconnect();
echo json_encode($returnArray);