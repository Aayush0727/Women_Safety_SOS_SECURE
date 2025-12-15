<?php
session_start();
require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database Connection Failed"]));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

$query = "SELECT id, name, email, contact FROM guardians WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$guardians = [];
while ($row = $result->fetch_assoc()) {
    $guardians[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($guardians);
?>
