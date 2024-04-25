<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <style>
        .quiz {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .question {
            margin-bottom: 15px;
        }
        .option {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1>Quiz</h1>
 
    <div class="quiz">
        <form name="quizForm" id="quizForm">
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
 
            // Check if subject ID is provided in the URL
            if (isset($_GET['subject'])) {
                $subjectID = $_GET['subject'];
 
                // Query to select the quiz with the highest ID for the selected subject
                $sql = "SELECT q.quiz_id, q.quiz_name
                FROM quiz q
                WHERE q.subject_id = $subjectID
                ORDER BY q.quiz_id DESC
                LIMIT 1";
 
                $result = $conn->query($sql);
 
                // Check if there is a quiz available for the subject
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $quizID = $row['quiz_id'];
                    $quizName = $row['quiz_name'];
 
                    // Query to select questions and their options for the selected quiz
                    $sql = "SELECT q.question_id, q.question_text, o.option_id, o.option_text, o.option_type
                    FROM quiz_question q
                    INNER JOIN quiz_option o ON q.question_id = o.question_id
                    WHERE q.quiz_id = $quizID";
 
                    $result = $conn->query($sql);
 
                    // Array to store questions and options
                    $questions = array();
 
                    // Loop through the results
                    while ($row = $result->fetch_assoc()) {
                        $questionID = $row['question_id'];
                        // Check if the question is already in the array
                        if (!isset($questions[$questionID])) {
                            // If not, create a new question entry
                            $questions[$questionID] = array(
                                'question_text' => $row['question_text'],
                                'options' => array(),
                                'correct_option' => 0 // Initialize correct option ID
                            );
                        }
                        // Add option to the question's options array
                        $questions[$questionID]['options'][] = array(
                            'option_id' => $row['option_id'],
                            'option_text' => $row['option_text'],
                            'option_type' => $row['option_type']
                        );
                        // If the option is correct, store its ID
                        if ($row['option_type'] == '1') {
                            $questions[$questionID]['correct_option'] = $row['option_id']; // Store the correct option ID
                        }
                    }
 
                    // Display quiz name
                    echo "<h2>$quizName</h2>";
 
                    // Display questions and options
                    foreach ($questions as $questionID => $question) {
                        echo "<div class='question'>";
                        echo "<p>{$question['question_text']}</p>";
                        // Display options as radio buttons
                        foreach ($question['options'] as $option) {
                            echo "<div class='option'>";
                            echo "<input type='radio' name='answer[{$questionID}]' value='{$option['option_id']}'> {$option['option_text']}<br>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "No quiz available for this subject.";
                }
            } else {
                echo "No subject selected.";
            }
            ?>
 
            <button type="button" id="submitBtn">Submit</button> <!-- Change type to 'button' -->
        </form>
    </div>
 
    <footer>
        <!-- Footer content -->
    </footer>
 
    <script>
document.getElementById('submitBtn').addEventListener('click', function() {
    var form = document.getElementById('quizForm');
    var formData = new FormData(form);
    var correctCount = 0;
    var correctOptions = <?php echo json_encode($questions); ?>;
 
    // Iterate through form data
    formData.forEach(function(value, key) {
        var questionID = key.split("[")[1].split("]")[0]; // Extract question ID
        var answerID = value; // Extract selected answer ID
 
        // Find the correct option ID for this question
        var correctOptionID = correctOptions[questionID]['correct_option'];
 
        // Check if the selected option ID matches the correct option ID
        if (answerID == correctOptionID) {
            correctCount++;
        }
    });
 
    // Display the count in an alert
    alert("You got " + correctCount + " out of " + <?php echo count($questions); ?> + " questions correct.");
});
</script>
 
</body>
</html>
