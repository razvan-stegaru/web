<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

$dBUsername = "web";
$dBPassword = "web";
$dBName = "localhost/XE";

global $conn;
$conn = oci_connect($dBUsername, $dBPassword, $dBName);
if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
    exit;
}