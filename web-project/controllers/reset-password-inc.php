<?php
if (isset($_POST["reset-password-submit"])) {
    require '../model/dbh-inc.php';

    $selector = $_POST["selector"];
    $validator = $_POST["validator"];
    $password = $_POST["pwd"];
    $passwordRepeat = $_POST["pwd-repeat"];

    if (empty($password) || empty($passwordRepeat)) {
        header("Location: ../view/create-new-password.php?selector=" . $selector . "&validator=" . $validator . "&newpwd=empty");
        exit();
    } else if ($password != $passwordRepeat) {
        header("Location: ../view/create-new-password.php?selector=" . $selector . "&validator=" . $validator . "&newpwd=pwdnotsame");
        exit();
    }

    $currentDate = date("U");

    $sql = "SELECT * FROM pwdReset WHERE pwdResetSelector = :selector AND pwdResetExpires >= :currentDate";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        echo "Failed to prepare statement: " . $e['message'];
        exit();
    }

    oci_bind_by_name($stmt, ':selector', $selector);
    oci_bind_by_name($stmt, ':currentDate', $currentDate);

    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$row) {
        echo "You need to re-submit your reset request.";
        exit();
    } else {
        $tokenBin = hex2bin($validator);
        $tokenCheck = password_verify($tokenBin, $row["PWDRESETTOKEN"]);

        if ($tokenCheck === false) {
            echo "You need to re-submit your reset request.";
            exit();
        } elseif ($tokenCheck === true) {
            $tokenEmail = $row['PWDRESETEMAIL'];

            $sql = "SELECT * FROM users WHERE usersEmail = :email";
            $stmt = oci_parse($conn, $sql);

            if (!$stmt) {
                $e = oci_error($conn);
                echo "Failed to prepare statement: " . $e['message'];
                exit();
            }

            oci_bind_by_name($stmt, ':email', $tokenEmail);
            oci_execute($stmt);

            $row = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);

            if (!$row) {
                echo "There was an error!";
                exit();
            } else {
                $sql = "UPDATE users SET usersPwd = :password WHERE usersEmail = :email";
                $stmt = oci_parse($conn, $sql);

                if (!$stmt) {
                    $e = oci_error($conn);
                    echo "Failed to prepare statement: " . $e['message'];
                    exit();
                }

                $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
                oci_bind_by_name($stmt, ':password', $newPwdHash);
                oci_bind_by_name($stmt, ':email', $tokenEmail);

                if (oci_execute($stmt)) {
                    oci_commit($conn);
                } else {
                    $e = oci_error($stmt);
                    echo "Failed to execute statement: " . $e['message'];
                    exit();
                }

                oci_free_statement($stmt);

                $sql = "DELETE FROM pwdReset WHERE pwdResetEmail = :email";
                $stmt = oci_parse($conn, $sql);

                if (!$stmt) {
                    $e = oci_error($conn);
                    echo "Failed to prepare statement: " . $e['message'];
                    exit();
                }

                oci_bind_by_name($stmt, ':email', $tokenEmail);

                if (oci_execute($stmt)) {
                    oci_commit($conn);
                } else {
                    $e = oci_error($stmt);
                    echo "Failed to execute statement: " . $e['message'];
                    exit();
                }

                oci_free_statement($stmt);

                header("Location: ../view/login.php?newpwd=passwordupdated");
            }
        }
    }
} else {
    header("Location: ../view/index.php");
}
?>
