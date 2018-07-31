<?php

require ("secure/access.php");
require ("secure/email.php");

if (empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"]) || empty($_POST["fullname"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required info";
    echo json_encode($returnArray);
    return;
}

// htmlentities helps w/ injection
$username = htmlentities($_POST["username"]);
$password = htmlentities($_POST["password"]);
$email = htmlentities($_POST["email"]);
$fullname = htmlentities($_POST["fullname"]);

$salt = openssl_random_pseudo_bytes(20);
$secured_password = sha1($password . $salt);

$file = parse_ini_file("../TwitterClone.ini");

$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

$access = new access($host, $user, $pass, $name);
$access->connect();

$result = $access->registerUser($username, $secured_password, $salt, $email, $fullname);

if ($result) {
    $user = $access->selectUser($username);

    $returnArray["status"] = "200";
    $returnArray["message"] = "Successfully registered";
    $returnArray["id"] = $user["id"];
    $returnArray["username"] = $user["username"];
    $returnArray["email"] = $user["email"];
    $returnArray["fullname"] = $user["fullname"];
    $returnArray["ava"] = $user["ava"];

    $email = new email();
    $token = $email->generateToken(20);

    $access->saveToken("emailTokens", $user["id"], $token);

    $details = array();
    // place valid credentials
    $details["subject"] = "Email confirmation on TwitterClone";
    $details["to"] = $user["email"];
    $details["fromName"] = "";
    $details["fromEmail"] = "";

    $template = $email->confirmationTemplate();

    // replace token from confirmationTemplate.html
    $template = str_replace("{token}", $token, $template);

    $details["body"] = $template;

    $email->sendEmail($details);
} else {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Could not register w/ provided info";
}

// close connection
$access->disconnect();
echo json_encode($returnArray);