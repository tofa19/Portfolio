<?php
include 'config.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle delete operations
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    
    // Delete biodata first (foreign key constraint)
    $stmt = $conn->prepare("DELETE FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Delete user
    $stmt2 = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    
    header("Location: admin_dashboard.php?msg=User deleted successfully");
    exit();
}

// Get all users with their biodata
$sql = "SELECT u.*, b.age, b.occupation, b.income FROM users u LEFT JOIN biodata b ON u.id = b.user_id WHERE u.role = 'user' ORDER BY u.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .admin-table th, .admin-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .admin-table th {
            background-color: #f2f2f2;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
        }
        .btn-view { background-color: #2196F3; color: white; }
        .btn-edit { background-color: #4CAF50; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .dashboard-nav {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <div class="dashboard-nav">
            <a href="logout.php" class="reset-btn" style="text-decoration: none; display: inline-block; text-align: center;">Logout</a>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div style="color: green; margin-bottom: 15px;"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        
        <fieldset class="section">
            <legend>All Registered Users</legend>
            
            <?php if ($result->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['fullName']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                           
                            <td>
                                <div class="action-buttons">
                                    <a href="view_user.php?id=<?php echo $row['id']; ?>" class="btn btn-view">View</a>
                                    <a href="admin_dashboard.php?delete_user=<?php echo $row['id']; ?>" 
                                       class="btn btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </fieldset>
    </div>
</body>
</html>