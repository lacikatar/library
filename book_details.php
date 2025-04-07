<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "Librarydb";
session_start();



try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$isbn = $_GET['isbn'] ?? '';

if ($isbn) {
    $sql = "SELECT DISTINCT 
                b.ISBN, 
                b.Title, 
                b.Description, 
                b.Publisher, 
                b.Release_Year, 
                b.Page_Nr, 
                bs.Name AS series_name,
                GROUP_CONCAT(DISTINCT a.Name SEPARATOR ', ') AS authors,
                b.Image_URL, 
                GROUP_CONCAT(DISTINCT c.Name SEPARATOR ', ') AS categories 
            FROM Book b
            INNER JOIN wrote w ON b.ISBN = w.ISBN
            INNER JOIN author a ON a.Author_ID = w.Author_ID
            LEFT JOIN belongs bl ON b.ISBN = bl.ISBN
            LEFT JOIN category c ON bl.Category_ID = c.Category_ID
            LEFT JOIN book_series bs ON b.Series_ID = bs.Series_ID
            WHERE b.ISBN = :isbn 
            GROUP BY b.ISBN";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(['isbn' => $isbn]);
    $bookDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    $copiesStmt = $conn->prepare("
        SELECT c.Copy_ID,c.Copy_Condition, c.Shelf_Position 
        FROM copy c
        LEFT JOIN borrowing b ON c.Copy_ID = b.Copy_ID AND b.Status = 'Checked Out'
        WHERE c.ISBN = ?
        AND (b.Copy_ID IS NULL)
        LIMIT 5
    ");
    $copiesStmt->execute([$isbn]);
    $availableCopies = $copiesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare and execute query to check if member is on the waitlist
    $waitlistStmt = $conn->prepare("
        SELECT * FROM waitlist 
        WHERE Member_ID = ? AND ISBN = ?
    ");
    $waitlistStmt->execute([$_SESSION['user_id'], $isbn]);
    $onWaitlist = $waitlistStmt->rowCount() > 0;

    // Prepare and execute query to get member's name
    $memberStmt = $conn->prepare("SELECT Name FROM member WHERE Member_ID = ?");
    $memberStmt->execute([$_SESSION['user_id']]);
    $memberName = $memberStmt->fetchColumn();


}
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #E6D5C3; }
        .book-cover {
            height: 400px;
            width: 100%;
            object-fit: contain; /* Ensures the entire image is shown without cropping */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .detail-list dt {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-list dd {
            margin-bottom: 1rem;
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
<div class="container mt-4">
    <?php if ($bookDetails): ?>
        <div class="row">
            <!-- Right Column - Cover Image -->
            <div class="col-md-4 mb-4">
                <img src="<?= htmlspecialchars($bookDetails['Image_URL'] ?: 'default_cover.jpg') ?>" 
                     alt="<?= htmlspecialchars($bookDetails['Title']) ?>" 
                     class="book-cover rounded">
            </div>

            <!-- Left Column - Book Details -->
            <div class="col-md-8">
                <h1 class="mb-3"><?= htmlspecialchars($bookDetails['Title']) ?></h1>
                
                <?php if (!empty($bookDetails['series_name'])): ?>
                    <h4 class="text-muted mb-3"><?= htmlspecialchars($bookDetails['series_name']) ?> </h4>
                <?php endif; ?>

                <h3 class="mb-4">
                    By <?= htmlspecialchars($bookDetails['authors']) ?>
                </h3>

                <?php if (!empty($bookDetails['Description'])): ?>
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-body">
                            <p class="card-text"><?= htmlspecialchars($bookDetails['Description']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <dl class="row detail-list">
                    <?php if (!empty($bookDetails['categories'])): ?>
                        <dt class="col-sm-3">Categories</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($bookDetails['categories']) ?></dd>
                    <?php endif; ?>

                    <dt class="col-sm-3">Publisher</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($bookDetails['Publisher']) ?></dd>

                    <dt class="col-sm-3">Publication Year</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($bookDetails['Release_Year']) ?></dd>

                    <dt class="col-sm-3">Pages</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars(number_format($bookDetails['Page_Nr'])) ?></dd>

                    <dt class="col-sm-3">ISBN</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($bookDetails['ISBN']) ?></dd>
                </dl>
            </div>

            <h5>Availability</h5>
             <div class="card mb-4 border-0 bg-light">
                        <div class="card-body">
                        <?php if (count($availableCopies) > 0): ?>
    <table class="table table-bordered mt-3">
        <thead class="table-light">
            <tr>
                <th>Copy ID</th>
                <th>Condition</th>
                <th>Shelf Position</th>
                <th>Availability</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($availableCopies as $copy): ?>
                <tr>
                    <td><?= htmlspecialchars($copy['Copy_ID']) ?></td>
                    <td><?= htmlspecialchars($copy['Copy_Condition']) ?></td>
                    <td><?= htmlspecialchars($copy['Shelf_Position']) ?></td>
                    <td>Available</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-muted mt-3">No available copies found.</p>
<?php endif; ?>
                        </div>
                    </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">Book not found!</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn = null;
?>
