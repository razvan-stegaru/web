<?php
session_start();
require_once '../controllers/post-inc.php';
require_once '../model/dbh-inc.php';

if (!isset($_SESSION['userID']) || !isset($_GET['postID'])) {
    header("Location: accountdetails.php");
    exit();
}

$postID = $_GET['postID'];
$post = getPostById($conn, $postID);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Post</title>
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
    </style>
</head>

<body>
<header>
    <nav class="navbar">
        <a href="../index.php">RESOURCE FINDER</a>
        <a href="support.php">What we do?</a>
    </nav>
</header>

<div class='all-container'>
    <div class='rightside'>
        <h2>Edit Post</h2>
        <div class='posts-section'>
            <form action="../controllers/post-inc.php" method="post">
                <input type="hidden" name="postID" value="<?php echo htmlspecialchars($postID); ?>">
                <input type="text" name="postName" value="<?php echo htmlspecialchars($post['POSTNAME']); ?>" required><br>
                <textarea name="postSubject" required><?php echo htmlspecialchars($post['POSTSUBJECT']); ?></textarea><br>
                <button type="submit">Update Post</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
