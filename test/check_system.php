<?php
// Simple test to check if registration system works
include_once '../config.php';

echo "<h1>Registration System Test</h1>";

// Check database connection
if ($connect) {
    echo "✅ Database connected successfully<br>";
    
    // Check if users table exists
    $result = mysqli_query($connect, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Users table exists<br>";
        
        // Show recent users
        $users = mysqli_query($connect, "SELECT username, email, role FROM users ORDER BY id DESC LIMIT 5");
        echo "<h3>Recent Users:</h3>";
        if (mysqli_num_rows($users) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Username</th><th>Email</th><th>Role</th></tr>";
            while ($user = mysqli_fetch_assoc($users)) {
                echo "<tr>";
                echo "<td>" . $user['username'] . "</td>";
                echo "<td>" . $user['email'] . "</td>";
                echo "<td>" . $user['role'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No users found in database.<br>";
        }
    } else {
        echo "❌ Users table does not exist<br>";
        echo "<p>Please create the users table with this SQL:</p>";
        echo "<textarea rows='8' cols='60'>
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    role ENUM('voter', 'group') NOT NULL,
    status INT DEFAULT 0,
    votes INT DEFAULT 0
);
        </textarea>";
    }
} else {
    echo "❌ Database connection failed: " . mysqli_connect_error();
}

echo "<br><br><a href='../routes/register.html'>Go to Registration Form</a>";
?>
