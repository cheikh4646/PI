<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in and has professor role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
    // Log unauthorized access attempt
    if (isset($_SESSION['user_id'])) {
        error_log("Unauthorized access attempt to proffeseur.php by user ID: " . $_SESSION['user_id']);
    } else {
        error_log("Unauthorized access attempt to proffeseur.php (no session)");
    }
    
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Get professor info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'professor'");
    $stmt->execute([$_SESSION['user_id']]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$professor) {
        // This should not happen if session checks are working, but just in case
        error_log("Professor user not found in database despite valid session. User ID: " . $_SESSION['user_id']);
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error in proffeseur.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Professor Dashboard</title>
</head>
<body>
    <h1>Professor Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($professor['email']); ?>!</p>
    
    <div>
        <h2>Professor Controls</h2>
        <!-- Professor controls here -->
    </div>
    
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
