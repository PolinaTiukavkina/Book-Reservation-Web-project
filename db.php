<!-- db.php -->
<?php
// Database connection settings
$servername = 'localhost';
$dbname= 'testdb';
$username = 'root';
$pass = ''; 

// Create connection
$conn = new mysqli($servername, $username, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




?>