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

// Get user information
$user_query = mysqli_query($connect, "SELECT * FROM users WHERE id = $user_id");

if (mysqli_num_rows($user_query) == 0) {
    echo "<script>
        alert('User not found.');
        window.location.href = 'admin_dashboard.php';
    </script>";
    exit;
}

$user = mysqli_fetch_assoc($user_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = isset($_POST['status']) ? 1 : 0;
    
    // Check if password is being changed
    $password_update = "";
    if (!empty($_POST['password'])) {
        $password_update = ", password = '{$_POST['password']}'";
    }
    
    $update_query = mysqli_query($connect, "UPDATE users SET 
                                          username = '$username',
                                          email = '$email',
                                          role = '$role',
                                          status = $status
                                          $password_update
                                          WHERE id = $user_id");
    
    if ($update_query) {
        echo "<script>
            alert('User updated successfully.');
            window.location.href = 'admin_dashboard.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Error updating user: " . mysqli_error($connect) . "');
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4>Edit User</h4>
                <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm float-end">Back to Dashboard</a>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter new password to change">
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="voter" <?php echo ($user['role'] == 'voter') ? 'selected' : ''; ?>>Voter</option>
                            <option value="group" <?php echo ($user['role'] == 'group') ? 'selected' : ''; ?>>Group/Candidate</option>
                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" 
                               <?php echo (isset($user['status']) && $user['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
