<?php
// Database configuration
ini_set('display_errors', 1);
error_reporting(E_ALL);


$host = "localhost";        // Database host (e.g., localhost or server IP)
$username = "prismabit.co.in";         // MySQL username
$password = "P@55w0rd";             // MySQL password
$database = "crm_prismabit"; // Name of the database

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Optional: set charset to avoid encoding issues
$conn->set_charset("utf8mb4");


