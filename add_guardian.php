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

// Get user ID from session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

// ✅ INPUT VALIDATION
if (!isset($_POST['guardianName'], $_POST['guardianEmail'], $_POST['guardianContact'])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$guardian_name = trim($_POST['guardianName']);
$guardian_email = filter_var(trim($_POST['guardianEmail']), FILTER_VALIDATE_EMAIL);
$guardian_contact = trim($_POST['guardianContact']);

if (!$guardian_email || empty($guardian_name) || !preg_match('/^[0-9]{10}$/', $guardian_contact)) {
    echo json_encode(["error" => "Invalid input format"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Insert guardian
$insert_sql = "INSERT INTO guardians (user_id, name, email, contact) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param("isss", $user_id, $guardian_name, $guardian_email, $guardian_contact);

if ($stmt->execute()) {
    echo json_encode(["success" => "Guardian added successfully!"]);
} else {
    echo json_encode(["error" => "Error adding guardian: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
