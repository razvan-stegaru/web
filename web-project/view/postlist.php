<?php
session_start();
require_once '../utils/TextProcessingService.php';
require_once '../model/dbh-inc.php';
require_once '../controllers/post-inc.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : '0';

if (isset($_GET['category'])) {
    $resultData = getPostsCategory($conn, $_GET['category']);
} else if (isset($_GET['subject'])) {
    $resultData = getPostsSearch($conn, $_GET['subject']);
}

// Fetch GitHub repositories and web results
$githubResults = [];
$webResults = [];

if (isset($_GET['subject'])) {
    $githubResults = TextProcessingService::fetchGitHubRepositories($_GET['subject']);
    $webResults = TextProcessingService::fetchWebResults($_GET['subject']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter</title>
    <link href="../css/stylepagePostList.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .posts-container ul li {
            font-size: 1.8rem;
            line-height: 2.5rem;
            color: #ffffff;
            margin-bottom: 1.5rem;
        }

        .posts-container ul li a {
            color: #ff0000;
            text-decoration: none;
        }

        .posts-container ul li a:hover {
            text-decoration: underline;
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
        }
        ?>
    </header>

    <div class="show-option">
        <?php
        if (isset($_GET['category'])) {
            echo "<p>Articles about: " . htmlspecialchars($_GET['category']) . "</p>";
        } else if (isset($_GET['subject'])) {
            echo "<p>Results for searching " . htmlspecialchars($_GET['subject']) . "</p>";
        }
        ?>
    </div>

    <div class="interactive">
        <div class="posts-container">
            <h2>Posts</h2>
            <ul>
                <?php
                if (!empty($resultData)) {
                    foreach ($resultData as $post) {
                        echo "<li class='post-item'>";
                        echo "<h3>" . htmlspecialchars($post['POSTNAME']) . "</h3>";
                        echo "<p>" . htmlspecialchars($post['POSTSUBJECT']) . "</p>";
                        echo "</li>";
                    }
                } else {
                    echo "<li class='post-item'>No posts found.</li>";
                }
                ?>
            </ul>
            
            <h2>GitHub Repositories</h2>
            <ul>
                <?php
                if (!empty($githubResults)) {
                    foreach ($githubResults as $repo) {
                        echo "<li class='github-results'><a href='" . htmlspecialchars($repo['html_url']) . "' target='_blank'>" . htmlspecialchars($repo['full_name']) . "</a>: " . htmlspecialchars($repo['description']) . "</li>";
                    }
                } else {
                    echo "<li class='github-results'>No GitHub repositories found.</li>";
                }
                ?>
            </ul>

            <h2>Web Results</h2>
            <ul>
                <?php
                if (!empty($webResults)) {
                    foreach ($webResults as $result) {
                        echo "<li class='web-results'><a href='" . htmlspecialchars($result['link']) . "' target='_blank'>" . htmlspecialchars($result['title']) . "</a></li>";
                    }
                } else {
                    echo "<li class='web-results'>No web results found.</li>";
                }
                ?>
            </ul>
        </div>

        <div class="filters">
            <h4>Filters</h4>
            <form action="postlist.php" method="get">
                <?php if (isset($_GET['category'])): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
                <?php endif; ?>
                <?php if (isset($_GET['subject'])): ?>
                    <input type="hidden" name="subject" value="<?php echo htmlspecialchars($_GET['subject']); ?>">
                <?php endif; ?>

                <select class="rating" name="filter" onchange="this.form.submit()">
                    <option value="0"<?php if ($filter === '0') echo ' selected'; ?>>All</option>
                    <option value="1"<?php if ($filter === '1') echo ' selected'; ?>>Oldest to Newest</option>
                    <option value="2"<?php if ($filter === '2') echo ' selected'; ?>>Recent</option>
                    <option value="3"<?php if ($filter === '3') echo ' selected'; ?>>Most Popular</option>
                </select>
            </form>
        </div>
    </div>
</body>

</html>
