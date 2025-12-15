<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header FIRST before anything else
header("Content-Type: application/json");
session_start();

try {
    // ✅ CSRF TOKEN VALIDATION
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "CSRF token validation failed"]);
        exit;
    }

   require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "DB Connection: " . $conn->connect_error]);
        exit;
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit;
    }

    // ✅ INPUT VALIDATION
    if (!isset($_POST['location']) || trim($_POST['location']) === '') {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Location is required"]);
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $location = trim($_POST['location']);
    $timestamp = date('Y-m-d H:i:s');

    // ✅ GET USER DETAILS (for email variables)
    $user_sql = "SELECT first_name, last_name, email FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    
    if (!$user_stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "User query failed: " . $conn->error]);
        exit;
    }

    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    $user_stmt->close();

    if (!$user_data) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit;
    }

    $first_name = $user_data['first_name'];
    $last_name = $user_data['last_name'];
    $email = $user_data['email'];

    // ✅ INSERT SOS ALERT WITH ALL COLUMNS
    $insert_sql = "INSERT INTO sos_alerts (user_id, first_name, last_name, location, email, timestamp) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Insert prepare failed: " . $conn->error]);
        exit;
    }

    if (!$stmt->bind_param("isssss", $user_id, $first_name, $last_name, $location, $email, $timestamp)) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Bind failed: " . $stmt->error]);
        exit;
    }

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "🚨 SOS Alert Sent & Recorded!",
        "user_id" => $user_id,
        "first_name" => $first_name,
        "last_name" => $last_name,
        "email" => $email,
        "location" => $location,
        "timestamp" => $timestamp
    ]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Exception: " . $e->getMessage()]);
}
?>
