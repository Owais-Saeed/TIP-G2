<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="TIP">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="" rel="stylesheet" type="text/css">
    <link href="" rel="icon" type="image/jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Select A Subject</title>
    <script>
    function redirectToQuiz(subject) 
    {
        window.location.href = 'tutor_quiz.php?subject=' + encodeURIComponent(subject);
    }
    </script>
</head>
<body>
<header>
    <nav class="p-3 text-bg-dark">
        <div class="container-fluid">
            <h1 id="cp_name">Instatute</h1>
        </div>
    </nav>
</header>

<?php
session_start(); // Start session

// Check if user ID is set in session
if (isset($_SESSION['user_id'])) {
    // Use $_SESSION['user_id'] to access the user's ID
    $user_id = $_SESSION['user_id'];
    // Now you can use $user_id to fetch subjects assigned to the tutor
    // Database connection
	include 'dbconnect.php';

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fetch subjects assigned to the tutor
    $query = "SELECT DISTINCT Subjects.subject_name
              FROM Subjects
              INNER JOIN Teaching ON Subjects.subject_id = Teaching.subject_id
              INNER JOIN Tutors ON Teaching.tutor_id = Tutors.tutor_id
              WHERE Tutors.user_id = $user_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Display subjects as buttons
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<button class="subject_button" onclick="redirectToQuiz(\'' . $row['subject_name'] . '\')">' . $row['subject_name'] . '</button>';
        }
    } else {
        echo "No subjects assigned.";
    }

    // Close database connection
    mysqli_close($conn);
} else {
    // Redirect user to login page if user ID is not set in session
    header('Location: login.php');
    exit();
}
?>

<!--Container-->
<div class="container">
    <!--Choose subject-->
    <div id="subjects">
        <!-- Subject buttons will be displayed here -->
    </div>
</div>
<script src=""></script>

</body>
</html>