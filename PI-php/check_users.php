<?php
require_once 'includes/db.php';

try {
    // Check if the users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "The 'users' table doesn't exist in the database.";
        exit;
    }
    
    // Query users table
    $stmt = $pdo->query("SELECT id, email, role, password FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<h2>Users in the database:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Password (hashed)</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . substr($user['password'], 0, 20) . "...</td>"; // Only show part of the hash for security
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Check password hashing format
        $firstPassword = $users[0]['password'];
        echo "<p>First password hash format check: " . 
        (password_get_info($firstPassword)['algo'] !== 0 ? "Properly hashed" : "NOT properly hashed") . "</p>";
    } else {
        echo "No users found in the database.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 