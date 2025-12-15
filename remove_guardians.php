<?php
session_start();
header("Content-Type: application/json");

// ✅ CSRF TOKEN VALIDATION
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(["error" => "CSRF token validation failed"]);
    exit;
}

require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

// ✅ INPUT VALIDATION
if (!isset($_POST['guardianId'])) {
    echo json_encode(["error" => "Missing guardian ID"]);
    exit;
}

$guardian_id = intval($_POST['guardianId']);
$user_id = $_SESSION['user_id'];

// Delete guardian (verify ownership)
$delete_sql = "DELETE FROM guardians WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($delete_sql);
$stmt->bind_param("ii", $guardian_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => "Guardian removed successfully!"]);
    } else {
        echo json_encode(["error" => "Guardian not found or unauthorized"]);
    }
} else {
    echo json_encode(["error" => "Error removing guardian: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
