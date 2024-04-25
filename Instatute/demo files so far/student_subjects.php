<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="TIP">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Select Subject</title>
</head>
<body>
    <h1>Select Subject</h1>

    <form action="student_quiz.php" method="GET">
        <?php
        session_start(); // Start session
        include 'dbconnect.php'; // Include the database connection

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit(); // Stop further execution
        }

        // Retrieve user ID from session
        $userID = $_SESSION['user_id'];

        // Query to select student ID based on user ID
        $studentQuery = "SELECT student_id FROM students WHERE user_id = $userID";
        $studentResult = $conn->query($studentQuery);

        if ($studentResult->num_rows > 0) {
            $studentRow = $studentResult->fetch_assoc();
            $studentID = $studentRow['student_id'];

            // Query to select subjects the student is enrolled in
            $sql = "SELECT s.subject_id, s.subject_name
                    FROM subjects s
                    INNER JOIN enrollment e ON s.subject_id = e.subject_id
                    WHERE e.student_id = $studentID";

            $result = $conn->query($sql);

            // Check if there are any subjects available
            if ($result->num_rows > 0) {
                // Display subjects as options in a form
                echo "<label for='subject'>Choose a subject:</label><br>";
                echo "<select name='subject' id='subject'>";
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['subject_id']}'>{$row['subject_name']}</option>";
                }
                echo "</select><br><br>";
                echo "<input type='submit' value='Start Quiz'>";
            } else {
                echo "You are not enrolled in any subjects.";
            }
        } else {
            echo "Student not found.";
        }
        ?>

    </form>
</body>
</html>