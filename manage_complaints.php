<?php
session_start();
header("Content-Type: application/json");

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? null;

if ($action === 'add') {
    // Add new complaint
    $name = $data['name'] ?? null;
    $title = $data['title'] ?? null;
    $description = $data['description'] ?? null;

    if (!$name || !$title || !$description) {
        http_response_code(400);
        echo json_encode(["error" => "All fields required"]);
        exit;
    }

    // ✅ Insert with status = 'active'
    $sql = "INSERT INTO complaints (user_id, name, title, description, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $_SESSION['user_id'], $name, $title, $description);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Complaint added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Insert failed: " . $conn->error]);
    }
    $stmt->close();

} elseif ($action === 'delete') {
    // Delete complaint
    $complaintId = $data['complaint_id'] ?? null;

    if (!$complaintId) {
        http_response_code(400);
        echo json_encode(["error" => "Complaint ID required"]);
        exit;
    }

    $sql = "DELETE FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaintId);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Complaint deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Delete failed: " . $conn->error]);
    }
    $stmt->close();
}

$conn->close();
?>