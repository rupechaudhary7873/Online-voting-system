<?php
include_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>⚙️ Setting Up Voting System Database</h4>
                <a href="../dashboard.html" class="btn btn-sm btn-light float-end">Back to Dashboard</a>
            </div>
            <div class="card-body">
                <h5 class="mb-3">Setup Progress:</h5>
                
                <div class="progress mb-4">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                
                <div id="setup-log" class="border p-3" style="max-height: 400px; overflow-y: auto;">
                    <!-- Setup logs will appear here -->
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Function to update progress bar
        function updateProgress(percent) {
            document.querySelector('.progress-bar').style.width = percent + '%';
        }
        
        // Function to add log entry
        function addLog(message, type = 'info') {
            const logDiv = document.getElementById('setup-log');
            const logEntry = document.createElement('div');
            
            let icon = '🔄';
            if (type === 'success') icon = '✅';
            if (type === 'error') icon = '❌';
            if (type === 'warning') icon = '⚠️';
            
            logEntry.innerHTML = `<p class="mb-2 ${type}">${icon} ${message}</p>`;
            logDiv.appendChild(logEntry);
            logDiv.scrollTop = logDiv.scrollHeight;
        }
        
        // Start setup process
        document.addEventListener('DOMContentLoaded', function() {
            addLog('Starting setup process...');
            updateProgress(10);
            
            // Use fetch API to run setup operations
            fetch(window.location.href + '?run_setup=1', {
                method: 'GET',
            })
            .then(response => response.text())
            .then(html => {
                // Parse the response HTML to extract setup results
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const results = doc.querySelectorAll('.setup-result');
                
                let progress = 20;
                let progressStep = 80 / results.length;
                
                results.forEach(result => {
                    const success = result.getAttribute('data-success') === 'true';
                    const message = result.textContent;
                    
                    addLog(message, success ? 'success' : 'error');
                    progress += progressStep;
                    updateProgress(Math.min(progress, 100));
                });
                
                // Final message
                addLog('Setup complete!', 'success');
                updateProgress(100);
            })
            .catch(error => {
                addLog('Error during setup: ' + error, 'error');
            });
        });
    </script>

<?php
// Only run setup operations when specifically requested
if (isset($_GET['run_setup'])) {
    // Function to output setup result
    function outputSetupResult($message, $success) {
        echo "<div class='setup-result' data-success='" . ($success ? 'true' : 'false') . "'>" . $message . "</div>";
        ob_flush();
        flush();
        // Add a small delay to make the progress visual
        usleep(300000);
    }
    
    // Check if users table exists, if not create it
    $check_users_table = mysqli_query($connect, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($check_users_table) == 0) {
        $create_users = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            contact VARCHAR(20),
            password VARCHAR(255) NOT NULL,
            photo VARCHAR(255),
            role ENUM('admin', 'user', 'voter', 'group') DEFAULT 'user',
            status TINYINT DEFAULT 0,
            votes INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $success = mysqli_query($connect, $create_users);
        outputSetupResult("Creating users table: " . ($success ? "Success" : "Failed - " . mysqli_error($connect)), $success);
        
        if ($success) {
            // Create default admin user
            $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
            $insert_admin = mysqli_query($connect, "INSERT INTO users (username, email, password, role, status) 
                                                  VALUES ('admin', 'admin@voting.com', '$admin_password', 'admin', 1)");
            outputSetupResult("Creating default admin user: " . ($insert_admin ? "Success" : "Failed"), $insert_admin);
        }
    } else {
        outputSetupResult("Users table already exists.", true);
    }
    
    // Check if votes_history table exists, if not create it
    $check_table = mysqli_query($connect, "SHOW TABLES LIKE 'votes_history'");
    if (mysqli_num_rows($check_table) == 0) {
        // Create votes_history table
        $create_table = "CREATE TABLE votes_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            voter_id INT NOT NULL,
            candidate_id INT NOT NULL,
            voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (voter_id) REFERENCES users(id),
            FOREIGN KEY (candidate_id) REFERENCES users(id),
            UNIQUE KEY unique_vote (voter_id)
        )";
        
        $success = mysqli_query($connect, $create_table);
        outputSetupResult("Creating votes history table: " . ($success ? "Success" : "Failed - " . mysqli_error($connect)), $success);
    } else {
        outputSetupResult("Votes history table already exists.", true);
    }

    // Add status column to users table if it doesn't exist
    $check_status = mysqli_query($connect, "SHOW COLUMNS FROM users LIKE 'status'");
    if (mysqli_num_rows($check_status) == 0) {
        $success = mysqli_query($connect, "ALTER TABLE users ADD COLUMN status TINYINT DEFAULT 0");
        outputSetupResult("Adding status column to users table: " . ($success ? "Success" : "Failed - " . mysqli_error($connect)), $success);
    } else {
        outputSetupResult("Status column already exists in users table.", true);
    }

    // Add votes column to users table if it doesn't exist
    $check_votes = mysqli_query($connect, "SHOW COLUMNS FROM users LIKE 'votes'");
    if (mysqli_num_rows($check_votes) == 0) {
        $success = mysqli_query($connect, "ALTER TABLE users ADD COLUMN votes INT DEFAULT 0");
        outputSetupResult("Adding votes column to users table: " . ($success ? "Success" : "Failed - " . mysqli_error($connect)), $success);
    } else {
        outputSetupResult("Votes column already exists in users table.", true);
    }

    // Activate all group users for testing
    $success = mysqli_query($connect, "UPDATE users SET status = 1 WHERE role = 'group'");
    outputSetupResult("Activating all group users for voting: " . ($success ? "Success" : "Failed - " . mysqli_error($connect)), $success);
    
    // Reset votes for demo/test purposes if requested
    if (isset($_GET['reset_votes']) && $_GET['reset_votes'] == 1) {
        $reset_votes = mysqli_query($connect, "UPDATE users SET votes = 0 WHERE 1");
        $delete_history = mysqli_query($connect, "TRUNCATE TABLE votes_history");
        
        if ($reset_votes && $delete_history) {
            outputSetupResult("Reset all votes and voting history for testing.", true);
        } else {
            outputSetupResult("Failed to reset votes: " . mysqli_error($connect), false);
        }
    }
    
    exit;
}
?>

</body>
</html>
