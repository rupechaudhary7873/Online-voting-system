<?php
include_once '../config.php';
session_start();

// Get all candidates with their vote counts
$sql = "SELECT id, username, photo, votes, status FROM users WHERE role = 'group' ORDER BY votes DESC, username ASC";
$results = mysqli_query($connect, $sql);

// Get total votes cast
$total_votes_query = mysqli_query($connect, "SELECT COUNT(*) as total FROM votes_history");
$total_votes_row = mysqli_fetch_assoc($total_votes_query);
$total_votes = $total_votes_row['total'];

// Check if user is admin (for special actions)
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';

// Get voter information if the user is logged in
$voter_info = null;
if (isset($_SESSION['user_id'])) {
    $voter_id = $_SESSION['user_id'];
    $voter_query = mysqli_query($connect, "SELECT vh.candidate_id, u.username as candidate_name, vh.voted_at 
                                         FROM votes_history vh 
                                         JOIN users u ON vh.candidate_id = u.id 
                                         WHERE vh.voter_id = $voter_id");
    if (mysqli_num_rows($voter_query) > 0) {
        $voter_info = mysqli_fetch_assoc($voter_query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>📊 Election Results</h4>
                <a href="../dashboard.html" class="btn btn-sm btn-light float-end">Back to Dashboard</a>
            </div>
            <div class="card-body">
                <h5 class="mb-4">Current Voting Results:</h5>
                
                <?php if (mysqli_num_rows($results) > 0): ?>
                
                    <!-- Voter information (if logged in and voted) -->
                    <?php if ($voter_info): ?>
                    <div class="alert alert-info">
                        <p>You voted for <strong><?php echo htmlspecialchars($voter_info['candidate_name']); ?></strong> on 
                           <strong><?php echo date('F j, Y \a\t g:i a', strtotime($voter_info['voted_at'])); ?></strong>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Total votes counter -->
                    <div class="alert alert-success text-center">
                        <strong>Total Votes Cast: <?php echo $total_votes; ?></strong>
                    </div>
                    
                    <!-- Results table -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Candidate</th>
                                <th>Status</th>
                                <th>Votes</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            while ($candidate = mysqli_fetch_assoc($results)): 
                                // Calculate percentage
                                $percentage = ($total_votes > 0) ? round(($candidate['votes'] / $total_votes) * 100, 2) : 0;
                                
                                // Determine if this is the leading candidate
                                $is_leading = ($rank == 1 && $candidate['votes'] > 0);
                            ?>
                                <tr <?php echo $is_leading ? 'class="table-success"' : ''; ?>>
                                    <td>
                                        <?php 
                                        echo $rank++; 
                                        if ($is_leading) echo ' 🏆';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($candidate['photo']) && file_exists('../uploads/' . $candidate['photo'])): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($candidate['photo']); ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; font-size: 18px;">
                                                    <?php echo strtoupper(substr($candidate['username'], 0, 1)); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($candidate['username']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($candidate['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $candidate['votes']; ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?php echo $is_leading ? 'bg-success' : 'bg-primary'; ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $percentage; ?>%;" 
                                                 aria-valuenow="<?php echo $percentage; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?php echo $percentage; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($is_admin): ?>
                    <div class="text-center mt-4">
                        <a href="setup.php?run_setup=1&reset_votes=1" class="btn btn-danger" 
                           onclick="return confirm('WARNING: This will reset all votes and voting history. Continue?');">
                            Reset All Votes (Admin Only)
                        </a>
                    </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-warning">
                        <p>No candidate data available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="voting.php" class="btn btn-primary me-2">Go to Voting</a>
                <a href="../dashboard.html" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
