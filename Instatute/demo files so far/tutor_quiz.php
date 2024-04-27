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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
	include 'dbconnect.php';

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Escape user inputs for security
    function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Get the subject selected by the tutor from the URL query parameter
    if (isset($_GET['subject'])) {
        // Get the subject name from the URL
        $subject_name = cleanInput($_GET['subject']);

        // Query the database to retrieve the subject_id based on the subject name
        $query_subject = "SELECT subject_id FROM subjects WHERE subject_name = '$subject_name'";
        $result_subject = mysqli_query($conn, $query_subject);

        if ($result_subject && mysqli_num_rows($result_subject) > 0) {
            // Fetch the subject_id from the result
            $row = mysqli_fetch_assoc($result_subject);
            $subject_id = $row['subject_id'];

            // Now you have the subject_id, proceed with inserting into the Quiz table
            $query_quiz = "INSERT INTO Quiz (subject_id, tutor_id) VALUES ('$subject_id', '$user_id')";
            $result_quiz = mysqli_query($conn, $query_quiz);

            if ($result_quiz) {
                // Get the auto-generated quiz_id
                $quiz_id = mysqli_insert_id($conn);

                // Proceed with inserting questions and options
                for ($i = 1; $i <= 5; $i++) {
                    $question = cleanInput($_POST['question_' . $i]);
                    $optionA = cleanInput($_POST['option_' . $i . '_A']);
                    $optionB = cleanInput($_POST['option_' . $i . '_B']);
                    $optionC = cleanInput($_POST['option_' . $i . '_C']);
                    $optionD = cleanInput($_POST['option_' . $i . '_D']);
                    $correctOption = cleanInput($_POST['correct_option_' . $i]);

                    // Insert question into the database
                    $query_question = "INSERT INTO quiz_question (question_text, subject_id, quiz_id) VALUES ('$question', '$subject_id', '$quiz_id')";
                    $result_question = mysqli_query($conn, $query_question);

                    if ($result_question) {
                        // Get the auto-generated question_id
                        $question_id = mysqli_insert_id($conn);

                        // Insert options into the database
                        $query_options = "INSERT INTO quiz_option (option_text, question_id, option_type) VALUES ('$optionA', '$question_id', '" . ($correctOption == 'A' ? '1' : '0') . "'),
                                        ('$optionB', '$question_id', '" . ($correctOption == 'B' ? '1' : '0') . "'),
                                        ('$optionC', '$question_id', '" . ($correctOption == 'C' ? '1' : '0') . "'),
                                        ('$optionD', '$question_id', '" . ($correctOption == 'D' ? '1' : '0') . "')";
                        $result_options = mysqli_query($conn, $query_options);

                        if (!$result_options) {
                            $error_message = "Error inserting options for question $i.";
                            break;
                        }
                    } else {
                        $error_message = "Error inserting question $i.";
                        break;
                    }
                }
            } else {
                $error_message = "Error inserting into the Quiz table.";
            }
        } else {
            $error_message = "Invalid subject name.";
        }
    } else {
        $error_message = "Subject not specified.";
    }

    // Close database connection
    mysqli_close($conn);

    if (isset($error_message)) {
        echo "<script>alert('$error_message');</script>";
    } else {
        echo "<script>alert('Quiz submission successful.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" > 
    <meta name="description" content="TIP">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="" rel="stylesheet" type="text/css">
    <link href="" rel="icon" type="image/jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        div.question_options {
        line-height: 1.6;
        }
        </style>
    <title>Create A Quiz!</title> 
</head>
<body>
<!--Vertical menu bar
https://www.w3schools.com/css/css_navbar_vertical.asp: materials-->
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
                        <a class="nav-link active" aria-current="page" href="sindex.php">Create Quiz</a>
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
    <!-- <p id="cp_name">Instatute</p>
    <ul class="navbar">    
        <li><a href="index.html">Create Quiz</a></li>
        <li><a href="leaderboard.html">Leaderboard</a></li>
        <li><a href="grade.html">View grades</a></li> //a list of students and grades, categorized by each subject tutors teach
    </ul> -->
</header>
<!--Container-->
<div class="container">
    <!--Create quiz content
    https://github.com/Prajwal-P/Quizzey: materials-->
    <form method="post">
    <div id="quiz_container">
        <h1>Create Quiz</h1>
        <div class="question_options">
            <h2>Q 1</h2>
            <label for="question_1">Question 1: </label>
            <input id="question_1" class="question" type="text" name="question_1" required="required">
            <br>
            <!--Option A-->
            <label for="option_1_A">Option A: </label>
            <input id="option_1_A" class="txt_field" type="text" name="option_1_A" required="required">
            <input class="checkbox" type="radio" name="correct_option_1" value="A" required="required">
            <!--Option B-->
            <label for="option_1_B">Option B: </label>
            <input id="option_1_B" class="txt_field" type="text" name="option_1_B" required="required">
            <input class="checkbox" type="radio" name="correct_option_1" value="B" required="required">
            <!--Option C-->
            <label for="option_1_C">Option C: </label>
            <input id="option_1_C" class="txt_field" type="text" name="option_1_C" required="required">
            <input class="checkbox" type="radio" name="correct_option_1" value="C" required="required">
            <!--Option D-->
            <label for="option_1_D">Option D: </label>
            <input id="option_1_D" class="txt_field" type="text" name="option_1_D" required="required">
            <input class="checkbox" type="radio" name="correct_option_1" value="D" required="required">
        </div>
		<div class="question_options">
            <h2>Q 2</h2>
            <label for="question_2">Question 2: </label>
            <input id="question_2" class="question" type="text" name="question_2" required="required">
            <br>
            <!--Option A-->
            <label for="option_2_A">Option A: </label>
            <input id="option_2_A" class="txt_field" type="text" name="option_2_A" required="required">
            <input class="checkbox" type="radio" name="correct_option_2" value="A" required="required">
            <!--Option B-->
            <label for="option_2_B">Option B: </label>
            <input id="option_2_B" class="txt_field" type="text" name="option_2_B" required="required">
            <input class="checkbox" type="radio" name="correct_option_2" value="B" required="required">
            <!--Option C-->
            <label for="option_2_C">Option C: </label>
            <input id="option_2_C" class="txt_field" type="text" name="option_2_C" required="required">
            <input class="checkbox" type="radio" name="correct_option_2" value="C" required="required">
            <!--Option D-->
            <label for="option_2_D">Option D: </label>
            <input id="option_2_D" class="txt_field" type="text" name="option_2_D" required="required">
            <input class="checkbox" type="radio" name="correct_option_2" value="D" required="required">
        </div>
		<div class="question_options">
            <h2>Q 3</h2>
            <label for="question_3">Question 3: </label>
            <input id="question_3" class="question" type="text" name="question_3" required="required">
            <br>
            <!--Option A-->
            <label for="option_3_A">Option A: </label>
            <input id="option_3_A" class="txt_field" type="text" name="option_3_A" required="required">
            <input class="checkbox" type="radio" name="correct_option_3" value="A" required="required">
            <!--Option B-->
            <label for="option_3_B">Option B: </label>
            <input id="option_3_B" class="txt_field" type="text" name="option_3_B" required="required">
            <input class="checkbox" type="radio" name="correct_option_3" value="B" required="required">
            <!--Option C-->
            <label for="option_3_C">Option C: </label>
            <input id="option_3_C" class="txt_field" type="text" name="option_3_C" required="required">
            <input class="checkbox" type="radio" name="correct_option_3" value="C" required="required">
            <!--Option D-->
            <label for="option_3_D">Option D: </label>
            <input id="option_3_D" class="txt_field" type="text" name="option_3_D" required="required">
            <input class="checkbox" type="radio" name="correct_option_3" value="D" required="required">
        </div>
		<div class="question_options">
            <h2>Q 4</h2>
            <label for="question_4">Question 4: </label>
            <input id="question_4" class="question" type="text" name="question_4" required="required">
            <br>
            <!--Option A-->
            <label for="option_4_A">Option A: </label>
            <input id="option_4_A" class="txt_field" type="text" name="option_4_A" required="required">
            <input class="checkbox" type="radio" name="correct_option_4" value="A" required="required">
            <!--Option B-->
            <label for="option_4_B">Option B: </label>
            <input id="option_4_B" class="txt_field" type="text" name="option_4_B" required="required">
            <input class="checkbox" type="radio" name="correct_option_4" value="B" required="required">
            <!--Option C-->
            <label for="option_4_C">Option C: </label>
            <input id="option_4_C" class="txt_field" type="text" name="option_4_C" required="required">
            <input class="checkbox" type="radio" name="correct_option_4" value="C" required="required">
            <!--Option D-->
            <label for="option_4_D">Option D: </label>
            <input id="option_4_D" class="txt_field" type="text" name="option_4_D" required="required">
            <input class="checkbox" type="radio" name="correct_option_4" value="D" required="required">
        </div>
        <div class="question_options">
            <h2>Q 5</h2>
            <label for="question_5">Question 5: </label>
            <input id="question_5" class="question" type="text" name="question_5" required="required">
            <br>
            <!--Option A-->
            <label for="option_5_A">Option A: </label>
            <input id="option_5_A" class="txt_field" type="text" name="option_5_A" required="required">
            <input class="checkbox" type="radio" name="correct_option_5" value="A" required="required">
            <!--Option B-->
            <label for="option_5_B">Option B: </label>
            <input id="option_5_B" class="txt_field" type="text" name="option_5_B" required="required">
            <input class="checkbox" type="radio" name="correct_option_5" value="B" required="required">
            <!--Option C-->
            <label for="option_5_C">Option C: </label>
            <input id="option_5_C" class="txt_field" type="text" name="option_5_C" required="required">
            <input class="checkbox" type="radio" name="correct_option_5" value="C" required="required">
            <!--Option D-->
            <label for="option_5_D">Option D: </label>
            <input id="option_5_D" class="txt_field" type="text" name="option_5_D" required="required">
            <input class="checkbox" type="radio" name="correct_option_5" value="D" required="required">
        </div>
    </div>
    <!--Next question/Submit quiz-->
    <div class="create_quiz_button">
        <button type="submit">Submit</button>
    </div>
</form>
</body>
</html>