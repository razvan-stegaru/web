<?php

if (isset($_POST["change-email-submit"])) {
    require '../model/dbh-inc.php'; 


    $userID = $_SESSION["userID"]; 
    $newEmail = $_POST["newEmail"]; 


    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../view/change-email.php?error=invalidemail");
        exit();
    }

    $sql = "SELECT * FROM users WHERE usersEmail = :newEmail";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        echo "Failed to prepare statement: " . $e['message'];
        exit();
    }

    oci_bind_by_name($stmt, ':newEmail', $newEmail);
    oci_execute($stmt);

    if (oci_fetch($stmt)) {
        header("Location: ../view/change-email.php?error=emailtaken");
        exit();
    }

    $sqlUpdate = "UPDATE users SET usersEmail = :newEmail WHERE usersID = :userID";
    $stmtUpdate = oci_parse($conn, $sqlUpdate);

    if (!$stmtUpdate) {
        $e = oci_error($conn);
        echo "Failed to prepare update statement: " . $e['message'];
        exit();
    }

    oci_bind_by_name($stmtUpdate, ':newEmail', $newEmail);
    oci_bind_by_name($stmtUpdate, ':userID', $userID);

    if (oci_execute($stmtUpdate)) {
        oci_commit($conn);
        $_SESSION["userEmail"] = $newEmail; 
        header("Location: ../view/accountdetails.php?change=emailsuccess");
        exit();
    } else {
        $e = oci_error($stmtUpdate);
        echo "Failed to update email: " . $e['message'];
        exit();
    }

    oci_free_statement($stmtUpdate);
    oci_close($conn); 
} else {
    header("Location: ../view/index.php");
    exit();
}
?>
