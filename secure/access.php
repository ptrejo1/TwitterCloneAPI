<?php

class access {

    // connection vars
    var $host = null;
    var $user = null;
    var $pass = null;
    var $name = null;
    var $conn = null;
    var $result = null;

    function __construct($dbhost, $dbuser, $dbpass, $dbname) {
        $this->host = $dbhost;
        $this->user = $dbuser;
        $this->pass = $dbpass;
        $this->name = $dbname;
    }

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name);
        if (mysqli_connect_errno()) { echo "Could not connect"; }
        $this->conn->set_charset("utf8");
    }

    public function disconnect() {
        if ($this->conn != null) {$this->conn->close();}
    }

    // Insert user
    public function registerUser($username, $password, $salt, $email, $fullname){
        $sql = "INSERT INTO users SET username=?, password=?, salt=?, email=?, fullname=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("sssss", $username, $password, $salt, $email, $fullname);
        $value = $statement->execute();
        return $value;
    }

    public function selectUser($username){
        $returnArray = array();

        $sql = "SELECT * FROM users WHERE username='".$username."'";
        $result = $this->conn->query($sql);

        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if (!empty($row)) {
                $returnArray = $row;
            }
        }
        return $returnArray;
    }

    // save email token
    public function saveToken($table, $id, $token) {
        $sql = "INSERT INTO $table SET id=?, token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("is", $id, $token);
        $returnValue = $statement->execute();

        return $returnValue;
    }

    function getUserID($table, $token) {
        $returnArray = array();

        $sql = "SELECT id FROM $table WHERE token='".$token."'";
        $result = $this->conn->query($sql);

        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if (!empty($row)) { $returnArray = $row; }
        }

        return $returnArray;
    }

    function emailConfirmationStatus($status, $id) {
        $sql = "UPDATE users SET emailConfirmed=? WHERE id=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("ii", $status, $id);
        $returnValue = $statement->execute();

        return $returnValue;
    }

    function deleteToken($table, $token) {
        $sql = "DELETE FROM $table WHERE token=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("s", $token);
        $returnValue = $statement->execute();

        return $returnValue;
    }

    function getUser($username) {
        $returnArray = array();

        $sql = "SELECT * FROM users WHERE username='".$username."'";
        $result = $this->conn->query($sql);

        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if (!empty($row)) { $returnArray = $row; }
        }

        return $returnArray;
    }

    public function selectUserViaEmail($email){
        $returnArray = array();

        $sql = "SELECT * FROM users WHERE email='".$email."'";
        $result = $this->conn->query($sql);
        $returnArray = null;

        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if (!empty($row)) {
                $returnArray = $row;
            }
        }
        return $returnArray;
    }

    public function selectUserViaID($id){
        $returnArray = array();

        $sql = "SELECT * FROM users WHERE id='".$id."'";
        $result = $this->conn->query($sql);
        $returnArray = null;

        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if (!empty($row)) {
                $returnArray = $row;
            }
        }
        return $returnArray;
    }

    public function updatePassword($id, $password, $salt) {
        $sql = "UPDATE users SET password=?, salt=? WHERE id=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("ssi", $password, $salt, $id);
        $returnValue = $statement->execute();

        return $returnValue;
    }

    function updateAvaPath($path, $id) {
        $sql = "UPDATE users SET ava=? WHERE id=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("si", $path, $id);
        $returnValue = $statement->execute();

        return $returnValue;
    }

    public function insertPost($id, $uuid, $text, $path) {
        $sql =  "INSERT INTO posts SET id=?, uuid=?, text=?, path=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("isss", $id, $uuid, $text, $path);
        $returnValue = $statement->execute();

        return $returnValue;
    }

    public function selectPosts($id) {
        $returnArray = array();

        $sql = "SELECT posts.id, 
        posts.uuid, 
        posts.text, 
        posts.path, 
        posts.date, 
        users.username, 
        users.fullname, 
        users.email, 
        users.ava
        FROM TwitterClone.posts JOIN TwitterClone.users 
        ON posts.id =$id and users.id=$id ORDER BY date DESC";

        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->execute();

        $result = $statement->get_result();

        while ($row = $result->fetch_assoc()) {
            $returnArray[] = $row;
        }

        return $returnArray;
    }

    public function deletePost($uuid) {
        $sql = "DELETE FROM posts WHERE uuid=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("s", $uuid);
        $statement->execute();

        $returnValue = $statement->affected_rows;

        return $returnValue;
    }

    public function selectUsers($word, $username) {
        $returnArray = array();

        $sql = "SELECT id, username, email, fullname, ava FROM users WHERE NOT username ='".$username."' ";

        if (!empty($word)) {
            $sql .= "AND ( username LIKE ? OR fullname LIKE ? )";
        }

        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        if (!empty($word)) {
            $word = '%'.$word.'%';
            $statement->bind_param("ss", $word, $word);
        }

        $statement->execute();

        $result = $statement->get_result();

        while ($row = $result->fetch_assoc()) {
            $returnArray[] = $row;
        }

        return $returnArray;
    }

    public function updateUser($username, $fullname, $email, $id) {
        $sql = "UPDATE users SET username=?, fullname=?, email=? WHERE id=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement) { throw new Exception($statement->error); }

        $statement->bind_param("sssi", $username, $fullname, $email, $id);

        $returnValue = $statement->execute();

        return $returnValue;
    }

}