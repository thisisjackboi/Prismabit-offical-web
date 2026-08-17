<?php


// CORS headers (optional if frontend is on same domain)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'config.php';



// ✅ Check request method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$firstname = $_POST['firstname'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$businessname = $_POST['businessname'] ?? '';
$email = $_POST['email'] ?? '';

$weburl = $_POST['weburl'] ?? '';
$message = $_POST['message'] ?? '';


$query = $conn->prepare("INSERT INTO audit (first_name, last_name, business_name, email, website_url, message) VALUES (?, ?, ?, ?, ?, ?)");
$query->bind_param("ssssss", $firstname, $lastname, $businessname, $email, $weburl, $message);
if ($query->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Data inserted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error inserting data: ' . $query->error
    ]);
}
