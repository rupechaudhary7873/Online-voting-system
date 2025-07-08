<?php
// Include database connection
include_once '../config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Check user in database
    $sql = "SELECT * FROM users WHERE username = '$username' AND role = '$role'";
    $result = mysqli_query($connect, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Check password (simple comparison for now)
        if ($password == $user['password']) {
            // Login successful
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            echo "<script>
                alert('Login Successful! Welcome " . $user['username'] . "');
                window.location.href = '../dashboard.html';
            </script>";
        } else {
            // Wrong password
            echo "<script>
                alert('Wrong password! Please try again.');
                window.location.href = '../index.html';
            </script>";
        }
    } else {
        // User not found
        echo "<script>
            alert('User not found! Please check username and role.');
            window.location.href = '../index.html';
        </script>";
    }
} else {
    // If accessed directly
    header("Location: ../index.html");
}
?>
