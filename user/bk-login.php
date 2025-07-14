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
            $_SESSION['role'] = $user['group'];
            
            echo "<script>
                alert('Login Successful! Welcome " . $user['username'] . "');
                window.location.href = '../dashboard.php';
            </script>";
        } else {
            // Wrong password
            echo "<script>
                alert('Wrong password! Please try again.');
                window.location.href = '../index.php';
            </script>";
        }
    } else {
        // User not found
        echo "<script>
            alert('User not found! Please check username and role.');
            window.location.href = '../index.php';
        </script>";
    }
} else {
    // If form is not submitted, just show the login form
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Backend of Online Voting System</h3>
                    </div>
                    <div class="card-body">
                        <!-- Login Form -->
                        <form action="Backend.php" method="POST">
                            
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Admin Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Enter admin username" required>
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                            </div>
                            
                            <!-- Hidden role field -->
                            <input type="hidden" name="role" value="admin">
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Login to Backend</button>
                                <a href="../dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
