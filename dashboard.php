<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Online Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
   
    </style>
         
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h3 class="mb-0">Welcome to Voting Dashboard</h3>
                        <small>Online Voting System</small>
                        <div>
                            <a href="logout.php" class="btn btn-light">Logout</a>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h4>🗳️ You are successfully logged in!</h4>
                        <p class="text-muted">Choose an option below to continue:</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>👥 View Users</h5>
                                        <p>See all registered users</p>
                                        <button class="btn btn-primary" id="viewUsersBtn" onclick="viewUsers()">View Users</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>👥 Candidates</h5>
                                        <p>See all candidates</p>
                                        <button class="btn btn-primary" id="viewGroupsBtn" onclick="viewGroups()">View Candidates</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>🗳️ Vote Now</h5>
                                        <p>Cast your vote</p>
                                        <a href="Group/voting.php" class="btn btn-primary">Start Voting</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>📊 Results</h5>
                                        <p>View voting results</p>
                                        <a href="Group/results.php" class="btn btn-primary">View Results</a>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewUsers() {
           window.location.href = './user/users_list.php';
        }        
        function viewGroups(){
            window.location.href = './Group/groups_list.php';
        }
        function vote() {
            window.location.href = './Group/voting.php';
        }
    </script>
</body>
</html>
