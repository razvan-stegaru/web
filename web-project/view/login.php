<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title class="title">login session</title>
    <link href="../css/styleLogin.css" rel="stylesheet" type="text/css">

 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <header>
        <nav class="navbar">
            <a href="../index.php">
                RESOURCE FINDER
              </a>
            <a href="support.php">What we do?</a> 
        </nav>
        
        <?php
        if(isset($_SESSION["userID"])){   
            echo "<div class=\"icons\">";
            echo "<a href=\"accountdetails.php\" class=\"fas fa-user\"></a>";
            echo "</div>";
        }
        ?>        
    </header>



    <section class="home" id="home">

        <form class="second" method="POST" action="../controllers/log-sign-inc.php">

            <h3>Login</h3>

            <div class="inputBox">
                <input type="email" id="email" name="email" placeholder="Email Address" required>
                <i class="validation"><span></span><span></span></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="validation"><span></span><span></span></i>
            </div>

            <input name="login" type="submit" value="submit" class="btn">

            <div class="inputBox">
                <p>
                    Not a member? <a href="signin.php">Create Account</a> <br>
                    Forgot password? <a href="reset-password.php">Press Here</a>
                </p>             
            </div>

            <?php
                if(isset($_GET["error"])){
                    if($_GET["error"] == "wronglogin1"){
                        echo "<p>Incorrect login information</p>";
                    }  
                    else if($_GET["error"] == "wronglogin2"){
                        echo "<p>Incorrect login information</p>";
                    } 
                }      
            ?>           
        </form>

    </section>

</html>
