<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>

</head>

<body>
    <h1>Leaderboard of Scores</h1>

    <table>
        <tr>
            <th>Rank</th>
            <th>Username</th>
            <th>Score</th>
        </tr>

    <?php
    // Include database connection
    include 'dbconnect.php';

    // Check if user ID is set in the URL
    if (isset($_GET['user_id'])) {
       
        // Get the user ID from the URL
        $userID = $_GET['user_id'];
 
        // Query to get the student ID associated with the user ID
        $studentIDQuery = "SELECT student_id
                           FROM students 
                           WHERE user_id = $userID";
        $studentIDResult = $conn->query($studentIDQuery);
 
        if ($studentIDResult->num_rows > 0) {
            $studentIDRow = $studentIDResult->fetch_assoc();
            $studentID = $studentIDRow['student_id'];
        }

        // Build query to fetch rows in descending order of student score
        $orderQuery = "SELECT student_id,
                       score FROM attempts,
                       ORDER BY score DESC";

        // Execute the query
        $result = $conn->query($orderQuery);

        // Ranking order
        $ranking = 1;

        // Fetch rows from the database
        if ($result->num_rows > 0) {
            echo "<h2>Leaderboard</h2>";
            echo "<table>";
            echo "<thead><tr><th>Rank</th><th>userID</th><th>Score</th></tr></thead>";
            echo "<tbody>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$ranking}</td>";
            echo "<td>{$row['user_id']}</td>";
            echo "<td>{$row['score']}</td>";
            echo "</tr>";
            $ranking++;
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "No leaderboard data available.";
    }

    // Close the database connection
    $conn->close();
    }

?>
</body>


