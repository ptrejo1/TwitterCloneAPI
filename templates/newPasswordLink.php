<?php

require ("../secure/access.php");

if (!empty($_POST["password_1"]) && !empty($_POST["password_2"]) && !empty($_POST["token"])) {
    $password_1 = htmlentities($_POST["password_1"]);
    $password_2 = htmlentities($_POST["password_2"]);
    $token = htmlentities($_POST["token"]);

    if ($password_1 == $password_2) {
        $file = parse_ini_file("../../TwitterClone.ini");

        $host = trim($file["dbhost"]);
        $user = trim($file["dbuser"]);
        $pass = trim($file["dbpass"]);
        $name = trim($file["dbname"]);

        $access = new access($host, $user, $pass, $name);
        $access->connect();

        $user = $access->getUserID("passwordTokens", $token);

        if (!empty($user)) {
            $salt = openssl_random_pseudo_bytes(20);
            $secured_password = sha1($password_1 . $salt);

            $result = $access->updatePassword($user["id"], $secured_password, $salt);

            if ($result) {
                $access->deleteToken("passwordTokens", $token);
                $message = "Successfully created new password";

                header("Location:didResetPassword.php?message=".$message);
            } else {
                echo "User ID is empty";
            }
        }

    } else {
        $message = "Passwords do not match";
    }
}

?>


<html>
    <head>
        <title>Create new password</title>
        <style>
            .password_field
            {
                margin: 10px;
            }

            .button
            {
                margin: 10px;
            }
        </style>

    </head>

    <body>
        <h1>Create new password</h1>

        <?php
            if (!empty($message)) {
                echo "</br>".$message."</br>";
            }
        ?>

        <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
            <div><input type="password" name="password_1" placeholder="New password" class="password_field"/></div>
            <div><input type="password" name="password_2" placeholder="Repeat password" class="password_field"/></div>
            <div><input type="submit" value="Save" class="button"/></div>
            <input type="hidden" value="<?php echo $_GET['token']?>" name ="token">
        </form>
    </body>

</html>