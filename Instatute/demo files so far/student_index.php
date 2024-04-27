<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="TIP">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Student Homepage</title>
</head>
<body>
    <header>
        <nav class="p-3 text-bg-dark">
            <div class="container-fluid">
                
                <h1 id="cp_name">Instatute</h1>
                
            </div>
        </nav>

        <nav class="navbar navbar-expand-lg" aria-label="Tenth navbar example">
        <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample08" aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample08">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="student_subjects.php">Quiz</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Leaderboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">View grades</a>
                </li>
            </ul>
        </div>
        </div>
    </nav>
<!-- 
        
    <p id="cp_name">Instatute</p>
    <ul class="navbar">
        <li><a href="student_subjects.php">Do Quiz</a></li>
        <li><a href=".html">Leaderboard</a></li>
        <li><a href=".html">View grades</a></li>
    </ul> -->
</header>

<?php
session_start(); // Start session

// Check if user ID is set in session
if (isset($_SESSION['user_id'])) {
    // Use $_SESSION['user_id'] to access the user's ID
    $user_id = $_SESSION['user_id'];
    // Now you can use $user_id as needed
} else {
    // Redirect user to login page if user ID is not set in session
    header('Location: login.php');
    exit();
}
?>

<!--Container-->
<div class="container">
    <!--Welcome page-->
    <div class="welcome">
        <h1>Welcome!</h1>
        <img src="" alt="logo">
        <a href="student_subjects.php">Do Quiz</a> <!-- Changed this line -->
    </div>
</div>
<script src=""></script>

</body>
</html>