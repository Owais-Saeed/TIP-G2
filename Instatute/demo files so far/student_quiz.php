<?php
session_start(); // Start the session
include 'dbconnect.php'; // Include your database connection file

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];
$subjectID = $_GET['subject'] ?? ''; // Use the null coalescing operator to handle the absence of 'subject'

if (empty($subjectID)) {
    echo "No subject selected.";
    exit();
}

// Fetch the latest quiz for the selected subject
$sql = "SELECT q.quiz_id, q.quiz_name FROM quiz q WHERE q.subject_id = ? ORDER BY q.quiz_id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $subjectID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc();
    $quizID = $quiz['quiz_id'];
	$_SESSION['quiz_id'] = $quizID;
    $quizName = $quiz['quiz_name'];

    // Fetch questions and options for the quiz
    $sql = "SELECT q.question_id, q.question_text, o.option_id, o.option_text FROM quiz_question q JOIN quiz_option o ON q.question_id = o.question_id WHERE q.quiz_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quizID);
    $stmt->execute();
    $questionsResult = $stmt->get_result();

    $questions = [];
    while ($row = $questionsResult->fetch_assoc()) {
        $questions[$row['question_id']]['text'] = $row['question_text'];
        $questions[$row['question_id']]['options'][] = $row;
    }
} else {
    echo "No quiz available for this subject.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz: <?= htmlspecialchars($quizName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($quizName) ?></h1>
        <form method="POST" action="submit_quiz.php">
            <?php foreach ($questions as $questionID => $question): ?>
            <div class="mb-3">
                <label class="form-label"><?= htmlspecialchars($question['text']) ?></label>
                <?php foreach ($question['options'] as $option): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answers[<?= $questionID ?>]" id="option_<?= $option['option_id'] ?>" value="<?= $option['option_id'] ?>" required>
                    <label class="form-check-label" for="option_<?= $option['option_id'] ?>">
                        <?= htmlspecialchars($option['option_text']) ?>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Submit Quiz</button>
        </form>
    </div>
</body>
</html>
