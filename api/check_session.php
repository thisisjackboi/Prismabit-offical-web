<?php
session_start();
header("Content-Type: application/json");

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => true,
        'loggedIn' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'role_slug' => $_SESSION['role_slug']
        ]
    ]);
} else {
    echo json_encode(['success' => true, 'loggedIn' => false]);
}
?>
