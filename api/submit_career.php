<?php
// ✅ Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

// ✅ Handle preflight request
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

// ✅ Get form data
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$mail = $_POST['mail'] ?? '';
$role = $_POST['role'] ?? '';
$phone = $_POST['phone'] ?? '';
$experience = $_POST['experience'] ?? '';

// ✅ Handle file upload
$upload_dir = '../uploads/cv/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$cv_path = '';
if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
    $temp_name = $_FILES['cv']['tmp_name'];
    $file_name = time() . '_' . basename($_FILES['cv']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($temp_name, $target_file)) {
        $cv_path = 'uploads/cv/' . $file_name;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'CV file is required or upload error.']);
    exit;
}

// ✅ Insert into database using table "career"
$query = $conn->prepare("INSERT INTO career (first_name, last_name, email, role, phone_number, attachment, experience) VALUES (?, ?, ?, ?, ?, ?, ?)");
$query->bind_param("sssssss", $first_name, $last_name, $mail, $role, $phone, $cv_path, $experience);

if ($query->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error inserting data: ' . $query->error
    ]);
}