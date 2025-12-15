<?php
session_start(); // ADD THIS LINE

require_once('/Applications/XAMPP/xamppfiles/config/database.php');
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['f-name'];
    $last_name = $_POST['l-name'];
    $email = $_POST['mail'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['user-password'], PASSWORD_BCRYPT); // Encrypt password
    $role = $_POST['role']; // Get selected role

    // Check if email already exists
    $check_email_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email already exists
        echo "<script>
                alert('Email already registered! Please use another email or log in.');
                window.location.href='login.html';
              </script>";
        exit();
    } else {
        // Insert user data into the database
        $stmt->close();

        $insert_sql = "INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $password, $role);

        if ($stmt->execute()) {
            // Registration successful -> Redirect to login page
            echo "<script>
                    alert('Registration successful! Please log in.');
                    window.location.href='login.html';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Error: " . $stmt->error . "');
                    window.location.href='register.html';
                  </script>";
            exit();
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!-- Add this script at the very top of <head> -->
<script>
// Check if user is logged in
fetch('get_user_profile.php')
    .then(response => {
        if (response.status === 401) {
            window.location.href = 'login.html';
        }
        return response.json();
    })
    .catch(() => window.location.href = 'login.html');
</script>
