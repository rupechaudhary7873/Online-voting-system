<?php
// Include database connection
include_once '../config.php';

// Get all users from database
$sql = "SELECT id, username, email, contact, photo, role FROM users ORDER BY id DESC";
$result = mysqli_query($connect, $sql);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="row">';
    
    while ($user = mysqli_fetch_assoc($result)) {
        echo '<div class="col-md-6 col-lg-4 mb-3">';
        echo '<div class="card h-100">';
        echo '<div class="card-body text-center">';
        
        // Display user image or default avatar
        if (!empty($user['photo']) && file_exists('../uploads/' . $user['photo'])) {
            echo '<img src="uploads/' . $user['photo'] . '" alt="Profile" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">';
        } else {
            echo '<div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; color: white; font-size: 24px; font-weight: bold;">';
            echo strtoupper(substr($user['username'], 0, 1));
            echo '</div>';
        }
        
        echo '<h5 class="card-title">' . htmlspecialchars($user['username']) . '</h5>';
        echo '<p class="card-text">';
        echo '<small class="text-muted">📧 ' . htmlspecialchars($user['email']) . '</small><br>';
        echo '<small class="text-muted">📞 ' . htmlspecialchars($user['contact']) . '</small><br>';
        
        // Role badge
        $badgeClass = $user['role'] == 'voter' ? 'bg-primary' : 'bg-success';
        echo '<span class="badge ' . $badgeClass . ' mt-2">' . ucfirst(htmlspecialchars($user['role'])) . '</span>';
        echo '</p>';
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    
    // Show total count
    $total = mysqli_num_rows($result);
    echo '<div class="text-center mt-3">';
    echo '<p class="text-muted">Total Users: <strong>' . $total . '</strong></p>';
    echo '</div>';
    
} else {
    echo '<div class="text-center">';
    echo '<h5>No users found</h5>';
    echo '<p class="text-muted">No users have registered yet.</p>';
    echo '<a href="routes/register.html" class="btn btn-primary">Register First User</a>';
    echo '</div>';
}

// Close database connection
mysqli_close($connect);
?>
