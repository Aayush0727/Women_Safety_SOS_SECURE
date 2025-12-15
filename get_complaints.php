<?php
session_start();
header("Content-Type: application/json");

// Check if user is logged in (admin)
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

// ✅ FETCH ALL COMPLAINTS (Not just for current user)
$sql = "SELECT id, name, title, description, created_at FROM complaints ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}
$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}
echo json_encode($complaints);
$conn->close();
?>