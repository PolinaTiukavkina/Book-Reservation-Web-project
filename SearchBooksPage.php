<?php
include('header.php');
include('db.php');


$categories = []; //creating an array to store all categories
$categoryQuery = "SELECT * FROM category";
if ($categoryResult = $conn->query($categoryQuery)) {
    while ($row = $categoryResult->fetch_assoc()) 
    {
        $categories[] = $row;
    }
    $categoryResult->free();
}

// Search query
$searchTitle = $_GET['title'] ?? '';
$searchAuthor = $_GET['author'] ?? '';
$categoryID = $_GET['category'] ?? '';
$displayAll = isset($_GET['display_all']); // Check if "Display All Books" is clicked

// Check if any field is filled
$filtered = !empty($searchTitle) || !empty($searchAuthor) || !empty($categoryID);


$limit = 5; //max number of books displayed per page
$instances = isset($_GET['instances']) ? (int)$_GET['instances'] : 1;
$offset = ($instances - 1) * $limit; //we are gonna skip displaying books displayed on previous page and start displaying next 5 books

if ($displayAll) 
{ 
    // If "Display All Books" button is clicked, reset filters and show all books
    $query = "SELECT * FROM book LIMIT ? OFFSET ?";  //limit is number of instances per page and offset is starting instance
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset); //ii: both variables are integers. WIll replace limit and offset with values
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);

    $totalBooksNum = $conn->query("SELECT COUNT(*) AS count FROM book"); //counts total number of rows in the book table
    $totalBooks = $totalBooksNum->fetch_assoc()['count'];
    $totalPages = ceil($totalBooks / $limit);
} 
elseif ($filtered) 
{
    // If search filters are used
    $query = "SELECT * FROM book 
              WHERE BookTitle LIKE ? 
              AND Author LIKE ? 
              AND (CategoryCode LIKE ? OR ? = '') 
              LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $searchTitleParam = "%$searchTitle%";
    $searchAuthorParam = "%$searchAuthor%";
    $categoryParam = "%$categoryID%";
    $stmt->bind_param("ssssii", $searchTitleParam, $searchAuthorParam, $categoryParam, $categoryID, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);

    $totalBooksQuery = "SELECT COUNT(*) AS count FROM book 
                        WHERE BookTitle LIKE ? 
                        AND Author LIKE ? 
                        AND (CategoryCode LIKE ? OR ? = '')";
    $stmt = $conn->prepare($totalBooksQuery);
    $stmt->bind_param("ssss", $searchTitleParam, $searchAuthorParam, $categoryParam, $categoryID);
    $stmt->execute();
    $totalBooksNum = $stmt->get_result();
    $totalBooks = $totalBooksNum->fetch_assoc()['count'];
    $totalPages = ceil($totalBooks / $limit); //rounds to integer with no regars to decimal part
}
?>

<main>
<center><div class="filteredSearch">

<img src="book2.png" width="100" height="100">
    <h1>Book search</h1>
    
    <!-- Book Search Form -->
    <form action="SearchBooksPage.php" method="GET">
        <label for="title">Book Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($searchTitle); ?>" /><br><br>
        <label for="author">Author:</label>&nbsp&nbsp
        <input type="text" name="author" value="<?php echo htmlspecialchars($searchAuthor); ?>"  /><br><br>

        <label for="category">Category:</label>&nbsp
        <select style="border-radius:15px;border-color:cadetblue;"name="category">
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['CategoryID']); ?>" 
                    <?php echo $category['CategoryID'] === $categoryID ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['CategoryDetails']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <table>
            <tr><td>
                <button class="searchbutton" type="submit">Search for a book</button></form>
            </td><td>

                <form action="SearchBooksPage.php" method="GET">
                <button class="searchbutton" type="submit" name="display_all" value="1">Display All Books</button>
                </form>         
            </td></tr>
        </table></div>
        <br>

    <!--results of book seacrh-->
    <?php if ($filtered || $displayAll): ?> <!--output depending on if user clicked to display all or based on filter-->
       
            <h3>Search Results</h3>
            <?php if ($books): ?>
                <table class="searchTable">

                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['BookTitle']); ?></td>
                                <td><?php echo htmlspecialchars($book['Author']); ?></td>
                                <td><?php echo htmlspecialchars($book['CategoryCode']); ?></td>
                                <td><?php echo $book['Reserved'] === 'Y' ? 'Reserved' : 'Available'; ?></td>
                                <td>
                                    <?php if ($book['Reserved'] === 'N'): ?>
                                        <form method="POST" action="ReservationPage.php">
                                            <input type="hidden" name="ISBN" value="<?php echo htmlspecialchars($book['ISBN']); ?>">
                                            <button type="submit">Reserve</button>
                                        </form>
                                    <?php else: ?>
                                        Not Available
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Pagination Links -->
                <div>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?<?php echo $displayAll ? 'display_all=1&' : ''; ?>title=<?php echo urlencode($searchTitle); ?>&author=<?php echo urlencode($searchAuthor); ?>&category=<?php echo urlencode($categoryID); ?>&instances=<?php echo $i; ?>"
                           <?php if ($i === $instances) echo 'style="font-weight: bold;"'; ?>><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php else: ?>
                <p>No books found matching your criteria.</p>
            <?php endif; ?>
                <?php endif; ?>
        </center><br>
</main>

<?php
include('footer.php'); // Include the footer
?>
