<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../model/dbh-inc.php';

function emptyInputSignup($firstName, $lastName, $email, $password, $cpassword){
    $result = false;
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($cpassword)) {
        $result = true;
    }
    return $result;
}

function invalidUid($firstName){
    $result = false;
    if (!preg_match("/^[a-zA-Z0-9]*$/", $firstName)){
        $result = true;
    }
    return $result; 
}

function pwdMatch($password, $cpassword){
    $result = false;
    if ($password !== $cpassword){
        $result = true;
    }
    return $result;     
}

function uidExists($conn, $email){
    $sql = "SELECT * FROM users WHERE usersEmail = :email";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':email', $email);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    return $row;
}

function createUser($conn, $firstName, $lastName, $email, $password){
    echo "Entering createUser function<br>";

    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (usersFirstName, usersLastName, usersEmail, usersPwd) VALUES (:firstName, :lastName, :email, :hashedPwd)";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        echo "Failed to prepare statement: " . $e['message'];
        return;
    }
    
    oci_bind_by_name($stmt, ':firstName', $firstName);
    oci_bind_by_name($stmt, ':lastName', $lastName);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':hashedPwd', $hashedPwd);
    echo "Parameters bound successfully<br>";
    
    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        echo "Failed to execute statement: " . $e['message'];
        return;
    }
    
    echo "Statement executed successfully<br>";
    oci_commit($conn);
    oci_free_statement($stmt);
    header("location: ../view/login.php?error=none");
    exit(); 
}

function emptyInputLogin($email, $pwd){
    $result = false;
    if (empty($email) || empty($pwd)){
        $result = true;
    }
    return $result;
}

function loginUser($conn, $email, $pwd){
    echo "Entering loginUser function<br>";

    $sql = "SELECT * FROM users WHERE usersEmail = :email";
    $stmt = oci_parse($conn, $sql);
    
    if (!$stmt) {
        $e = oci_error($conn);
        echo "Failed to prepare statement: " . $e['message'] . "<br>";
        return;
    }

    oci_bind_by_name($stmt, ':email', $email);
    echo "Parameters bound successfully<br>";

    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        echo "Failed to execute statement: " . $e['message'] . "<br>";
        return;
    }

    echo "Statement executed successfully<br>";

    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if ($row) {
        echo "User found<br>";
        $pwdHashed = $row['USERSPWD'];
        echo "Checking password<br>";

        if (password_verify($pwd, $pwdHashed)) {
            echo "Login successful<br>";

            session_start();
            $_SESSION["userID"] = $row["USERSID"];
            $_SESSION["userEmail"] = $row["USERSEMAIL"];
            header("location: ../index.php");
            exit();
        } else {
            echo "Wrong password<br>";
            header("location: ../view/login.php?error=wrongpassword");
            exit();
        }
    } else {
        echo "User not found<br>";
        header("location: ../view/login.php?error=usernotfound");
        exit();
    }
}

if (isset($_POST['signup'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    echo "First Name: $firstName<br>";
    echo "Last Name: $lastName<br>";
    echo "Email: $email<br>";

    createUser($conn, $firstName, $lastName, $email, $password);
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pwd = $_POST['password'];

    echo "Email: $email<br>";

    loginUser($conn, $email, $pwd);
}

if (isset($_POST['submit'])){  
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

    require_once '../model/dbh-inc.php';

    if (emptyInputSignup($firstName, $lastName, $email, $password, $cpassword) !== false) {
        header("location: ../view/signin.php?error=forgot.to.complete.something");
        exit();
    }
    if (invalidUid($firstName) !== false) {
        header("location: ../view/signin.php?error=invalidUid");
        exit(); 
    }   
    if (pwdMatch($password, $cpassword) !== false) {
        header("location: ../view/signin.php?error=pwddontmatch");
        exit(); 
    }

    if (uidExists($conn, $email)) {
        header("location: ../view/signin.php?error=userexists");
        exit(); 
    }

    createUser($conn, $firstName, $lastName, $email, $password);
}
?>
