<?php
include 'config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Get current user and biodata details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$stmt2 = $conn->prepare("SELECT * FROM biodata WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$biodata = $stmt2->get_result()->fetch_assoc();

// Check if form is submitted
if (isset($_POST['update_profile'])) {
    try {
        // Update users table first
        $fullName = $_POST['fullName'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $maritalStatus = $_POST['maritalStatus'] ?? '';
        
        $stmt = $conn->prepare("UPDATE users SET fullName = ?, phone = ?, gender = ?, maritalStatus = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $fullName, $phone, $gender, $maritalStatus, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("User table update failed: " . $stmt->error);
        }
        
        // Handle photo upload
        $photo = $biodata['photo'] ?? ''; // Keep existing photo
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $photo = $target_dir . basename($_FILES["photo"]["name"]);
            move_uploaded_file($_FILES["photo"]["tmp_name"], $photo);
        }
        
        // Handle hobbies and preferences arrays
        $hobbies = isset($_POST['hobbies']) ? implode(', ', $_POST['hobbies']) : '';
        $preferences = isset($_POST['preferences']) ? implode(', ', $_POST['preferences']) : '';

        // Get all form data with proper defaults
        $age = (int)($_POST['age'] ?? 0);
        $address = $_POST['address'] ?? '';
        
        // Education fields - handle NULL values properly
        $degree1 = $_POST['degree1'] ?? '';
        $institution1 = $_POST['institution1'] ?? '';
        $year1 = ($_POST['year1'] === '' || $_POST['year1'] == 0) ? null : (int)$_POST['year1'];
        $grade1 = $_POST['grade1'] ?? '';
        
        $degree2 = $_POST['degree2'] ?? '';
        $institution2 = $_POST['institution2'] ?? '';
        $year2 = ($_POST['year2'] === '' || $_POST['year2'] == 0) ? null : (int)$_POST['year2'];
        $grade2 = $_POST['grade2'] ?? '';
        
        $degree3 = $_POST['degree3'] ?? '';
        $institution3 = $_POST['institution3'] ?? '';
        $year3 = ($_POST['year3'] === '' || $_POST['year3'] == 0) ? null : (int)$_POST['year3'];
        $grade3 = $_POST['grade3'] ?? '';
        
        $degree4 = $_POST['degree4'] ?? '';
        $institution4 = $_POST['institution4'] ?? '';
        $year4 = ($_POST['year4'] === '' || $_POST['year4'] == 0) ? null : (int)$_POST['year4'];
        $grade4 = $_POST['grade4'] ?? '';

        // Professional fields
        $occupation = $_POST['occupation'] ?? '';
        $company = $_POST['company'] ?? '';
        $income = $_POST['income'] ?? '';

        // Family fields
        $fatherName = $_POST['fatherName'] ?? '';
        $motherName = $_POST['motherName'] ?? '';
        $siblings = (int)($_POST['siblings'] ?? 0);
        $familyType = $_POST['familyType'] ?? '';
        $additionalInfo = $_POST['additionalInfo'] ?? '';
        
        
        // SQL UPDATE with exact 30 parameters
        $sql = "UPDATE biodata SET
            age = ?, 
            address = ?, 
            hobbies = ?, 
            photo = ?, 
            degree1 = ?, 
            institution1 = ?, 
            year1 = ?, 
            grade1 = ?, 
            degree2 = ?, 
            institution2 = ?, 
            year2 = ?, 
            grade2 = ?, 
            degree3 = ?, 
            institution3 = ?, 
            year3 = ?, 
            grade3 = ?, 
            degree4 = ?, 
            institution4 = ?, 
            year4 = ?, 
            grade4 = ?, 
            occupation = ?, 
            company = ?, 
            income = ?, 
            fatherName = ?, 
            motherName = ?, 
            siblings = ?, 
            familyType = ?, 
            preferences = ?, 
            additionalInfo = ?
        WHERE user_id = ?";

        $stmt2 = $conn->prepare($sql);
        if (!$stmt2) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Let me manually build the type string character by character
        $typeString = "";
        $typeString .= "i"; // 1. age
        $typeString .= "s"; // 2. address  
        $typeString .= "s"; // 3. hobbies
        $typeString .= "s"; // 4. photo
        $typeString .= "s"; // 5. degree1
        $typeString .= "s"; // 6. institution1
        $typeString .= "i"; // 7. year1
        $typeString .= "s"; // 8. grade1
        $typeString .= "s"; // 9. degree2
        $typeString .= "s"; // 10. institution2
        $typeString .= "i"; // 11. year2
        $typeString .= "s"; // 12. grade2
        $typeString .= "s"; // 13. degree3
        $typeString .= "s"; // 14. institution3
        $typeString .= "i"; // 15. year3
        $typeString .= "s"; // 16. grade3
        $typeString .= "s"; // 17. degree4
        $typeString .= "s"; // 18. institution4
        $typeString .= "i"; // 19. year4
        $typeString .= "s"; // 20. grade4
        $typeString .= "s"; // 21. occupation
        $typeString .= "s"; // 22. company
        $typeString .= "s"; // 23. income
        $typeString .= "s"; // 24. fatherName
        $typeString .= "s"; // 25. motherName
        $typeString .= "i"; // 26. siblings
        $typeString .= "s"; // 27. familyType
        $typeString .= "s"; // 28. preferences
        $typeString .= "s"; // 29. additionalInfo
        $typeString .= "i"; // 30. user_id
        

        
        $stmt2->bind_param(
            $typeString,
            $age,           // 1  - i
            $address,       // 2  - s
            $hobbies,       // 3  - s
            $photo,         // 4  - s
            $degree1,       // 5  - s
            $institution1,  // 6  - s
            $year1,         // 7  - i
            $grade1,        // 8  - s
            $degree2,       // 9  - s
            $institution2,  // 10 - s
            $year2,         // 11 - i
            $grade2,        // 12 - s
            $degree3,       // 13 - s
            $institution3,  // 14 - s
            $year3,         // 15 - i
            $grade3,        // 16 - s
            $degree4,       // 17 - s
            $institution4,  // 18 - s
            $year4,         // 19 - i
            $grade4,        // 20 - s
            $occupation,    // 21 - s
            $company,       // 22 - s
            $income,        // 23 - s
            $fatherName,    // 24 - s
            $motherName,    // 25 - s
            $siblings,      // 26 - i
            $familyType,    // 27 - s
            $preferences,   // 28 - s
            $additionalInfo,// 29 - s
            $user_id        // 30 - i
        );

        if (!$stmt2->execute()) {
            throw new Exception("Biodata update failed: " . $stmt2->error);
        }

        echo "<div style='background: lightgreen; padding: 10px;'>Profile updated successfully!</div>";
        
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        $stmt2 = $conn->prepare("SELECT * FROM biodata WHERE user_id = ?");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $biodata = $stmt2->get_result()->fetch_assoc();
        
    } catch (Exception $e) {
        echo "<div style='background: red; color: white; padding: 10px;'>Error: " . $e->getMessage() . "</div>";
        $error = "Update failed: " . $e->getMessage();
    }
} else {
    echo "<div style='background: lightblue; padding: 10px; margin: 10px;'>Form not submitted yet</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Biodata</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Update Your Biodata</h1>
        
        <div class="dashboard-nav">
            <a href="user_dashboard.php" class="submit-btn">Back to Dashboard</a>
        </div>
        <br>
        <?php if($success): ?>
            <div style="color: green; margin-bottom: 15px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div style="color: red; margin-bottom: 15px; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form class="matrimonial-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <fieldset class="section">
                <legend>Personal Details</legend>
            
                <label for="fullName">Full Name:</label>
                <div class="input-wrapper">
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['fullName']); ?>" required>
                </div>
            
                <label for="age">Age:</label>
                <div class="input-wrapper">
                    <input type="number" id="age" name="age" min="18" max="80" value="<?php echo htmlspecialchars($biodata['age']); ?>" required>
                </div>
            
                <label for="phone">Phone Number:</label>
                <div class="input-wrapper">
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
            
                <label for="address">Address:</label>
                <div class="input-wrapper">
                    <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($biodata['address']); ?></textarea>
                </div>
            </fieldset>

            <fieldset class="section">
                <legend>Gender</legend>
                <div class="radio-group">
                    <input type="radio" id="male" name="gender" value="male" <?php echo ($user['gender'] == 'male') ? 'checked' : ''; ?> required>
                    <label for="male">Male</label>

                    <input type="radio" id="female" name="gender" value="female" <?php echo ($user['gender'] == 'female') ? 'checked' : ''; ?> required>
                    <label for="female">Female</label>
                </div>
            </fieldset>

            <fieldset class="section">
                <legend>Marital Status</legend>
                <div class="radio-group">
                    <input type="radio" id="single" name="maritalStatus" value="single" <?php echo ($user['maritalStatus'] == 'single') ? 'checked' : ''; ?> required>
                    <label for="single">Single</label>

                    <input type="radio" id="divorced" name="maritalStatus" value="divorced" <?php echo ($user['maritalStatus'] == 'divorced') ? 'checked' : ''; ?> required>
                    <label for="divorced">Divorced</label>

                    <input type="radio" id="widowed" name="maritalStatus" value="widowed" <?php echo ($user['maritalStatus'] == 'widowed') ? 'checked' : ''; ?> required>
                    <label for="widowed">Widowed</label>
                </div>
            </fieldset>

            <fieldset class="section">
                <legend>Hobbies & Interests</legend>
                <div class="checkbox-group">
                    <?php 
                    $current_hobbies = explode(', ', $biodata['hobbies']);
                    $hobby_options = ['reading', 'music', 'sports', 'cooking', 'traveling', 'movies'];
                    ?>
                    
                    <?php foreach($hobby_options as $hobby): ?>
                        <input type="checkbox" id="<?php echo $hobby; ?>" name="hobbies[]" value="<?php echo $hobby; ?>" 
                               <?php echo in_array($hobby, $current_hobbies) ? 'checked' : ''; ?>>
                        <label for="<?php echo $hobby; ?>"><?php echo ucfirst($hobby); ?></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset class="section">
                <legend>Photo Upload</legend>
                <?php if($biodata['photo']): ?>
                    <p>Current Photo:</p>
                    <img src="<?php echo htmlspecialchars($biodata['photo']); ?>" width="100" height="100" style="margin-bottom: 10px;">
                <?php endif; ?>
                <label for="photo">Upload New Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </fieldset>

            <fieldset class="section">
                <legend>Education & Qualification</legend>
                <table class="education-table">
                    <thead>
                        <tr>
                            <th>Degree/Certificate</th>
                            <th>Institution</th>
                            <th>Year of Passing</th>
                            <th>Percentage/Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i=1; $i<=4; $i++): ?>
                        <tr>
                            <td><input type="text" name="degree<?php echo $i; ?>" value="<?php echo htmlspecialchars($biodata["degree$i"]); ?>"></td>
                            <td><input type="text" name="institution<?php echo $i; ?>" value="<?php echo htmlspecialchars($biodata["institution$i"]); ?>"></td>
                            <td><input type="number" name="year<?php echo $i; ?>" min="1980" max="2030" value="<?php echo htmlspecialchars($biodata["year$i"]); ?>"></td>
                            <td><input type="text" name="grade<?php echo $i; ?>" value="<?php echo htmlspecialchars($biodata["grade$i"]); ?>"></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </fieldset>

            <fieldset class="section">
                <legend>Professional Details</legend>

                <label for="occupation">Occupation:</label>
                <div class="input-wrapper">
                    <input type="text" id="occupation" name="occupation" value="<?php echo htmlspecialchars($biodata['occupation']); ?>">
                </div>

                <label for="company">Company Name:</label>
                <div class="input-wrapper">
                    <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($biodata['company']); ?>">
                </div>

                <label for="income">Annual Income:</label>
                <div class="input-wrapper">
                    <select id="income" name="income">
                        <option value="">Select Income Range</option>
                        <option value="0-50 Thousands" <?php echo ($biodata['income'] == '0-50 Thousands') ? 'selected' : ''; ?>>0 - 50 Thousands</option>
                        <option value="50 Thousand -1 Lakh" <?php echo ($biodata['income'] == '50 Thousand -1 Lakh ') ? 'selected' : ''; ?>>50 Thousand - 1 Lakhs</option>
                        <option value="1-2 Lakh" <?php echo ($biodata['income'] == '1-2 Lakh') ? 'selected' : ''; ?>>1 - 2 Lakhs</option>
                        <option value="2+ Lakh" <?php echo ($biodata['income'] == '2+ Lakh') ? 'selected' : ''; ?>>2+ Lakhs</option>
                    </select>
                </div>
            </fieldset>

            <fieldset class="section">
                <legend>Family Details</legend>
                <label for="fatherName">Father's Name:</label>
                <div class="input-wrapper">
                    <input type="text" id="fatherName" name="fatherName" value="<?php echo htmlspecialchars($biodata['fatherName']); ?>">
                </div>
                <label for="motherName">Mother's Name:</label>
                <div class="input-wrapper">
                    <input type="text" id="motherName" name="motherName" value="<?php echo htmlspecialchars($biodata['motherName']); ?>">
                </div>
                <label for="siblings">Number of Siblings:</label>
                <div class="input-wrapper">
                    <input type="number" id="siblings" name="siblings" min="0" max="10" value="<?php echo htmlspecialchars($biodata['siblings']); ?>">
                </div>
                <label for="familyType">Family Type:</label>
                <div class="input-wrapper">
                    <select id="familyType" name="familyType">
                        <option value="">Select Family Type</option>
                        <option value="nuclear" <?php echo ($biodata['familyType'] == 'nuclear') ? 'selected' : ''; ?>>Nuclear Family</option>
                        <option value="joint" <?php echo ($biodata['familyType'] == 'joint') ? 'selected' : ''; ?>>Joint Family</option>
                    </select>
                </div>
            </fieldset>

            <fieldset class="section">
                <legend>Partner Preferences</legend>
                <div class="checkbox-group">
                    <?php 
                    $current_preferences = explode(', ', $biodata['preferences']);
                    $preference_options = ['educated', 'employed', 'family-oriented', 'religious'];
                    ?>
                    <?php foreach($preference_options as $pref): ?>
                        <input type="checkbox" id="<?php echo $pref; ?>" name="preferences[]" value="<?php echo $pref; ?>" 
                               <?php echo in_array($pref, $current_preferences) ? 'checked' : ''; ?>>
                        <label for="<?php echo $pref; ?>"><?php echo ucfirst(str_replace('-', ' ', $pref)); ?></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset class="section">
                <legend>Additional Information</legend>
                <label for="additionalInfo" class="sr-only">Additional Information about yourself and partner preferences:</label>
                <textarea id="additionalInfo" name="additionalInfo" rows="4"><?php echo htmlspecialchars($biodata['additionalInfo']); ?></textarea>
            </fieldset>

            <div class="submit-section">
                <button type="submit" name="update_profile" class="submit-btn">Update Profile</button>
                <a href="user_dashboard.php" class="reset-btn" style="text-decoration: none; display: inline-block; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>

</body>
</html>
``````