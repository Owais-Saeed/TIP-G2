<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" > 
  <meta name="description" content="TIP">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="" rel="stylesheet" type="text/css">
  <link href="" rel="icon" type="image/jpg">
  <title>Tutor Homepage</title> 
</head>
<body>
<!--Vertical menu bar
https://www.w3schools.com/css/css_navbar_vertical.asp: materials-->
<header>
    <p id="cp_name">Instatute</p>
    <ul class="navbar">    
        <li><a href="index.php">Create Quiz</a></li>
        <li><a href="leaderboard.html">Leaderboard</a></li>
        <li><a href="grade.html">View grades</a></li> <!--a list of students and grades, categorized by each subject tutors teach-->
    </ul>
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
        <a href="tutor_subjects.php">Create Quiz</a>
    </div>
</div>
<script src=""></script>

</body>
</html>