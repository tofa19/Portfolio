<?php
include 'config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];
$user_id = $_SESSION['user_id'];

// Get user details
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get biodata details
$stmt2 = $conn->prepare("SELECT * FROM biodata WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$biodata = $stmt2->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user['fullName']); ?>!</h1>

        <div class="dashboard-nav">
            <input type="button" value="Edit Profile" class="submit-btn" onclick="window.location.href='update_biodata.php'">
            <input type="button" value="Change Password" class="submit-btn" onclick="window.location.href='change_password.php'">
            <input type="button" value="Logout" class="reset-btn" onclick="window.location.href='logout.php'"> 
        </div>
        <br>
        <fieldset class="section">
            <legend>Personal Information</legend>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <?php if ($biodata): ?>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($biodata['age']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($biodata['address']); ?></p>

        </fieldset>
        <fieldset class="section">
            <legend>Gender</legend>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        </fieldset>
        <fieldset class="section">
            <legend>Marital Status</legend>
            <p><strong>Marital Status:</strong> <?php echo htmlspecialchars($user['maritalStatus']); ?></p>
        </fieldset>
        <fieldset class="section">
            <legend>Photo</legend>
            <?php if ($biodata['photo']): ?>
                <p><strong>Photo:</strong><br> <img src="<?php echo htmlspecialchars($biodata['photo']); ?>" width="150" height="150"></p>
            <?php endif; ?>
        <?php endif; ?>
        </fieldset>
        <fieldset class="section">
            <legend>Hobbies and Interests</legend>
            <p><strong>Hobbies:</strong> <?php echo htmlspecialchars($biodata['hobbies']); ?></p>
        </fieldset>
        <?php if ($biodata): ?>
            <fieldset class="section">
                <legend>Education Details</legend>
                <table class="education-table">
                    <thead>
                        <tr>
                            <th>Degree/Certificate</th>
                            <th>Institution</th>
                            <th>Year</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <?php if ($biodata["degree$i"]): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($biodata["degree$i"]); ?></td>
                                    <td><?php echo htmlspecialchars($biodata["institution$i"]); ?></td>
                                    <td><?php echo htmlspecialchars($biodata["year$i"]); ?></td>
                                    <td><?php echo htmlspecialchars($biodata["grade$i"]); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </fieldset>

            <fieldset class="section">
                <legend>Professional Details</legend>
                <p><strong>Occupation:</strong> <?php echo htmlspecialchars($biodata['occupation']); ?></p>
                <p><strong>Company:</strong> <?php echo htmlspecialchars($biodata['company']); ?></p>
                <p><strong>Income:</strong> <?php echo htmlspecialchars($biodata['income']); ?></p>
            </fieldset>

            <fieldset class="section">
                <legend>Family Details</legend>
                <p><strong>Father's Name:</strong> <?php echo htmlspecialchars($biodata['fatherName']); ?></p>
                <p><strong>Mother's Name:</strong> <?php echo htmlspecialchars($biodata['motherName']); ?></p>
                <p><strong>Siblings:</strong> <?php echo htmlspecialchars($biodata['siblings']); ?></p>
                <p><strong>Family Type:</strong> <?php echo htmlspecialchars($biodata['familyType']); ?></p>
            </fieldset>

            <fieldset class="section">
                <legend>Partner Preferences</legend>
                <p><?php echo htmlspecialchars($biodata['preferences']); ?></p>
            </fieldset>

            <fieldset class="section">
                <legend>Additional Information</legend>
                <p><?php echo nl2br(htmlspecialchars($biodata['additionalInfo'])); ?></p>
            </fieldset>
        <?php endif; ?>
    </div>
</body>

</html>