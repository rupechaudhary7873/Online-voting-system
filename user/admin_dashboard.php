<?php
// Include database connection
include_once '../config.php';
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'group') {
    echo "<script>
        alert('Please login as admin to access this page.');
        window.location.href = 'bk-login.php';
    </script>";
    exit;
}

// Get user list for management
$users_query = mysqli_query($connect, "SELECT * FROM users ORDER BY role, username");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4>👨‍💼 Admin Dashboard</h4>
                <div>
                    <a href="../dashboard.php" class="btn btn-outline-light btn-sm me-2">Main Dashboard</a>
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>
            </div>
            <div class="card-body">
                <h5 class="mb-4">User Management</h5>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = mysqli_fetch_assoc($users_query)): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $user['role'] == 'admin' ? 'bg-danger' : 
                                                ($user['role'] == 'group' ? 'bg-primary' : 'bg-secondary'); 
                                        ?>">
                                            <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (isset($user['status'])): ?>
                                            <?php if ($user['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Unknown</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        
                                        <?php if ($user['role'] != 'admin' || $_SESSION['user_id'] != $user['id']): ?>
                                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this user?')">
                                                Delete
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['role'] == 'group'): ?>
                                            <a href="toggle_status.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-primary">
                                                Toggle Status
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="../Group/results.php" class="btn btn-outline-success">View Results</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
