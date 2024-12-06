<?php
// Start session to handle login status
include('header.php'); // Include the header
include('db.php'); // Include database connection (mysqli version)


// Initialize variables
$username = $password = $confirmPassword = $firstName = $surname = $addressLine1 = $addressLine2 = $city = $telephone = $mobile = '';
$errorMessage = '';

// Process the form submission when it occurs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize inputs
  $username = htmlspecialchars($_POST['Username']);
  $password = $_POST['Password'];
  $confirmPassword = $_POST['ConfirmPassword'];
  $firstName = htmlspecialchars($_POST['FirstName']);
  $surname = htmlspecialchars($_POST['Surname']);
  $addressLine1 = htmlspecialchars($_POST['AddressLine']);  
  $addressLine2 = htmlspecialchars($_POST['AddressLine2']);
  $city = htmlspecialchars($_POST['City']);
  $telephone = $_POST['Telephone']; 
  $mobile = $_POST['Mobile'];

  // Validate the inputs
  if (strlen($password) != 6) {
    $errorMessage = "Password must be 6 characters long!";
  } elseif ($password !== $confirmPassword) {
    $errorMessage = "Passwords do not match!";
  } elseif (!is_numeric($telephone) || strlen($telephone) != 10) {
    $errorMessage = "Telephone number must be numeric and 10 digits long!";
  } elseif (!is_numeric($mobile) || strlen($mobile) != 10) {
    $errorMessage = "Mobile number must be numeric and 10 digits long!";
  } else {
    // Check if the username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
      $errorMessage = "Username already exists. Please choose a different username.";
    } else {
      

        $stmt = $conn->prepare("INSERT INTO users (username, password, FirstName, Surname, Addressline, Addressline2, City, Telephone, Mobile) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssii", $username, $password, $firstName, $surname, $addressLine1, $addressLine2, $city, $telephone, $mobile);
        $stmt->execute();

        // Redirect to login page after successful registration
        header("Location: Login.php");
        exit();
      } 
    }
  }

?>

<main>
    <!-- Display error message if there is one -->
    <?php if (!empty($errorMessage)) { echo "<p style='color: red;'>$errorMessage</p>"; } ?>

    <!-- Registration Form -->
    
        <form action="RegistrationPage.php" method="POST">
            <div class="regg">
                <table>
                    <thead>
                        <tr><th><h2 style="text-align:left">Register</h2></th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label for="Username">Username:</label>
                                <input type="text" name="Username" placeholder="Enter username" value="<?= htmlspecialchars($username) ?>" required><br><br>
                                <label for="Password">Password:</label>
                                <input type="password" name="Password" placeholder="Enter password" required><br><br>
                                <label for="ConfirmPassword">Confirm Password:</label>
                                <input type="password" name="ConfirmPassword" placeholder="Confirm password" required><br><br>
                                <label for="FirstName">First Name:</label>
                                <input type="text" name="FirstName" placeholder="Enter first name" value="<?= htmlspecialchars($firstName) ?>" required><br><br>
                                <label for="Surname">Last Name:</label>
                                <input type="text" name="Surname" placeholder="Enter last name" value="<?= htmlspecialchars($surname) ?>" required><br><br>
                            </td>
                            <td>
                                <label for="AddressLine">Address Line 1:</label>
                                <input type="text" name="AddressLine" placeholder="Enter address" value="<?= htmlspecialchars($addressLine1) ?>" required><br><br>
                                <label for="AddressLine2">Address Line 2:</label>
                                <input type="text" name="AddressLine2" placeholder="Enter address" value="<?= htmlspecialchars($addressLine2) ?>"><br><br>
                                <label for="City">City:</label>
                                <input type="text" name="City" placeholder="Enter city" value="<?= htmlspecialchars($city) ?>" required><br><br>
                                <label for="Telephone">Telephone:</label>
                                <input type="text" name="Telephone" placeholder="Enter telephone" value="<?= htmlspecialchars($telephone) ?>" required><br><br>
                                <label for="Mobile">Mobile:</label>
                                <input type="text" name="Mobile" placeholder="Enter mobile" value="<?= htmlspecialchars($mobile) ?>" required><br><br>
                            </td>
                        </tr>
                    </tbody>
                </table>
            
            <button id="reg" type="submit"><h4>Register</h4></button>
            <p>Already have an account? <a href="Login.php">Sign in</a>.</p>
        </form>
    </div>
</main>

<?php
include('footer.php'); // Include the footer
?>
