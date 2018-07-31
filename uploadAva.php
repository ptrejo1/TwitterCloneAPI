<?php

require ("secure/access.php");

if (empty($_POST["id"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required info";
    return;
}

$id = htmlentities($_POST["id"]);

$folder = "TwitterClone/ava/".$id;

// make folder if DNE
if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
}

$folder = $folder."/".basename($_FILES["file"]["name"]);


if (move_uploaded_file($_FILES["file"]["tmp_name"], $folder)) {
    $returnArray["status"] = "200";
    $returnArray["message"] = "Successfully uploaded";
} else {
    $returnArray["status"] = "300";
    $returnArray["message"] = "Error while uploading";
}

$file = parse_ini_file("../TwitterClone.ini");

$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

$access = new access($host, $user, $pass, $name);
$access->connect();

$path = "http://localhost:8080/TwitterClone/ava/".$id."/ava.jpg";
$access->updateAvaPath($path, $id);

$user = $access->selectUserViaID($id);

$returnArray["id"] = $user["id"];
$returnArray["username"] = $user["username"];
$returnArray["fullname"] = $user["fullname"];
$returnArray["email"] = $user["email"];
$returnArray["ava"] = $user["ava"];

$access->disconnect();

echo json_encode($returnArray);

