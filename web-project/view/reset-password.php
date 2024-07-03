<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
        <h2>Reset Password</h2>
        <form action="../controllers/reset-request-inc.php" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <br><br>
            <button type="submit" name="submit">Reset Password</button>
        </form>
        <?php
        if(isset($_GET["reset"])){
            if($_GET["reset"] == "success") {
                echo '<p class = "signupsuccess"> Check your email!</p>';
            }
            if($_GET["reset"] == "failure") {
                echo '<p class = "signupsuccess"> Procces failed! </p>';
            }            
        }
        ?>
    </div> 
</body>

</html>
