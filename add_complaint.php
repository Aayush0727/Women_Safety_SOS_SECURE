<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
header("Content-Type: application/json");

try {
    // ✅ CSRF TOKEN VALIDATION
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(["error" => "CSRF token validation failed"]);
        exit;
    }

    require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Database Connection failed: " . $conn->connect_error]);
        exit;
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["error" => "User not logged in"]);
        exit;
    }

    // ✅ INPUT VALIDATION & SANITIZATION
    if (!isset($_POST['complaintName']) || !isset($_POST['complaintTitle']) || !isset($_POST['complaintDescription'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    $name = trim($_POST['complaintName']);
    $title = trim($_POST['complaintTitle']);
    $description = trim($_POST['complaintDescription']);

    // Validate name length
    if (empty($name) || strlen($name) < 2 || strlen($name) > 100) {
        http_response_code(400);
        echo json_encode(["error" => "Name must be 2-100 characters"]);
        exit;
    }

    // Validate title length
    if (empty($title) || strlen($title) < 5 || strlen($title) > 200) {
        http_response_code(400);
        echo json_encode(["error" => "Title must be 5-200 characters"]);
        exit;
    }

    // Validate description length
    if (empty($description) || strlen($description) < 10 || strlen($description) > 2000) {
        http_response_code(400);
        echo json_encode(["error" => "Description must be 10-2000 characters"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $timestamp = date('Y-m-d H:i:s');

    // ✅ CHECK IF COMPLAINTS TABLE EXISTS
    $check_table = "SHOW TABLES LIKE 'complaints'";
    $result = $conn->query($check_table);
    
    if ($result->num_rows === 0) {
        http_response_code(500);
        echo json_encode(["error" => "Complaints table does not exist. Please create it first."]);
        exit;
    }

    // ✅ INSERT COMPLAINT WITH PROPER ERROR HANDLING
    $insert_sql = "INSERT INTO complaints (user_id, name, title, description, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Database prepare error: " . $conn->error]);
        exit;
    }

    if (!$stmt->bind_param("issss", $user_id, $name, $title, $description, $timestamp)) {
        http_response_code(500);
        echo json_encode(["error" => "Bind param error: " . $stmt->error]);
        exit;
    }

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Execute error: " . $stmt->error]);
        exit;
    }

    http_response_code(201);
    echo json_encode(["success" => "Complaint submitted successfully!"]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Exception: " . $e->getMessage()]);
}
?>
