<?php
// Include database connection
include_once '../config.php';
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Should be 'admin'
    
    // Check admin in database
    $sql = "SELECT * FROM users WHERE username = '$username' AND role = 'group'";
    $result = mysqli_query($connect, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Check password (simple comparison for now)
        if ($password == $user['password']) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'group';
            
            // Redirect to admin dashboard
            echo "<script>
                alert('Admin Login Successful! Welcome " . $user['username'] . "');
                window.location.href = 'admin_dashboard.php';
            </script>";
        } else {
            // Wrong password
            echo "<script>
                alert('Wrong admin password! Please try again.');
                window.location.href = 'bk-login.php';
            </script>";
        }
    } else {
        // Admin not found
        echo "<script>
            alert('Admin user not found! Please check credentials.');
            window.location.href = 'bk-login.php';
        </script>";
    }
} else {
    // If accessed directly
    header("Location: bk-login.php");
}
?>
