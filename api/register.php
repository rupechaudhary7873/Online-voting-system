<?php
// Include database connection
include_once '../config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Step 1: Get all form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $role = $_POST['role'];

    
    if ($password == $confirm_password) {
        
      
        $image_name = NULL;
        if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
            $image_name = $_FILES['image']['name'];
            
         
            if (!file_exists('../uploads/')) {
                mkdir('../uploads/', 0777, true);
            }
            
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image_name);
        }
        
      
        $sql = "INSERT INTO users (username, password, email, contact, photo, role, status, votes) 
                VALUES ('$username', '$password', '$email', '$contact', '$image_name', '$role', 0, 0)";
        
        $result = mysqli_query($connect, $sql);

        if ($result) {
            echo "<script>
                alert('Registration Successful! Welcome $username. Please login now.');
                window.location.href = '../index.php';
            </script>";
        } else {
            echo "<script>
                alert('Registration Failed! Please try again.');
                window.location.href = '../routes/register.html';
            </script>";
        }
        
    } else {
        // Passwords don't match
        echo "<script>
            alert('Passwords do not match! Please try again.');
            window.location.href = '../routes/register.html';
        </script>";
    }
} else {
    // If someone tries to access this file directly
    header("Location: ../routes/register.html");
}
?>