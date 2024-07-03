<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title class="title">signin session</title>
    <link href="../css/styleSignin.css" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    
    <header>
        <nav class="navbar">
            <a href="../index.php"> RESOURCE FINDER </a>
            <a href="support.php">What we do?</a>
        </nav>

        <?php
        if(isset($_SESSION["userID"])){    
            echo "<div class=\"icons\">";
            echo "<a href=\"accountdetails.php\" class=\"fas fa-user\"></a>";
            echo "</div>";
        }
        else {
            echo "<div class=\"login-signin\">";  
            echo "<div class=\"login\">";
            echo "<a href=\"login.php\">Log in</a>";
            echo "</div>";
        }
        ?>
    </header>



    <section class="home" id="home">

        <form class="first" action="../controllers/log-sign-inc.php" method="post">

            <h3>Sign In</h3>

            <div class="inputBox">
                <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                <i class="validation"><span></span><span></span></i>
                <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                <i class="validation"><span></span><span></span></i>
                <input type="email" id="email" name="email" placeholder="Email Address" required>
                <i class="validation"><span></span><span></span></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="validation"><span></span><span></span></i>
                <input type="password" id="cpassword" name="cpassword" placeholder="Repeat Password" required>
                <i class="validation"><span></span><span></span></i>
            </div>

            <input type="submit" value="submit" name="submit" class="btn">

            <?php
                if(isset($_GET["error"])){
                    if($_GET["error"] == "stmtfailed2"){
                        echo "<p>Something went wrong</p>";
                    }
                    else if($_GET["error"] == "stmtfailed1"){
                        echo "<p>Something went wrong</p>";
                    }    
                    else if ($_GET["error"] == "mailtakenalready"){
                        echo "<p>Choose another mail, already taken</p>";
                    }    
                    else if ($_GET["error"] == "forgot.to.complete.something"){
                        echo "<p>Check fields again, something is missing</p>";
                    } 
                    else if ($_GET["error"] == "invalidUid"){
                        echo "<p>First name contains forbidden symbols</p>";
                    } 
                    else if ($_GET["error"] == "pwddontmatch"){
                        echo "<p>Passwords don't match</p>";
                    }                 
                }
            ?>
        </form>
    </section>

</html>
