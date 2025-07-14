<?php
include_once '../config.php';
session_start();

// Simple check for login status
$logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Get total votes - simple count query
$total_votes = 0;
$votes_query = mysqli_query($connect, "SELECT COUNT(*) as total FROM votes_history");
if($votes_query) {
    $total_votes = mysqli_fetch_assoc($votes_query)['total'];
}

// Check if voter has already voted - simple approach
$has_voted = false;
$voted_for = '';
$voted_name = '';

if ($logged_in) {
    $voter_id = $_SESSION['user_id'];
    $check_query = mysqli_query($connect, "SELECT vh.candidate_id, u.username 
                                         FROM votes_history vh 
                                         JOIN users u ON vh.candidate_id = u.id 
                                         WHERE vh.voter_id = $voter_id");
    if ($check_query && mysqli_num_rows($check_query) > 0) {
        $has_voted = true;
        $vote_data = mysqli_fetch_assoc($check_query);
        $voted_for = $vote_data['candidate_id'];
        $voted_name = $vote_data['username'];
    }
}

// Get ALL candidates (not just active ones)
$sql = "SELECT * FROM users WHERE role = 'group' ORDER BY username";
$candidates = mysqli_query($connect, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>🗳️ Vote for your Candidate</h4>
                <a href="../dashboard.php" class="btn btn-sm btn-secondary float-end">Back to Dashboard</a>
            </div>
            <div class="card-body">
                <!-- Simple stats display -->
                <div class="alert alert-info">
                    <b>Total Votes: <?php echo $total_votes; ?></b>
                    <a href="results.php" class="btn btn-sm btn-primary float-end">View Results</a>
                </div>
                
                <!-- Login check -->
                <?php if (!$logged_in): ?>
                    <div class="alert alert-warning">
                        <h5>You need to login first!</h5>
                        <p>Please <a href="../index.php">login</a> to vote.</p>
                    </div>
                
                <!-- Already voted message -->
                <?php elseif ($has_voted): ?>
                    <div class="alert alert-success">
                        <h5>You already voted!</h5>
                        <p>Thanks for voting. You voted for: <b><?php echo htmlspecialchars($voted_name); ?></b></p>
                    </div>
                    
                <!-- Voting form -->
                <?php else: ?>
                    <h5 class="mb-3">Select a candidate:</h5>
                    
                    <?php if (mysqli_num_rows($candidates) == 0): ?>
                        <p>No candidates available.</p>
                    <?php else: ?>
                        <form action="submit_vote.php" method="POST">
                            <div class="row">
                                <?php while ($candidate = mysqli_fetch_assoc($candidates)): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-2">
                                                    <?php if (!empty($candidate['photo']) && file_exists('../uploads/' . $candidate['photo'])): ?>
                                                        <img src="../uploads/<?php echo $candidate['photo']; ?>" class="rounded-circle me-2" style="width: 60px; height: 60px;">
                                                    <?php else: ?>
                                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 60px; height: 60px; font-size: 24px;">
                                                            <?php echo strtoupper(substr($candidate['username'], 0, 1)); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h5 class="mb-0"><?php echo htmlspecialchars($candidate['username']); ?></h5>
                                                        <div>Votes: <span class="badge bg-secondary"><?php echo $candidate['votes']; ?></span></div>
                                                        
                                                        <!-- Show status -->
                                                        <?php if (isset($candidate['status']) && $candidate['status'] == 0): ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactive</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <!-- Allow voting for all candidates -->
                                                <button type="submit" name="vote_for" value="<?php echo $candidate['id']; ?>" 
                                                    class="btn btn-primary w-100"
                                                    onclick="return confirm('Vote for <?php echo htmlspecialchars($candidate['username']); ?>?');">
                                                    Vote for this candidate
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="mt-3">
                                <p><i>Note: You can only vote once.</i></p>
                            </div>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="results.php" class="btn btn-outline-primary">See Current Results</a>
            </div>
        </div>
    </div>
</body>
</html>