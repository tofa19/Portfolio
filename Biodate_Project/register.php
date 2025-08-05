<?php
include 'config.php';

$success = '';
$error = '';

// Debug: Check if form is being submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<div style='background: yellow; padding: 10px; margin: 10px;'>FORM SUBMITTED!</div>";

    $fullName = $_POST['fullName'] ?? '';
    $age = $_POST['age'] ?? null;
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $maritalStatus = $_POST['maritalStatus'] ?? '';
    $password = $_POST['password'] ?? '';

    // Enhanced validation with specific error messages
    $validation_errors = [];

    if (empty($fullName)) $validation_errors[] = "Full Name is required";
    if (empty($age) || $age < 18) $validation_errors[] = "Valid age (18+) is required";
    if (empty($email)) $validation_errors[] = "Email is required";
    
    // Enhanced password validation
    if (empty($password)) {
        $validation_errors[] = "Password is required";
    } else {
        if (strlen($password) < 6) {
            $validation_errors[] = "Password must be at least 6 characters long";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $validation_errors[] = "Password must contain at least one uppercase letter";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $validation_errors[] = "Password must contain at least one number";
        }
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $validation_errors[] = "Password must contain at least one special character (!@#$%^&*(),.?\":{}|<>)";
        }
    }
    
    if (empty($phone)) $validation_errors[] = "Phone number is required";
    if (empty($gender)) $validation_errors[] = "Gender selection is required";
    if (empty($maritalStatus)) $validation_errors[] = "Marital status selection is required";

    if (!empty($validation_errors)) {
        $error = "Please fix the following errors:<br>• " . implode("<br>• ", $validation_errors);
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Handle hobbies array
        $hobbies = isset($_POST['hobbies']) ? implode(', ', $_POST['hobbies']) : '';

        // Handle photo upload
        $photo = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $photo = $target_dir . basename($_FILES["photo"]["name"]);
            move_uploaded_file($_FILES["photo"]["tmp_name"], $photo);
        }

        // Handle preferences array
        $preferences = isset($_POST['preferences']) ? implode(', ', $_POST['preferences']) : '';

        try {
            // Check if email already exists
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Email already exists! Please use a different email.";
            } else {
                // Insert into users table first
                $stmt = $conn->prepare("INSERT INTO users (email, password, fullName, phone, gender, maritalStatus, role) VALUES (?, ?, ?, ?, ?, ?, 'user')");
                $stmt->bind_param("ssssss", $email, $password, $fullName, $phone, $gender, $maritalStatus);

                if ($stmt->execute()) {
                    $user_id = $conn->insert_id;
                    echo "<div style='background: lightgreen; padding: 10px; margin: 10px;'>User created with ID: $user_id</div>";

                    // Prepare all variables for biodata
                    $degree1 = $_POST['degree1'] ?? '';         $institution1 = $_POST['institution1'] ?? '';
                    $year1 = $_POST['year1'] ?? null;           $grade1 = $_POST['grade1'] ?? '';
                    $degree2 = $_POST['degree2'] ?? '';         $institution2 = $_POST['institution2'] ?? '';
                    $year2 = $_POST['year2'] ?? null;           $grade2 = $_POST['grade2'] ?? '';
                    $degree3 = $_POST['degree3'] ?? '';         $institution3 = $_POST['institution3'] ?? '';
                    $year3 = $_POST['year3'] ?? null;           $grade3 = $_POST['grade3'] ?? '';
                    $degree4 = $_POST['degree4'] ?? '';         $institution4 = $_POST['institution4'] ?? '';
                    $year4 = $_POST['year4'] ?? null;           $grade4 = $_POST['grade4'] ?? '';
                    $occupation = $_POST['occupation'] ?? '';   $company = $_POST['company'] ?? '';
                    $income = $_POST['income'] ?? '';           $fatherName = $_POST['fatherName'] ?? '';
                    $motherName = $_POST['motherName'] ?? '';   $siblings = $_POST['siblings'] ?? 0;
                    $familyType = $_POST['familyType'] ?? '';   $additionalInfo = $_POST['additionalInfo'] ?? '';
                    $preferences = isset($_POST['preferences']) ? implode(', ', $_POST['preferences']) : '';

                    // Convert empty year values to NULL for database
                    $year1 = ($year1 === '' || $year1 === 0) ? null : (int)$year1;
                    $year2 = ($year2 === '' || $year2 === 0) ? null : (int)$year2;
                    $year3 = ($year3 === '' || $year3 === 0) ? null : (int)$year3;
                    $year4 = ($year4 === '' || $year4 === 0) ? null : (int)$year4;
                    $siblings = (int)$siblings;

                    // SQL with exactly 30 placeholders
                    $sql = "INSERT INTO biodata (
                        user_id, age, address, hobbies, photo,
                        degree1, institution1, year1, grade1,
                        degree2, institution2, year2, grade2,
                        degree3, institution3, year3, grade3,
                        degree4, institution4, year4, grade4,
                        occupation, company, income,
                        fatherName, motherName, siblings, familyType,
                        preferences, additionalInfo
                    ) VALUES (
                        ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
                    )";

                    $stmt2 = $conn->prepare($sql);
                    if (!$stmt2) {
                        die("Prepare failed: " . $conn->error);
                    }

                    $stmt2->bind_param(
                        "iisssssisssssisssssisssissssss", // Fixed: changed 'i' to 's' for occupation
                        $user_id,        // i
                        $age,            // i
                        $address,        // s
                        $hobbies,        // s
                        $photo,          // s
                        $degree1,        // s
                        $institution1,   // s
                        $year1,          // i
                        $grade1,         // s
                        $degree2,        // s
                        $institution2,   // s
                        $year2,          // i
                        $grade2,         // s
                        $degree3,        // s
                        $institution3,   // s
                        $year3,          // i
                        $grade3,         // s
                        $degree4,        // s
                        $institution4,   // s
                        $year4,          // i
                        $grade4,         // s
                        $occupation,     // s (was incorrectly marked as 'i')
                        $company,        // s
                        $income,         // s
                        $fatherName,     // s
                        $motherName,     // s
                        $siblings,       // i
                        $familyType,     // s
                        $preferences,    // s
                        $additionalInfo  // s
                    );

                    if ($stmt2->execute()) {
                        $success = "Registration successful! You can now login.";
                        echo "<div style='background: lightgreen; padding: 10px; margin: 10px;'>Biodata inserted successfully!</div>";
                    } else {
                        $error = "User created but biodata insertion failed: " . $stmt2->error;
                        echo "<div style='background: red; color: white; padding: 10px; margin: 10px;'>Biodata Error: " . $stmt2->error . "</div>";
                    }
                } else {
                    $error = "User insertion failed: " . $stmt->error;
                    echo "<div style='background: red; color: white; padding: 10px; margin: 10px;'>User Error: " . $stmt->error . "</div>";
                }
            }
        } catch (Exception $e) {
            $error = "Registration failed: " . $e->getMessage();
            echo "<div style='background: red; color: white; padding: 10px; margin: 10px;'>Exception: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrimonial Registration Form</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Matrimonial Registration Form</h1>

        <?php if ($success): ?>
            <div style="color: green; margin-bottom: 15px; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center;">
                <?php echo $success; ?>
                <br><br>
                <a href="login.php" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Login</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="color: red; margin-bottom: 15px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form class="matrimonial-form" method="POST" enctype="multipart/form-data" novalidate>
                <fieldset class="section">
                    <legend>Personal Details</legend>

                    <label for="fullName">Full Name: <span style="color: red;">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($_POST['fullName'] ?? ''); ?>">
                    </div>

                    <label for="age">Age: <span style="color: red;">*</span></label>
                    <div class="input-wrapper">
                        <input type="number" id="age" name="age" min="18" max="80" value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>">
                    </div>

                    <label for="email">Email: <span style="color: red;">*</span></label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <label for="password">Password:  <span class="password-toggle" onclick="togglePassword()">👁️</span></label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>">
                       
                    </div>
                    <div class="password-requirements">
                        <small>Password must contain:</small>
                        <ul>
                            <li>At least 6 characters</li>
                            <li>One uppercase letter (A-Z)</li>
                            <li>One number (0-9)</li>
                            <li>One special character (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <label for="phone">Phone Number: <span style="color: red;">*</span></label>
                    <div class="input-wrapper">
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>

                    <label for="address">Address:</label>
                    <div class="input-wrapper">
                        <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                    </div>
                </fieldset>

                <fieldset class="section">
                    <legend>Gender <span style="color: red;">*</span></legend>
                    <div class="radio-group">
                        <input type="radio" id="male" name="gender" value="male" <?php echo (($_POST['gender'] ?? '') == 'male') ? 'checked' : ''; ?>>
                        <label for="male">Male</label>

                        <input type="radio" id="female" name="gender" value="female" <?php echo (($_POST['gender'] ?? '') == 'female') ? 'checked' : ''; ?>>
                        <label for="female">Female</label>
                    </div>
                </fieldset>

                <fieldset class="section">
                    <legend>Marital Status <span style="color: red;">*</span></legend>
                    <div class="radio-group">
                        <input type="radio" id="single" name="maritalStatus" value="single" <?php echo (($_POST['maritalStatus'] ?? '') == 'single') ? 'checked' : ''; ?>>
                        <label for="single">Single</label>

                        <input type="radio" id="divorced" name="maritalStatus" value="divorced" <?php echo (($_POST['maritalStatus'] ?? '') == 'divorced') ? 'checked' : ''; ?>>
                        <label for="divorced">Divorced</label>

                        <input type="radio" id="widowed" name="maritalStatus" value="widowed" <?php echo (($_POST['maritalStatus'] ?? '') == 'widowed') ? 'checked' : ''; ?>>
                        <label for="widowed">Widowed</label>
                    </div>
                </fieldset>

                <fieldset class="section">
                    <legend>Hobbies & Interests</legend>
                    <div class="checkbox-group">
                        <?php
                        $selected_hobbies = $_POST['hobbies'] ?? [];
                        ?>
                        <input type="checkbox" id="reading" name="hobbies[]" value="reading" <?php echo in_array('reading', $selected_hobbies) ? 'checked' : ''; ?>>
                        <label for="reading">Reading</label>

                        <input type="checkbox" id="music" name="hobbies[]" value="music" <?php echo in_array('music', $selected_hobbies) ? 'checked' : ''; ?>>
                        <label for="music">Music</label>

                        <input type="checkbox" id="sports" name="hobbies[]" value="sports" <?php echo in_array('sports', $selected_hobbies) ? 'checked' : ''; ?>>
                        <label for="sports">Sports</label>

                        <input type="checkbox" id="cooking" name="hobbies[]" value="cooking" <?php echo in_array('cooking', $selected_hobbies) ? 'checked' : ''; ?>>
                        <label for="cooking">Cooking</label>

                        <input type="checkbox" id="traveling" name="hobbies[]" value="traveling" <?php echo in_array('traveling', $selected_hobbies) ? 'checked' : ''; ?>>
                        <label for="traveling">Traveling</label>

                        <input type="checkbox" id="movies" name="hobbies[]" value="movies" <?php echo in_array('movies', $selected_hobbies) ? 'checked' : ''; ?>>
                        <label for="movies">Movies</label>
                    </div>
                </fieldset>

                <fieldset class="section">
                    <legend>Photo Upload</legend>
                    <label for="photo">Upload Your Photo:</label>
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
                            <tr>
                                <td><input type="text" name="degree1" placeholder="e.g., 10th Grade" value="<?php echo htmlspecialchars($_POST['degree1'] ?? ''); ?>"></td>
                                <td><input type="text" name="institution1" placeholder="School Name" value="<?php echo htmlspecialchars($_POST['institution1'] ?? ''); ?>"></td>
                                <td><input type="number" name="year1" min="1980" max="2030" value="<?php echo htmlspecialchars($_POST['year1'] ?? ''); ?>"></td>
                                <td><input type="text" name="grade1" placeholder="e.g., 85% or 5.00" value="<?php echo htmlspecialchars($_POST['grade1'] ?? ''); ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="degree2" placeholder="e.g., 12th Grade" value="<?php echo htmlspecialchars($_POST['degree2'] ?? ''); ?>"></td>
                                <td><input type="text" name="institution2" placeholder="School Name" value="<?php echo htmlspecialchars($_POST['institution2'] ?? ''); ?>"></td>
                                <td><input type="number" name="year2" min="1980" max="2030" value="<?php echo htmlspecialchars($_POST['year2'] ?? ''); ?>"></td>
                                <td><input type="text" name="grade2" placeholder="e.g., 78% or 4.50" value="<?php echo htmlspecialchars($_POST['grade2'] ?? ''); ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="degree3" placeholder="e.g., Bachelor's" value="<?php echo htmlspecialchars($_POST['degree3'] ?? ''); ?>"></td>
                                <td><input type="text" name="institution3" placeholder="College Name" value="<?php echo htmlspecialchars($_POST['institution3'] ?? ''); ?>"></td>
                                <td><input type="number" name="year3" min="1980" max="2030" value="<?php echo htmlspecialchars($_POST['year3'] ?? ''); ?>"></td>
                                <td><input type="text" name="grade3" placeholder="e.g., 3.5 GPA" value="<?php echo htmlspecialchars($_POST['grade3'] ?? ''); ?>"></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="degree4" placeholder="e.g., Master's" value="<?php echo htmlspecialchars($_POST['degree4'] ?? ''); ?>"></td>
                                <td><input type="text" name="institution4" placeholder="University Name" value="<?php echo htmlspecialchars($_POST['institution4'] ?? ''); ?>"></td>
                                <td><input type="number" name="year4" min="1980" max="2030" value="<?php echo htmlspecialchars($_POST['year4'] ?? ''); ?>"></td>
                                <td><input type="text" name="grade4" placeholder="e.g., 3.2 GPA" value="<?php echo htmlspecialchars($_POST['grade4'] ?? ''); ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset class="section">
                    <legend>Professional Details</legend>

                    <label for="occupation">Occupation:</label>
                    <div class="input-wrapper">
                        <input type="text" id="occupation" name="occupation" value="<?php echo htmlspecialchars($_POST['occupation'] ?? ''); ?>">
                    </div>

                    <label for="company">Company Name:</label>
                    <div class="input-wrapper">
                        <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($_POST['company'] ?? ''); ?>">
                    </div>

                    <label for="income">Annual Income:</label>
                    <div class="input-wrapper">
                        <select id="income" name="income">
                            <option value="">Select Income Range</option>
                            <option value="0-50 Thousand" <?php echo (($_POST['income'] ?? '') == '0-50 Thousand') ? 'selected' : ''; ?>>0 - 50 Thousands</option>
                            <option value="50-100 Thousand" <?php echo (($_POST['income'] ?? '') == '50-100 Thousand') ? 'selected' : ''; ?>>50 Thousand - 1 Lakhs</option>
                            <option value="1-2 Lakh" <?php echo (($_POST['income'] ?? '') == '1-2 Lakh') ? 'selected' : ''; ?>>1 - 2 Lakhs</option>
                            <option value="2+ Lakh" <?php echo (($_POST['income'] ?? '') == '2+ Lakh') ? 'selected' : ''; ?>>2+ Lakhs</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="section">
                    <legend>Family Details</legend>

                    <label for="fatherName">Father's Name:</label>
                    <div class="input-wrapper">
                        <input type="text" id="fatherName" name="fatherName" value="<?php echo htmlspecialchars($_POST['fatherName'] ?? ''); ?>">
                    </div>

                    <label for="motherName">Mother's Name:</label>
                    <div class="input-wrapper">
                        <input type="text" id="motherName" name="motherName" value="<?php echo htmlspecialchars($_POST['motherName'] ?? ''); ?>">
                    </div>

                    <label for="siblings">Number of Siblings:</label>
                    <div class="input-wrapper">
                        <input type="number" id="siblings" name="siblings" min="0" max="10" value="<?php echo htmlspecialchars($_POST['siblings'] ?? ''); ?>">
                    </div>

                    <label for="familyType">Family Type:</label>
                    <div class="input-wrapper">
                        <select id="familyType" name="familyType">
                            <option value="">Select Family Type</option>
                            <option value="nuclear" <?php echo (($_POST['familyType'] ?? '') == 'nuclear') ? 'selected' : ''; ?>>Nuclear Family</option>
                            <option value="joint" <?php echo (($_POST['familyType'] ?? '') == 'joint') ? 'selected' : ''; ?>>Joint Family</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="section">
                    <legend>Partner Preferences</legend>
                    <div class="checkbox-group">
                        <?php
                        $selected_preferences = $_POST['preferences'] ?? [];
                        ?>
                        <input type="checkbox" id="educated" name="preferences[]" value="educated" <?php echo in_array('educated', $selected_preferences) ? 'checked' : ''; ?>>
                        <label for="educated">Well Educated</label>

                        <input type="checkbox" id="employed" name="preferences[]" value="employed" <?php echo in_array('employed', $selected_preferences) ? 'checked' : ''; ?>>
                        <label for="employed">Employed</label>

                        <input type="checkbox" id="family-oriented" name="preferences[]" value="family-oriented" <?php echo in_array('family-oriented', $selected_preferences) ? 'checked' : ''; ?>>
                        <label for="family-oriented">Family Oriented</label>

                        <input type="checkbox" id="religious" name="preferences[]" value="religious" <?php echo in_array('religious', $selected_preferences) ? 'checked' : ''; ?>>
                        <label for="religious">Religious</label>
                    </div>
                </fieldset>

                <fieldset class="section">
                    <legend>Additional Information</legend>
                    <label for="additionalInfo" class="sr-only">Additional Information about yourself and partner preferences:</label>
                    <textarea id="additionalInfo" name="additionalInfo" rows="4" placeholder="Tell us more about yourself and what you're looking for in a partner..."><?php echo htmlspecialchars($_POST['additionalInfo'] ?? ''); ?></textarea>
                </fieldset>

                <div class="submit-section">
                    <button type="submit" class="submit-btn">Register</button>
                    <button type="reset" class="reset-btn">Reset</button>
                </div>
            </form>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 20px;">
            <p>Already have an account? <a href="login.php">Login Here</a></p>
        </div>
    </div>

    <script src="validation.js"></script>
</body>

</html>