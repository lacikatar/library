<?php

session_start();



?>
<!DOCTYPE html>
<html>
<head>
    <title>Laci's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #E6D5C3;
        }
        .navbar {
            background-color: #8B7355 !important;
        }
        .dropdown-menu {
            background-color: #F4EBE2;
        }
        .welcome-text {
            color: #5C4033;
            text-align: center;
            margin-top: 200px;
            font-family: 'Georgia', serif;
        }
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
                    echo '<li class="nav-item"><a class="nav-link">Welcome back, ' . htmlspecialchars( $_SESSION['username']) . '!'. '</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="welcome-text">
        <h1>Welcome to Laci's Library</h1>
        <p class="lead">Your gateway to endless literary adventures</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 