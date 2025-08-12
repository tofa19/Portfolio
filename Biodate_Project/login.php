<?php
include 'config.php';

$error = '';

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];

            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Matrimonial</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Matrimonial Login</h1>
        
        <div style="display: flex; align-items: center; justify-content: center;">
            <img src="logo.png" alt="Matrimonial Logo" style="height: 120px; margin-right: 40px;">
            <form method="POST" class="matrimonial-form">
                <fieldset class="section">
                    <legend>Login Details</legend>
                    <label for="email">Email:</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" required>
                    </div>
                    <label for="password">Password: <span class="password-toggle" onclick="togglePassword()">👁️</span></label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>">
                    </div>
                </fieldset>
                <div class="submit-section">
                    <button type="submit" class="submit-btn">Login</button>
                    <button type="button" class="submit-btn" onclick="window.location.href='register.php'">Register as a New User</button>
                </div>
            </form>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <p><small>Admin Login: Use admin credentials</small></p>
        </div>
    </div>
</body>
<script src="validation.js"></script>

</html>