<?php
include_once '../config.php';

// Start session to check if user is admin
session_start();

// Only admin should be able to manage candidate status
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';

// Handle status toggle if requested by admin
if ($is_admin && isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $toggle_sql = "UPDATE users SET status = IF(status=1, 0, 1) WHERE id = $id AND role = 'group'";
    if (mysqli_query($connect, $toggle_sql)) {
        // Status updated successfully
        header("Location: groups_list.php?status_updated=1");
        exit;
    }
}

// Modified SQL to fetch only groups with status info
$sql = "SELECT * FROM users WHERE role = 'group'";
$results = mysqli_query($connect, $sql);

if (!$results) {
    echo "Error: " . mysqli_error($connect);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>👥 Registered Groups (Candidates)</h4>
                <a href="../dashboard.html" class="btn btn-sm btn-light float-end">Back to Dashboard</a>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['status_updated'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Candidate status updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Votes</th>
                                <th>Status</th>
                                <th>Photo</th>
                                <?php if ($is_admin): ?>
                                <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            while ($user = mysqli_fetch_assoc($results)) {
                                echo "<tr>";
                                echo "<td>" . $count++ . "</td>";
                                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['contact']) . "</td>";
                                
                                // Show votes count
                                echo "<td>" . (isset($user['votes']) ? $user['votes'] : '0') . "</td>";
                                
                                // Show status with badge
                                $status = isset($user['status']) && $user['status'] == 1 ? 
                                    '<span class="badge bg-success">Active</span>' : 
                                    '<span class="badge bg-danger">Inactive</span>';
                                echo "<td>" . $status . "</td>";
                                
                                // Display user photo or default avatar
                                if (!empty($user['photo']) && file_exists('../uploads/' . $user['photo'])) {
                                    echo "<td><img src='../uploads/" . htmlspecialchars($user['photo']) . "' alt='Profile' class='img-thumbnail' style='width: 50px; height: 50px; object-fit: cover;'></td>";
                                } else {
                                    echo "<td><div class='bg-secondary rounded-circle mx-auto d-flex align-items-center justify-content-center' style='width: 50px; height: 50px; color: white; font-size: 24px; font-weight: bold;'>" . strtoupper(substr($user['username'], 0, 1)) . "</div></td>";
                                }
                                
                                // Admin actions - toggle status
                                if ($is_admin) {
                                    $action_text = (isset($user['status']) && $user['status'] == 1) ? 
                                        'Deactivate' : 'Activate';
                                    $btn_class = (isset($user['status']) && $user['status'] == 1) ? 
                                        'btn-outline-danger' : 'btn-outline-success';
                                    
                                    echo "<td>
                                        <a href='groups_list.php?toggle_status=1&id={$user['id']}' 
                                           class='btn btn-sm {$btn_class}' 
                                           onclick='return confirm(\"Are you sure you want to {$action_text} this candidate?\")'>
                                            {$action_text}
                                        </a>
                                      </td>";
                                }
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                // Display group count at the bottom
                $group_count = mysqli_num_rows($results);
                echo "<div class='text-center mt-3'>";
                echo "<p class='fw-bold'>Total Groups: <span class='badge bg-success'>{$group_count}</span></p>";
                echo "</div>";
                ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>