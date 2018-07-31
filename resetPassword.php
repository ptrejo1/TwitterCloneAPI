<?php

require ("secure/access.php");
require ("secure/email.php");

if (empty($_POST["email"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing info";
    echo json_encode($returnArray);
    return;
}

$email = htmlentities($_POST["email"]);

$file = parse_ini_file("../TwitterClone.ini");

$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

$access = new access($host, $user, $pass, $name);
$access->connect();

$user = $access->selectUserViaEmail($email);

if (empty($user)) {
    //$returnArray["status"] = "";
    $returnArray["message"] = "email not found";
    echo json_encode($returnArray);
    return;
}

$email = new email();

$token = $email->generateToken(20);
$access->saveToken("passwordTokens", $user["id"], $token);

$details = array();
// place credentials
$details["subject"] = "Password reset POST";
$details["to"] = $user["email"];
$details["fromName"] = "TwitterClone";
$details["fromEmail"] = "";

$template = $email->resetPasswordTemplate();
$template = str_replace("{token}", $token, $template);

$details["body"] = $template;

$email->sendEmail($details);

$returnArray["email"] = $user["email"];
$returnArray["message"] = "We have sent you an email to reset password";
echo json_encode($returnArray);

$access->disconnect();
