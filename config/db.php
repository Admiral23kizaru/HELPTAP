<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'helptap_db';

// Create connection using mysqli (compatible with PHP 5.0+)
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");

// Define a function to safely escape strings
function escape_string($string) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($string));
}

// Define a function to safely execute queries
function execute_query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    return $result;
}

// Define a function to get a single row
function get_row($sql) {
    $result = execute_query($sql);
    return mysqli_fetch_assoc($result);
}

// Define a function to get multiple rows
function get_rows($sql) {
    $result = execute_query($sql);
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
?> 