<?php
if (isset($_GET["selector"]) && isset($_GET["validator"])) {
    $selector = $_GET["selector"];
    $validator = $_GET["validator"];

    if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Create New Password</title>
            <style>
                body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    padding: 0;
                    background-color: #285943;
                }

                .container {
                    border: 2px solid #ffffff;
                    border-radius: 10px;
                    padding: 20px;
                    text-align: center;
                }

                h2, form {
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Create New Password</h2>
                <form action="../controllers/reset-password-inc.php" method="post">
                    <input type="hidden" name="selector" value="<?php echo $selector; ?>">
                    <input type="hidden" name="validator" value="<?php echo $validator; ?>">
                    <input type="password" name="pwd" placeholder="Enter a new password..." required>
                    <br><br>
                    <input type="password" name="pwd-repeat" placeholder="Repeat new password..." required>
                    <br><br>
                    <button type="submit" name="reset-password-submit">Reset Password</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Could not validate your request!";
    }
} else {
    echo "Could not validate your request!";
}
?>
