<?php

require ("secure/access.php");

$file = parse_ini_file("../TwitterClone.ini");

$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

$access = new access($host, $user, $pass, $name);
$access->connect();

$returnArray = array();

if (!empty($_POST["uuid"]) && !empty($_POST["text"])) {
    $id = htmlentities($_POST["id"]);
    $uuid = htmlentities($_POST["uuid"]);
    $text = htmlentities($_POST["text"]);

    // place valid file path
    $folder = "/TwitterClone/posts/".$id;

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $folder = $folder."/".basename($_FILES["file"]["name"]);

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $folder)) {
        $returnArray["status"] = "200";
        $returnArray["message"] = "Post made with picture";
        // place location
        $path = "TwitterClone/posts/".$id."/post-".$uuid.".jpg";
    } else {
        $returnArray["status"] = "200";
        $returnArray["message"] = "Post made without picture";
        $path = "";
    }

    $access->insertPost($id, $uuid, $text, $path);


} else if (!empty($_POST["uuid"]) && empty($_POST["id"])) {
    $uuid = htmlentities($_POST["uuid"]);
    $path = htmlentities($_POST["path"]);

    $result = $access->deletePost($uuid);

    if (!empty($result)) {
        $returnArray["message"] = "Successfully deleted";
        $returnArray["result"] = $result;

        if (!empty($path)) {
            // place valid path
            $path = str_replace("http://localhost:8080/", "", $path);

            if (unlink($path)) {
                $returnArray["status"] = "1000";
            } else {
                $returnArray["status"] = "400";
            }
        }

    } else {
        $returnArray["message"] = "Could not delete";
    }

} else {
    $id = htmlentities($_POST["id"]);

    $posts = $access->selectPosts($id);

    if (!empty($posts)) {
        $returnArray["posts"] = $posts;
    }
}

$access->disconnect();

echo json_encode($returnArray);
