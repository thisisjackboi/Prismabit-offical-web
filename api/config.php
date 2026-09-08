<?php
// Database configuration
ini_set('display_errors', 1);
error_reporting(E_ALL);


// $host = getenv('DB_HOST') ?: "localhost";
// $username = getenv('DB_USER') ?: "prismabit.co.in";
// $password = getenv('DB_PASS') ?: "P@55w0rd";
// $database = getenv('DB_NAME') ?: "crm_prismabit";

$host = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";
$database = getenv('DB_NAME') ?: "crm_prismabit";

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Optional: set charset to avoid encoding issues
$conn->set_charset("utf8mb4");
