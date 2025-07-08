<?php   
$hostname="localhost";
$username="root";   
$password= "";
$dbname= "voting";
$connect=mysqli_connect("localhost","root","","voting");
if(!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
?>