<?php 
session_start(); // Add session start at the beginning

$host = "localhost";
$username = "root";  
$password = "";      
$database = "Librarydb";


try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
   
    $loginUsername = $_POST['username'];
   
    $loginPassword =sha1($_POST['password']);
   
    

    $sql="SELECT Member_ID, username FROM Member WHERE username = :username AND password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        
        ':username' => $loginUsername,
       
        ':password' => $loginPassword

  ]);

  if($stmt->rowCount() > 0){
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_id'] = $user['Member_ID'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_type'] = 'member';
    header("Location: index.php"); // Redirect to home page
  //  $_SESSION['Member_ID']=$user['username'];
    exit();
  }else{
    $sql1="SELECT Work_ID, username FROM Worker WHERE username = :username AND password = :password";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([
        ':username' => $loginUsername,
        ':password' => $loginPassword
    ]);
    if($stmt1->rowCount() > 0)
    {
        $user = $stmt1->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['Work_ID'];
        $_SESSION['username'] =htmlspecialchars( $user['username']);
        $_SESSION['user_type'] = 'worker';
        header("Location: index.php"); // Redirect to home page
        exit();
    }
    else
    {
        $error_message = "Invalid username or password";
    }
  }

// Get the last inserted ID
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Alexandria's Haven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #E6D5C3;
        }
        .navbar {
            background-color: #8B7355 !important;
        }
        .card {
            background-color: #F4EBE2;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search.php">Search</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
            </ul>
        </div>
    </div>
</nav>



<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="text-center">Login</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                       
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                       
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                   
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 