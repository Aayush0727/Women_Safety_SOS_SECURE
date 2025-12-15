<?php
header('Content-Type: application/json');

require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Fetch registered users (excluding admins)
$sql = "SELECT first_name, last_name, email, phone FROM users WHERE role = 'User'"; // Adjust table/column names if different
$result = $conn->query($sql);

$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Close connection
$conn->close();

// Return JSON response
echo json_encode($users);
?>
