<?php
// Include database connection
include_once '../config.php';
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>
        alert('Please login as admin to access this page.');
        window.location.href = 'bk-login.php';
    </script>";
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('No user ID specified.');
        window.location.href = 'admin_dashboard.php';
    </script>";
    exit;
}

$user_id = $_GET['id'];

// Toggle status
$toggle_query = mysqli_query($connect, "UPDATE users SET status = IF(status=1, 0, 1) WHERE id = $user_id");

if ($toggle_query) {
    echo "<script>
        alert('User status has been updated.');
        window.location.href = 'admin_dashboard.php';
    </script>";
} else {
    echo "<script>
        alert('Error updating user status: " . mysqli_error($connect) . "');
        window.location.href = 'admin_dashboard.php';
    </script>";
}
?>
