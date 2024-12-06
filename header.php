<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reservation System</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Book Reservation System</h1>
            <hr>
            <div id="menu">
                <nav >
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <?php if (isset($_SESSION['Username'])): ?>
                            <li><a href="SearchBooksPage.php">Search Books</a></li>
                            <li><a href="displayReservedPage.php">My Reserved Books</a></li>
                            <li><a href="Logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="Login.php">Login</a></li>
                            <li><a href="RegistrationPage.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>