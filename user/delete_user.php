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

// Make sure admin is not deleting themselves
if ($user_id == $_SESSION['user_id']) {
    echo "<script>
        alert('You cannot delete your own admin account.');
        window.location.href = 'admin_dashboard.php';
    </script>";
    exit;
}

// Check for related votes before deleting a user
$check_votes = mysqli_query($connect, "SELECT * FROM votes_history WHERE voter_id = $user_id OR candidate_id = $user_id");
$has_votes = mysqli_num_rows($check_votes) > 0;

if ($has_votes) {
    // Delete votes first if they exist
    $delete_votes = mysqli_query($connect, "DELETE FROM votes_history WHERE voter_id = $user_id OR candidate_id = $user_id");
    
    if (!$delete_votes) {
        echo "<script>
            alert('Error deleting user votes: " . mysqli_error($connect) . "');
            window.location.href = 'admin_dashboard.php';
        </script>";
        exit;
    }
}

// Now delete the user
$delete_user = mysqli_query($connect, "DELETE FROM users WHERE id = $user_id");

if ($delete_user) {
    echo "<script>
        alert('User has been deleted successfully.');
        window.location.href = 'admin_dashboard.php';
    </script>";
} else {
    echo "<script>
        alert('Error deleting user: " . mysqli_error($connect) . "');
        window.location.href = 'admin_dashboard.php';
    </script>";
}
?>
