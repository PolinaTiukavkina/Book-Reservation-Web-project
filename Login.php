<!-- login.php -->
<?php
// Start the session to handle login
// Ensure session is started
include('header.php');
include('db.php'); // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $username = $_POST['Username'] ?? '';
    $password = $_POST['Password'] ?? '';

    // Prepare SQL query to fetch the user
    $query = "SELECT * FROM users WHERE Username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the user data
    $user = $result->fetch_assoc();

    // Check if the user exists and if the password is correct
    if ($user && $password==$user['Password']) {
    
        $_SESSION['Username'] = $user['Username']; // Store username in current session

        header("Location: index.php");//return to home page

        exit();//exit to prevent further execution
    } else {
        // Invalid login credentials
        echo "<p>Incorrect username or password!</p>";
    }

    $stmt->close(); //closing the statement
}

$conn->close();
?>

<main>
    <center>
        <h2>Login</h2>
        <!-- Login Form -->
        <div style="background-color:white;width:300px;height:185px;border-radius:15px;box-shadow:0 4px 6px rgba(0, 0, 0, 0.1);"><form action="Login.php" method="POST">
            <label for="Username">Username:</label>
            <br>
            <input type="text" name="Username" required><br><br>
            <label for="Password">Password:</label><br>
            <input type="password" name="Password" required><br><br>
            <button type="submit">Login</button>
        </form></div>
        <br>
        <p>Don't have an account? <a href="RegistrationPage.php">Register here</a></p>
    </center>
</main>

<?php
include('footer.php'); // Include footer
?>
