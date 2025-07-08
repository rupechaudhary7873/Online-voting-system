<?php
include_once '../config.php';

// Modified SQL to fetch only voters
$sql = "SELECT * FROM users WHERE role = 'voter'";
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
    <title>Voters List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>👥 Registered Voters</h4>
                <a href="../dashboard.html" class="btn btn-sm btn-secondary float-end">Back to Dashboard</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Role</th>
                                <th>Photo</th>
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
                                echo "<td>" . ucfirst(htmlspecialchars($user['role'])) . "</td>";
                                
                                // Display user photo or default avatar
                                if (!empty($user['photo']) && file_exists('../uploads/' . $user['photo'])) {
                                    echo "<td><img src='../uploads/" . htmlspecialchars($user['photo']) . "' alt='Profile' class='img-thumbnail' style='width: 50px; height: 50px; object-fit: cover;'></td>";
                                } else {
                                    echo "<td><div class='bg-secondary rounded-circle mx-auto d-flex align-items-center justify-content-center' style='width: 50px; height: 50px; color: white; font-size: 24px; font-weight: bold;'>" . strtoupper(substr($user['username'], 0, 1)) . "</div></td>";
                                }
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                // Display voter count at the bottom
                $voter_count = mysqli_num_rows($results);
                echo "<div class='text-center mt-3'>";
                echo "<p class='fw-bold'>Total Voters: <span class='badge bg-success'>{$voter_count}</span></p>";
                echo "</div>";
                ?>
            </div>
        </div>
    </div>
</body>
</html>