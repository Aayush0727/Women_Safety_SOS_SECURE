<?php
session_start([
    'cookie_httponly' => true,  // This prevent JavaScript access
    'cookie_secure' => true,     // HTTPS only
    'cookie_samesite' => 'Strict' // CSRF protection
]);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

echo json_encode(["token" => $_SESSION['csrf_token']]);
?>