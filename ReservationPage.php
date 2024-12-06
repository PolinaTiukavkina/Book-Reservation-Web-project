<?php

include('db.php');
include('header.php');

$isbn = $_POST['ISBN'] ?? ''; // ISBN of the selected book
$username = $_SESSION['Username']; // store the username of person who logged in

// Check if the book is not reserved
$query = "SELECT Reserved FROM book WHERE ISBN = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $isbn);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

if ($book === null) {
    echo "Book not found.";
} elseif ($book['Reserved'] == 'N') {
    
    $reserve = "INSERT INTO reservedbooks (Username, ISBN) VALUES (?, ?)";
    $stmt = $conn->prepare($reserve);
    $stmt->bind_param("ss", $username, $isbn);
    $stmt->execute();

    // Update the reservation date
    $current_date = date('Y-m-d');
    $updateReservedDateQuery = "UPDATE reservedbooks 
                                SET ReservedDate = ? 
                                WHERE Username = ? AND ISBN = ?";
    $stmt = $conn->prepare($updateReservedDateQuery);
    $stmt->bind_param("sss", $current_date, $username, $isbn);
    $stmt->execute();

    // Update book availability
    $updateBookQuery = "UPDATE book SET Reserved = 'Y' WHERE ISBN = ?";
    $stmt = $conn->prepare($updateBookQuery);
    $stmt->bind_param("s", $isbn);
    $stmt->execute();

    echo "<br><center>Book reserved successfully!</center>";
} else {
    echo "This book is already reserved.";
}

$stmt->close();
$conn->close();
?>
