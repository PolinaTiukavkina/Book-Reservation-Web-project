<?php
// Display reserved books for the logged-in user
include('db.php');
include('header.php');

$username = $_SESSION['Username'];  


$query = "SELECT b.BookTitle, b.Author, rb.ReservedDate, rb.ISBN
          FROM reservedbooks rb
          JOIN book b ON rb.ISBN = b.ISBN
          WHERE rb.Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$reservedbooks = $result->fetch_all(MYSQLI_ASSOC);

echo "<center><h2>" . htmlspecialchars($_SESSION['Username']) . "'s reserved Books: </h2></center>";
if (empty($reservedbooks)) {
    echo "<center><p>You have no reserved books.</p></center>";
} else {
    foreach ($reservedbooks as $book) {
        echo "<center>";
        echo "<strong>Title:</strong> " . htmlspecialchars($book['BookTitle']) . " by " . htmlspecialchars($book['Author']) . "<br>";
        echo "<strong>Reserved on:</strong> " . htmlspecialchars($book['ReservedDate']) . "<br>";
        echo "<br><form method='POST' action='displayReservedPage.php'>
                  <input type='hidden' name='isbn' value='" . htmlspecialchars($book['ISBN']) . "' />
                  <button class='reservation'type='submit' name='remove' value='1'>Remove Reservation</button>
              </form>";
        echo "</center><br>";
    }
}

// Check if the form was submitted to remove a reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    $isbn = $_POST['isbn'];  // Ensure this matches the input field
    $username = $_SESSION['Username'];  // Consistent session variable

    // Remove the reservation from the ReservedBooks table
    $deleteQuery = "DELETE FROM reservedbooks WHERE Username = ? AND ISBN = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("ss", $username, $isbn);
    $stmt->execute();

    // Update the book's availability in the Books table (set it back to available)
    $updateQuery = "UPDATE book SET Reserved = 'N' WHERE ISBN = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("s", $isbn);
    $stmt->execute();

    
    
    // Redirect back to the reserved books page
    header('Location: displayReservedPage.php');
    exit();
}

$stmt->close();
$conn->close();

include('footer.php'); // Include the footer

?>
