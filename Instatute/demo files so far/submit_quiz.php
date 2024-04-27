<?php
session_start(); // Start the session
include 'dbconnect.php'; // Include your database connection

if (!isset($_SESSION['user_id']) || !isset($_POST['answers'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];
$quizID = $_SESSION['quiz_id']; // Ensure this is set when the quiz starts
$answers = $_POST['answers']; // This assumes answers are posted as an associative array question_id => answer_id

// Retrieve the student_id for the logged-in user
$studentQuery = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$studentResult = $stmt->get_result();
if ($studentRow = $studentResult->fetch_assoc()) {
    $studentID = $studentRow['student_id'];
} else {
    echo "Student ID not found.";
    exit();
}

// Fetch correct answers from the database
$sql = "SELECT question_id, option_id FROM quiz_option WHERE question_id IN (" . implode(',', array_keys($answers)) . ") AND option_type = '1'";
$result = $conn->query($sql);

$correctAnswers = [];
while ($row = $result->fetch_assoc()) {
    $correctAnswers[$row['question_id']] = $row['option_id'];
}

// Calculate the score
$score = 0;
foreach ($answers as $question_id => $answer_id) {
    if (isset($correctAnswers[$question_id]) && $correctAnswers[$question_id] == $answer_id) {
        $score++;
    }
}

// Record the attempt in the database
$sql = "INSERT INTO attempts (quiz_id, student_id, score) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $quizID, $studentID, $score);
$stmt->execute();

// Check for success/failure
if ($stmt->affected_rows > 0) {
    echo "Quiz submitted successfully! Your score: $score";
    // Redirect or link back to another page if desired
} else {
    echo "Failed to record quiz attempt.";
}

$stmt->close();
$conn->close();
?>
