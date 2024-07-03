<?php

include_once 'dbh-inc.php';

function addPost($conn, $userID, $postName, $postSubject) {
    $sql = "INSERT INTO posts (usersID, postID, postName, postSubject) 
            VALUES (:userID, posts_seq.NEXTVAL, :postName, :postSubject)";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":userID", $userID);
    oci_bind_by_name($stmt, ":postName", $postName);
    oci_bind_by_name($stmt, ":postSubject", $postSubject);

    if (oci_execute($stmt, OCI_COMMIT_ON_SUCCESS)) {
        oci_free_statement($stmt);
        return true; 
    } else {
        $e = oci_error($stmt);
        oci_free_statement($stmt);
        return "Error: " . $e['message'];
    }
}

function getPosts($conn) {
    $sql = "SELECT * FROM posts";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $posts = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $posts[] = $row;
    }
    oci_free_statement($stmt);
    return $posts;
}

function getPostById($conn, $postID) {
    $sql = "SELECT * FROM posts WHERE postID = :postID";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':postID', $postID);
    oci_execute($stmt);

    $post = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    return $post;
}

function updatePost($conn, $postID, $postName, $postSubject) {
    $sql = "UPDATE posts SET postName = :postName, postSubject = :postSubject WHERE postID = :postID";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':postID', $postID);
    oci_bind_by_name($stmt, ':postName', $postName);
    oci_bind_by_name($stmt, ':postSubject', $postSubject);

    if (oci_execute($stmt, OCI_COMMIT_ON_SUCCESS)) {
        oci_free_statement($stmt);
        return true; 
    } else {
        $e = oci_error($stmt);
        oci_free_statement($stmt);
        return "Error: " . $e['message'];
    }
}

function deletePost($conn, $postID) {
    $sql = "DELETE FROM posts WHERE postID = :postID";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':postID', $postID);

    if (oci_execute($stmt, OCI_COMMIT_ON_SUCCESS)) {
        oci_free_statement($stmt);
        return true; 
    } else {
        $e = oci_error($stmt);
        oci_free_statement($stmt);
        return "Error: " . $e['message'];
    }
}
?>
