<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Log unauthorized access attempt
    if (isset($_SESSION['user_id'])) {
        error_log("Unauthorized access attempt to admin.php by user ID: " . $_SESSION['user_id']);
    } else {
        error_log("Unauthorized access attempt to admin.php (no session)");
    }
    
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Get admin info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        // This should not happen if session checks are working, but just in case
        error_log("Admin user not found in database despite valid session. User ID: " . $_SESSION['user_id']);
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error in admin.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($admin['email']); ?>!</p>
    
    <div>
        <h2>Admin Controls</h2>
        <!-- Admin controls here -->
    </div>
    
    <p><a href="logout.php">Logout</a></p>
</body>
</html>