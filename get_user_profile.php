<?php
session_start();
header("Content-Type: application/json");

// Check if user is logged in
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

$user_id = $_SESSION['user_id'];

// ✅ FETCH USER DATA
$sql = "SELECT id, first_name, last_name, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
    exit;
}

$user = $result->fetch_assoc();

// ✅ RETURN USER DATA WITH CORRECT FIELD NAMES
echo json_encode([
    "id" => $user['id'],
    "first_name" => $user['first_name'],
    "last_name" => $user['last_name'],
    "name" => $user['first_name'] . ' ' . $user['last_name'],  // Combined name
    "email" => $user['email'],
    "phone" => $user['phone']
]);

$stmt->close();
$conn->close();
?>
