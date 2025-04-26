<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in and has student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    // Log unauthorized access attempt
    if (isset($_SESSION['user_id'])) {
        error_log("Unauthorized access attempt to student.php by user ID: " . $_SESSION['user_id']);
    } else {
        error_log("Unauthorized access attempt to student.php (no session)");
    }
    
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Get student info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
    $stmt->execute([$_SESSION['user_id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        // This should not happen if session checks are working, but just in case
        error_log("Student user not found in database despite valid session. User ID: " . $_SESSION['user_id']);
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error in student.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Student Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($student['email']); ?>!</p>
    
    <div>
        <h2>Student Information</h2>
        <!-- Student information here -->
    </div>
    
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
