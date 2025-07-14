<?php
// Start session and include database connection
include_once '../config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in
    echo "<script>
        alert('Please login to cast your vote.');
        window.location.href = '../index.php';
    </script>";
    exit;
}

// Get voter ID
$voter_id = $_SESSION['user_id'];

// Check if voter has already voted
$check_vote = mysqli_query($connect, "SELECT * FROM votes_history WHERE voter_id = $voter_id");
if (mysqli_num_rows($check_vote) > 0) {
    // Already voted
    echo "<script>
        alert('You have already voted in this election.');
        window.location.href = 'results.php';
    </script>";
    exit;
}

// Check if a candidate was selected
if (!isset($_POST['vote_for']) || empty($_POST['vote_for'])) {
    echo "<script>
        alert('Please select a candidate to vote for.');
        window.location.href = 'voting.php';
    </script>";
    exit;
}

// Get candidate ID
$candidate_id = $_POST['vote_for'];

// Validate candidate exists (allow any status)
$check_candidate = mysqli_query($connect, "SELECT * FROM users WHERE id = $candidate_id AND role = 'group'");
if (mysqli_num_rows($check_candidate) == 0) {
    echo "<script>
        alert('Invalid candidate selection.');
        window.location.href = 'voting.php';
    </script>";
    exit;
}

// Get candidate name for confirmation message
$candidate_data = mysqli_fetch_assoc($check_candidate);
$candidate_name = htmlspecialchars($candidate_data['username']);

// Start transaction to ensure data consistency
mysqli_begin_transaction($connect);

try {
    // 1. Update candidate's vote count
    $update_votes = mysqli_query($connect, "UPDATE users SET votes = votes + 1 WHERE id = $candidate_id");
    
    // 2. Record vote in history table with timestamp
    $timestamp = date('Y-m-d H:i:s');
    $record_vote = mysqli_query($connect, "INSERT INTO votes_history (voter_id, candidate_id, voted_at) VALUES ($voter_id, $candidate_id, '$timestamp')");
    
    // If both operations successful, commit transaction
    if ($update_votes && $record_vote) {
        mysqli_commit($connect);
        echo "<script>
            alert('Your vote for $candidate_name has been cast successfully! Thank you for participating.');
            window.location.href = 'results.php';
        </script>";
    } else {
        // Something went wrong, rollback
        mysqli_rollback($connect);
        echo "<script>
            alert('Error processing your vote. Please try again later.');
            window.location.href = 'voting.php';
        </script>";
    }
} catch (Exception $e) {
    // Error occurred, rollback transaction
    mysqli_rollback($connect);
    echo "<script>
        alert('Error: " . mysqli_error($connect) . "');
        window.location.href = 'voting.php';
    </script>";
}
?>
