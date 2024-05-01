<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" > 
    <meta name="description" content="TIP">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="" rel="stylesheet" type="text/css">
    <link href="" rel="icon" type="image/jpg">

    <script type="module" src="https://pyscript.net/releases/2024.1.1/core.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>Login Page</title> 
</head>
<body>
<header>
    <p id="cp_name">Instatute</p>
</header>

<?php
session_start(); // Start session

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
	include 'dbconnect.php';

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if form data is set
    if (isset($_POST['usn']) && isset($_POST['psw'])) {
        // Retrieve form data
        $username = mysqli_real_escape_string($conn, $_POST['usn']);
        $password = mysqli_real_escape_string($conn, $_POST['psw']);

        // Query to check login credentials
        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result = mysqli_query($conn, $query);

        // Check if user exists
        if (mysqli_num_rows($result) == 1) {
            // User found
            $row = mysqli_fetch_assoc($result);
            // Store user ID in session variable
            $_SESSION['user_id'] = $row['user_id'];
            // Redirect based on user role
            if ($row['role'] == 'student') {
                header('Location: student_index.php');
                exit(); // Stop further execution
            } elseif ($row['role'] == 'tutor') {
                header('Location: tutor_index.php');
                exit(); // Stop further execution
            } else {
                echo "Invalid role";
            }
        } else {
            echo "Invalid username/password";
        }
    }

    // Close database connection
    mysqli_close($conn);
}
?>

    <form id="loginframe" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <div class="logininfo">
            <label for="pid"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" id="usn" name="usn" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" id="psw" name="psw" required>

            <button type="submit">Login</button>
            <label>
                <input type="checkbox" name="remember"> Remember me
            </label>
        </div>

    </form>
<!--Container-->
<script src=""></script>

</body>
</html>