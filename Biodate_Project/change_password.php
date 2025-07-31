<?php
include 'config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $error = "New password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $error = "New password must contain at least one number.";
    } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password)) {
        $error = "New password must contain at least one special character.";
    } else {
        // Update with new password directly
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_new_password, $email);
        
        if ($update_stmt->execute()) {
            $success = "Password changed successfully!";
        } else {
            $error = "Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Change Password</h1>

        <?php if ($success): ?>
            <div style="color: green; margin-bottom: 15px; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center;">
                <?php echo $success; ?>
                <br><br>
                <a href="user_dashboard.php" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Back to Dashboard</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="color: red; margin-bottom: 15px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form class="matrimonial-form" method="POST" novalidate>
                <fieldset class="section">
                    <legend>Change Your Password</legend>

                    <label for="email">Email: <span style="color: red;">*</span></label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly style="background-color: #f5f5f5;">
                    </div>

                    <label for="new_password">New Password: <span style="color: red;">*</span> <span class="password-toggle" onclick="togglePassword('new_password')">👁️</span></label>
                    <div class="input-wrapper">
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="password-requirements">
                        <small>New password must contain:</small>
                        <ul>
                            <li>At least 6 characters</li>
                            <li>One uppercase letter (A-Z)</li>
                            <li>One number (0-9)</li>
                            <li>One special character (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <label for="confirm_password">Confirm New Password: <span style="color: red;">*</span> <span class="password-toggle" onclick="togglePassword('confirm_password')">👁️</span></label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </fieldset>

                <div class="submit-section">
                    <input type="submit" value="Change Password" class="submit-btn">
                    <input type="button" value="Cancel" class="reset-btn" onclick="window.location.href='user_dashboard.php'">
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const label = document.querySelector(`label[for="${fieldId}"]`);
            const toggleIcon = label.querySelector('.password-toggle');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordField.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }

        // Real-time password validation for new password
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordField = document.getElementById('new_password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const requirements = document.querySelectorAll('.password-requirements li');
            
            if (newPasswordField && requirements.length > 0) {
                newPasswordField.addEventListener('input', function() {
                    const password = this.value;
                    
                    // Check length
                    if (requirements[0]) {
                        if (password.length >= 6) {
                            requirements[0].style.color = '#28a745';
                        } else {
                            requirements[0].style.color = '#dc3545';
                        }
                    }
                    
                    // Check uppercase
                    if (requirements[1]) {
                        if (/[A-Z]/.test(password)) {
                            requirements[1].style.color = '#28a745';
                        } else {
                            requirements[1].style.color = '#dc3545';
                        }
                    }
                    
                    // Check number
                    if (requirements[2]) {
                        if (/[0-9]/.test(password)) {
                            requirements[2].style.color = '#28a745';
                        } else {
                            requirements[2].style.color = '#dc3545';
                        }
                    }
                    
                    // Check special character
                    if (requirements[3]) {
                        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                            requirements[3].style.color = '#28a745';
                        } else {
                            requirements[3].style.color = '#dc3545';
                        }
                    }
                });
            }

            // Check if passwords match
            function checkPasswordMatch() {
                if (confirmPasswordField.value && newPasswordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.style.borderColor = '#dc3545';
                } else {
                    confirmPasswordField.style.borderColor = '';
                }
            }

            if (confirmPasswordField) {
                confirmPasswordField.addEventListener('input', checkPasswordMatch);
                newPasswordField.addEventListener('input', checkPasswordMatch);
            }
        });
    </script>
</body>
</html>