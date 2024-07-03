<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title class="title">About us</title>
    <link href="../css/styleSupport.css" rel="stylesheet" type="text/css">

  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    
        .home {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 100vh;
        }

        
        .home h2,
        .home p,
        .home ul,
        .home li {
            margin-left: 20px;
            margin-right: 20px;
        }
    </style>
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
        if (isset($_SESSION["userID"])) {
            echo "<div class=\"icons\">";
            echo "<a href=\"accountdetails.php\" class=\"fas fa-user\"></a>";
            echo "</div>";
        } else {
            echo "<div class=\"login-signin\">";
            echo "<div class=\"login\">";
            echo "<a href=\"login.php\">Log in</a>";
            echo "</div>";
            echo "<div class=\"signin\">";
            echo "<a href=\"signin.php\">Sign up</a>";
            echo "</div>";
            echo "</div>";
        }
        ?>
    </header>
 

    <section class="home" id="home">
    <div>
        <ol>
            <li>
                <h1>What is Resource Finder?</h1>
                <p>Resource Finder is a platform designed to assist developers in the realm of creative programming, offering a curated collection of resources, tutorials, and tools tailored to various creative coding endeavors.</p>
            </li>
            <li>
                <h1>What types of resources are available on Resource Finder?</h1>
                <p>Resource Finder provides access to a wide range of resources, including specialized websites, tutorials, multimedia presentations, source code repositories, and software projects relevant to creative programming.</p>
            </li>
            <li>
                <h1>How does Resource Finder help developers find relevant resources?</h1>
                <p>Developers can utilize search and filtering functionalities to discover resources based on specific criteria such as programming languages (e.g., JavaScript, WebGL, WebAssembly), desired outcomes (e.g., 2D illustration, 3D virtual worlds, animation, sound content).</p>
            </li>
            <li>
                <h1>How does the recommendation system work?</h1>
                <p>The recommendation system analyzes the user's stated purpose or preferences, such as "I want to create landscapes using fractals" or "I need a polyphonic generator," to suggest existing software solutions, frameworks, libraries, and components necessary to achieve their goals.</p>
            </li>
            <li>
                <h1>Can I specify my preferences for programming languages or implementations?</h1>
                <p>Yes, users can specify their preferences, such as "only JavaScript/TypeScript implementations," to tailor the recommendations provided by the system.</p>
            </li>

            <li>
                <h1>Is Resource Finder suitable for beginners?</h1>
                <p>Absolutely! Resource Finder caters to developers of all skill levels, offering resources and tutorials ranging from introductory to advanced topics in creative programming.</p>
            </li>

            <li>
                <h1>Is Resource Finder free to use?</h1>
                <p>Yes, Resource Finder is freely accessible to all users. There are no subscription fees or charges for accessing the platform's resources and functionalities.</p>
            </li>
            <li>
                <h1>How often are new resources added to Resource Finder?</h1>
                <p>Be sure to check back regularly for updates and additions to the platform.</p>
            </li>
        </ol>
    </div>
</section>

</html>
