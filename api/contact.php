<?php
// ✅ Allow CORS (must be before any output)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

// ✅ Handle preflight request (important for browser CORS checks)
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
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';




$query = $conn->prepare("INSERT INTO contact (first_name, last_name, email, message) VALUES (?, ?, ?, ?)");
$query->bind_param("ssss", $firstname, $lastname,  $email,  $message);
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
