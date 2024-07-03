<?php

include_once '../model/dbh-inc.php'; 
include_once '../model/PostModel.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postName']) && isset($_POST['postSubject'])) {
    $userID = $_SESSION['userID'];
    $postName = $_POST['postName'];
    $postSubject = $_POST['postSubject'];

 
    $result = addPost($conn, $userID, $postName, $postSubject);

    if ($result === true) {
      
        header("Location: ../accountdetails.php");
        exit();
    } else {
       
        echo "Error: " . $result;
    }
}
?>
