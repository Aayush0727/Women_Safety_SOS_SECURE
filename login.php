<?php
session_start(); // Start a session for user login management
require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    // Fetch user details from the database
    $sql = "SELECT id, first_name, last_name, password, role FROM users WHERE email = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $first_name, $last_name, $hashed_password, $db_role);
        $stmt->fetch();
        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $db_role;
            $_SESSION['name'] = $first_name . " " . $last_name;

            // Redirect based on role
            if ($db_role === "admin") {
                header("Location: Securitycode.html");
                exit();
            } elseif ($db_role === "user") {
                header("Location: user_dashboard.html");
                exit();
            }
        } else {
            echo "<script>alert('Invalid password!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid email or role selection!'); window.location.href='login.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
