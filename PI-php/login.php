<?php
session_start();
require_once 'includes/db.php'; // your database connection

// Initialize an error message variable
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Log login attempt for debugging
    error_log("Login attempt for email: $email");
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        try {
            // Fetch user data from the database with better error handling
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                // User not found
                error_log("Login failed: No user found with email $email");
                $error_message = "Invalid email or password.";
            } else {
                // Check if the password is stored as plain text (bad practice but might be the issue)
                if ($user['password'] === $password) {
                    // Plain text password match (not secure)
                    error_log("WARNING: Using plain text password comparison for user: $email");
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['group_id'] = $user['group_id'] ?? null;
                    $_SESSION['year_id'] = $user['year_id'] ?? null;
                    
                    redirectUserByRole($user['role']);
                } 
                // Check if the password is properly hashed
                else if (password_verify($password, $user['password'])) {
                    error_log("Login successful for user: $email");
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['group_id'] = $user['group_id'] ?? null;
                    $_SESSION['year_id'] = $user['year_id'] ?? null;
                    
                    redirectUserByRole($user['role']);
                } else {
                    // Password doesn't match
                    error_log("Login failed: Incorrect password for user $email");
                    $error_message = "Invalid email or password.";
                }
            }
        } catch (PDOException $e) {
            error_log("Database error during login: " . $e->getMessage());
            $error_message = "A system error occurred. Please try again later.";
        }
    }
}

// Function to redirect user based on role
function redirectUserByRole($role) {
    switch ($role) {
        case 'admin':
            header("Location: admin.php");
            break;
        case 'professor':
            header("Location: proffeseur.php");  // Fix spelling if needed
            break;
        case 'student':
            header("Location: student.php");
            break;
        default:
            echo "Unknown role.";
    }
    exit;
}
?>

<!-- HTML for login form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - University Timetable</title>
    <style>
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h2>Login</h2>
    <?php if (!empty($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <input type="email" name="email" placeholder="Email" required> <br>
        <input type="password" name="password" placeholder="Password" required> <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
