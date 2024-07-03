<?php
require_once '../model/dbh-inc.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['postName']) && isset($_POST['postSubject']) && !isset($_POST['postID'])) {

        $userID = $_SESSION['userID'];
        $postName = $_POST['postName'];
        $postSubject = $_POST['postSubject'];

        $result = createPost($conn, $userID, $postName, $postSubject);

        if ($result === true) {
            header("Location: ../view/accountdetails.php");
            exit();
        } else {
            echo "Error: " . $result;
        }
    } elseif (isset($_POST['postName']) && isset($_POST['postSubject']) && isset($_POST['postID'])) {

        $postID = $_POST['postID'];
        $postName = $_POST['postName'];
        $postSubject = $_POST['postSubject'];

        $result = updatePost($conn, $postID, $postName, $postSubject);

        if ($result === true) {
            header("Location: ../view/accountdetails.php");
            exit();
        } else {
            echo "Error: " . $result;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $postID = $_GET['delete'];

    $result = deletePost($conn, $postID);

    if ($result === true) {
        header("Location: ../view/accountdetails.php");
        exit();
    } else {
        echo "Error: " . $result;
    }
}



function getPostsCategory($conn, $category) {
    $sql = "SELECT * FROM posts WHERE postSubject = :category";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        throw new Exception("Failed to prepare statement: " . $e['message']);
    }

    oci_bind_by_name($stmt, ':category', $category);
    oci_execute($stmt);

    $resultData = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $resultData[] = $row;
    }

    oci_free_statement($stmt);
    return $resultData;
}

function getPostsUID($conn, $userID) {
    $sql = "SELECT * FROM posts WHERE usersID = :userID";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        throw new Exception("Failed to prepare statement: " . $e['message']);
    }

    oci_bind_by_name($stmt, ':userID', $userID);
    oci_execute($stmt);

    $resultData = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $resultData[] = $row;
    }

    oci_free_statement($stmt);
    return $resultData;
}

function getPostsSearch($conn, $subject) {
    $sql = "SELECT * FROM posts WHERE postName LIKE :subject";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        throw new Exception("Failed to prepare statement: " . $e['message']);
    }

    $searchTerm = '%' . $subject . '%';
    oci_bind_by_name($stmt, ':subject', $searchTerm);
    oci_execute($stmt);

    $resultData = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $resultData[] = $row;
    }

    oci_free_statement($stmt);
    return $resultData;
}

function createPost($conn, $userID, $postName, $postSubject) {
    $sql = "INSERT INTO posts (usersID, postName, postSubject) VALUES (:userID, :postName, :postSubject)";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        throw new Exception("Failed to prepare statement: " . $e['message']);
    }

    oci_bind_by_name($stmt, ':userID', $userID);
    oci_bind_by_name($stmt, ':postName', $postName);
    oci_bind_by_name($stmt, ':postSubject', $postSubject);

    if (oci_execute($stmt)) {
        oci_commit($conn);
        return true;
    } else {
        $e = oci_error($stmt);
        return "Error: " . $e['message'];
    }

    oci_free_statement($stmt);
}

function getPostById($conn, $postID) {
    $sql = "SELECT * FROM posts WHERE postID = :postID";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        throw new Exception("Failed to prepare statement: " . $e['message']);
    }

    oci_bind_by_name($stmt, ':postID', $postID);
    oci_execute($stmt);

    $post = oci_fetch_assoc($stmt);

    oci_free_statement($stmt);
    return $post;
}

function updatePost($conn, $postID, $postName, $postSubject) {
    $sql = "UPDATE posts SET postName = :postName, postSubject = :postSubject WHERE postID = :postID";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        throw new Exception("Failed to prepare statement: " . $e['message']);
    }

    oci_bind_by_name($stmt, ':postName', $postName);
    oci_bind_by_name($stmt, ':postSubject', $postSubject);
    oci_bind_by_name($stmt, ':postID', $postID);

    if (oci_execute($stmt)) {
        oci_commit($conn);
        return true;
    } else {
        $e = oci_error($stmt);
        return "Error: " . $e['message'];
    }

    oci_free_statement($stmt);
}

function deletePost($conn, $postID) {
    $sql = "DELETE FROM posts WHERE postID = :postID";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        throw new Exception("Failed to prepare statement: " . $e['message']);
    }

    oci_bind_by_name($stmt, ':postID', $postID);

    if (oci_execute($stmt)) {
        oci_commit($conn);
        return true;
    } else {
        $e = oci_error($stmt);
        return "Error: " . $e['message'];
    }

    oci_free_statement($stmt);
}
?>
