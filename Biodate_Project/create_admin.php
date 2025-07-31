<?php
include 'config.php';

// Create admin user (run this once)
$admin_email = "admin@matrimonial.com";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);
$admin_name = "Admin";
$admin_phone = "1234567890";
$admin_gender = "male";
$admin_marital = "single";
$admin_role = "admin";

$stmt = $conn->prepare("INSERT INTO users (email, password, fullName, phone, gender, maritalStatus, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $admin_email, $admin_password, $admin_name, $admin_phone, $admin_gender, $admin_marital, $admin_role);

if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Email: admin@matrimonial.com<br>";
    echo "Password: admin123<br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "Error: " . $stmt->error;
}
?>