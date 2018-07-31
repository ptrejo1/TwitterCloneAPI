<?php

require ("secure/access.php");

$file = parse_ini_file("../TwitterClone.ini");

$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

$access = new access($host, $user, $pass, $name);
$access->connect();

$word = null;
$username = htmlentities($_POST["username"]);

if (!empty($_POST["word"])) {
    $word= htmlentities($_POST["word"]);

}

$users = $access->selectUsers($word, $username);
$returnArray = array();

if (!empty($users)) {
    $returnArray["users"] = $users;
} else {
    $returnArray["message"] = "Could not find records";
}

$access->disconnect();

echo json_encode($returnArray);