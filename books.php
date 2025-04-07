<?php
session_start();
// Database connection setup
$host = "localhost";
$username = "root";
$password = "";
$database = "Librarydb";

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch all books with their titles, authors, image URLs, and categories
$sql1 = "SELECT DISTINCT b.ISBN, 
       b.Title, 
       GROUP_CONCAT(DISTINCT a.Name SEPARATOR ', ') as authors, 
       b.Image_URL, 
       GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as categories 
FROM Book b
INNER JOIN wrote w ON b.isbn = w.isbn
INNER JOIN author a ON a.author_id = w.author_id
LEFT JOIN belongs bl ON b.isbn = bl.isbn
LEFT JOIN category c ON bl.category_id = c.category_id
GROUP BY b.isbn, b.Title, b.Image_URL 
ORDER BY b.Title ASC";
$stmt = $conn->query($sql1);
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #E6D5C3; }
        .card { background-color: #F4EBE2; position: relative; overflow: hidden; }
        .card img { transition: transform 0.3s ease; }
        .card:hover img { transform: scale(1.05); }
        .card-title-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .card:hover .card-title-overlay {
            opacity: 1;
        }
        .navbar { background-color: #8B7355 !important; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Laci's Library</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Catalogue
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="books.php">Books</a></li>
                        <li><a class="dropdown-item" href="authors.php">Authors</a></li>
                        <li><a class="dropdown-item" href="categories.php">Categories</a></li>
                        <li><a class="dropdown-item" href="book_series.php">Book Series</a></li>
                        
                    </ul>
                </li>
                <?php
             
                if (isset($_SESSION['user_id'])) {
                    // Show these items only if user is logged in
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">';
                    echo 'My Library';
                    echo '</a>';
                    echo '<ul class="dropdown-menu">';
                    echo '<li><a class="dropdown-item" href="reading-lists.php">Reading Lists</a></li>';
                    echo '<li><a class="dropdown-item" href="borrowed.php">Borrowed Books</a></li>';
                    echo '<li><a class="dropdown-item" href="read.php">Read Books</a></li>';
                    echo '<li><a class="dropdown-item" href="recommendations.php">Recommendations</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                ?>
            </ul>
            <ul class="navbar-nav">
                <?php
                if (!isset($_SESSION['user_id'])) {
                    // Show login/register only if user is not logged in
                    echo '<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                } else {
                    echo '<li class="nav-item"><a class="nav-link">Welcome, ' . htmlspecialchars( $_SESSION['username']) . '</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <?php
    if ($stmt->rowCount() > 0) {
        echo '<div class="row row-cols-1 row-cols-md-5 g-4">'; // Start row with 5 columns on medium+ screens
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="col">';
            echo '<div class="card h-100">';
            
            // Image section with hover effect and click functionality
            if ($row['Image_URL']) {
                echo '<a href="book_details.php?isbn=' . urlencode($row['ISBN']) . '">';
                echo '<img src="' . $row['Image_URL'] . '" class="card-img-top" alt="' . htmlspecialchars($row['Title']) . '" style="height: 300px; object-fit: contain;">';
                echo '</a>';
            } else {
                echo '<a href="book_details.php?isbn=' . urlencode($row['ISBN']) . '">';
                echo '<img src="default_cover.jpg" class="card-img-top" alt="' . htmlspecialchars($row['Title']) . '" style="height: 300px; object-fit: contain;">';
                echo '</a>';
            }

            // Overlay with title and authors on hover
            echo '<div class="card-title-overlay">';
            echo '<h5>' . htmlspecialchars($row['Title']) . '</h5>';
            echo '<p>By ' . htmlspecialchars($row['authors']) . '</p>';
            echo '</div>';
            
            echo '</div></div>';
        }
        echo '</div>'; // End row
    } else {
        echo '<div class="col-12"><div class="alert alert-info">No books found!</div></div>';
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn = null;
?>
