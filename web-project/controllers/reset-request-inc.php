<?php
if (isset($_POST["submit"])) {
    require '../model/dbh-inc.php';

    $userEmail = $_POST["email"];


    $sql = "SELECT * FROM users WHERE usersEmail = :email";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        echo "Failed to prepare statement: " . $e['message'];
        exit();
    }

    oci_bind_by_name($stmt, ':email', $userEmail);
    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);

    if (!$row) {
        header("Location: ../view/reset-password.php?reset=failure");
        exit();
    }

    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);
    $url = "https://test.onfundev.com/view/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);
    $expires = date("U") + 1800;


    $sql = "DELETE FROM pwdReset WHERE pwdResetEmail = :email";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        echo "Failed to prepare statement: " . $e['message'];
        exit();
    }

    oci_bind_by_name($stmt, ':email', $userEmail);
    oci_execute($stmt);

    $hashedToken = password_hash($token, PASSWORD_DEFAULT);
    $sql = "INSERT INTO pwdReset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (:email, :selector, :hashedToken, :expires)";
    $stmt = oci_parse($conn, $sql);
    
    if (!$stmt) {
        $e = oci_error($conn);
        echo "Failed to prepare statement: " . $e['message'];
        exit();
    }
    
    oci_bind_by_name($stmt, ':email', $userEmail);
    oci_bind_by_name($stmt, ':selector', $selector);
    oci_bind_by_name($stmt, ':hashedToken', $hashedToken);
    oci_bind_by_name($stmt, ':expires', $expires);
    oci_execute($stmt);

    $to = $userEmail;
    $subject = 'Reset your password for test.onfundev.com';
    $message = '<p>We received a password reset request. The link to reset your password is below. If you did not make this request, you can ignore this email.</p>';
    $message .= '<p>Here is your password reset link: </br>';
    $message .= '<a href="' . $url . '">' . $url . '</a></p>';

    $headers = "From: ResourceFinder <no-reply@test.onfundev.com>\r\n";
    $headers .= "Reply-To: no-reply@test.onfundev.com\r\n";
    $headers .= "Content-type: text/html\r\n";


    if (mail($to, $subject, $message, $headers)) {
        header("Location: ../view/reset-password.php?reset=success");
    } else {
        echo "Failed to send email.";
    }
} else {
    header("Location: ../view/reset-password.php");
}
?>
