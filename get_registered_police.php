<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch police data
$sql = "SELECT name, station, email, contact FROM police";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}

// Store data in an array
$policeData = [];
while ($row = $result->fetch_assoc()) {
    $policeData[] = $row;
}

// Close the connection
$conn->close();

// Output JSON
echo json_encode($policeData);
exit;
?>
