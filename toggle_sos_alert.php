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

// Get alert ID and new status
$data = json_decode(file_get_contents("php://input"), true);
$alertId = $data['alert_id'] ?? null;
$newStatus = $data['status'] ?? null;

if (!$alertId || !$newStatus) {
    http_response_code(400);
    echo json_encode(["error" => "Alert ID and status required"]);
    exit;
}

// Update SOS alert status
$sql = "UPDATE sos_alerts SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $newStatus, $alertId);

if ($stmt->execute()) {
    echo json_encode(["success" => "Alert status updated to: " . $newStatus]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Update failed: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>