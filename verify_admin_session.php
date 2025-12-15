<?php
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Check if user is admin (adjust column name based on your database)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized', 'isAdmin' => false]);
    exit;
}

// ✅ Verified Admin
echo json_encode(['success' => true, 'isAdmin' => true]);
?>
