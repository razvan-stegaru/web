<?php
session_start();
require_once '../controllers/post-inc.php';
require_once '../model/dbh-inc.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User account</title>
    <link href="../css/styleaccountDetails.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .button-container button {
            margin: 0 10px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #285943;
            color: #dfffd5;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button-container button:hover {
            background-color: #8CD790;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
</head>

<body>

<header>
    <nav class="navbar">
        <a href="../index.php">RESOURCE FINDER</a>
        <a href="support.php">What we do?</a>
    </nav>

    <?php
    if (isset($_SESSION["userID"])) {
        echo "<div class=\"login\">";
        echo "<a id=\"createPostBtn\">Create New Post</a>";
        echo "</div>";
    }
    ?>
</header>

<div class='all-container'>
    <div class='leftside'>
        <div class='search-section'>
            <form class='search-bar' action='accountdetails.php' method='get'>
                <input type="text" name="subject" placeholder=" check in history" required>
                <button type="submit">Search</button>
            </form>
            <div class="button-container">
                <button onclick="window.location.href='../controllers/logout-inc.php'">Logout</button>
                <button onclick="window.location.href='reset-password.php'">Change Password</button>
                <button onclick="window.location.href='pagetobeimplemented.php'">Change Email</button>
            </div>
        </div>
    </div>

    <div class='rightside'>
        <h2> Review your own searches and stay up to date with your progress! </h2>
        <div class='posts-section'>
            <?php
            if (!isset($_GET['subject'])) {
                $resultData = getPostsUID($conn, $_SESSION['userID']);
            } else {
                $subject = $_GET['subject'];
                $resultData = getPostsSearchUID($conn, $subject, $_SESSION['userID']);
            }
            if (is_array($resultData) && !empty($resultData)) {
                foreach ($resultData as $post) {
                    $postName = isset($post['POSTNAME']) ? htmlspecialchars($post['POSTNAME']) : 'No Title';
                    $postSubject = isset($post['POSTSUBJECT']) ? htmlspecialchars($post['POSTSUBJECT']) : 'No Subject';
                    $postID = isset($post['POSTID']) ? htmlspecialchars($post['POSTID']) : '';
                    echo "<div class='post'>";
                    echo "<h3>" . $postName . "</h3>";
                    echo "<p>" . $postSubject . "</p>";
                    if ($postID) {
                        echo "<a href='editpost.php?postID=" . $postID . "'>Edit</a> | ";
                        echo "<a href='../controllers/post-inc.php?delete=" . $postID . "' onclick=\"return confirm('Are you sure you want to delete this post?');\">Delete</a>";
                    }
                    echo "</div>";
                }
            } else {
                echo "No posts found for the current user.";
            }
            ?>
        </div>
    </div>
</div>

<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Create New Post</h2>
        <form action="../controllers/post-inc.php" method="post">
            <input type="text" name="postName" placeholder="Post Name" required><br>
            <textarea name="postSubject" placeholder="Post Subject" required></textarea><br>
            <button type="submit">Post</button>
        </form>
    </div>
</div>

<script>
    
    var modal = document.getElementById("myModal");

   
    var btn = document.getElementById("createPostBtn");

  
    var span = document.getElementsByClassName("close")[0];

   
    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }


    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>

</html>
