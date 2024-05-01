<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Grades</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>View Grades</h1>

    <?php
    // Include database connection
    include 'dbconnect.php';

    // Check if user ID is set in the session
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // Retrieve user ID from session
    $userID = $_SESSION['user_id'];

    // Build the query to select subjects taught by the tutor
    $subjectQuery = "SELECT DISTINCT s.subject_id, s.subject_name
                    FROM subjects s
                    INNER JOIN teaching t ON s.subject_id = t.subject_id
                    WHERE t.tutor_id = $userID";

    // Execute the query
    $subjectResult = $conn->query($subjectQuery);

    // Check if there are any results
    if ($subjectResult->num_rows > 0) {
        // Display subjects in the dropdown
        echo "<form action='' method='get'>";
        echo "<label for='subject_id'>Filter by Subject:</label>";
        echo "<select name='subject_id' id='subject_id'>";
        echo "<option value=''>All Subjects</option>";
        while ($row = $subjectResult->fetch_assoc()) {
            $subjectID = $row['subject_id'];
            $subjectName = $row['subject_name'];
            $selected = isset($_GET['subject_id']) && $_GET['subject_id'] == $subjectID ? 'selected' : '';
            echo "<option value='$subjectID' $selected>$subjectName</option>";
        }
        echo "</select>";
        echo "<input type='submit' value='Filter'>";
        echo "</form>";

        // Check if subject filter is applied
        $filterSubject = isset($_GET['subject_id']) ? $_GET['subject_id'] : '';

        // Build the query
        $sql = "SELECT a.student_id, a.quiz_id, q.quiz_name, s.subject_name, a.score
                FROM attempts a
                INNER JOIN quiz q ON a.quiz_id = q.quiz_id
                INNER JOIN subjects s ON q.subject_id = s.subject_id
                INNER JOIN teaching t ON s.subject_id = t.subject_id
                WHERE t.tutor_id = $userID";

        if (!empty($filterSubject)) {
            $sql .= " AND s.subject_id = $filterSubject";
        }

        // Execute the query
        $result = $conn->query($sql);

        // Check if there are any results
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>Student ID</th><th>Quiz Name</th><th>Subject</th><th>Score</th></tr></thead>";
            echo "<tbody>";

            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['student_id']}</td>";
                echo "<td>{$row['quiz_name']}</td>";
                echo "<td>{$row['subject_name']}</td>";
                echo "<td>{$row['score']}</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "No quiz attempts found.";
        }
    } else {
        echo "You are not assigned to teach any subjects.";
    }

    // Close the database connection
    $conn->close();
    ?>
</body>
</html>