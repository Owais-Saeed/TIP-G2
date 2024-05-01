<?php
session_start();
include 'dbconnect.php'; // Ensure your database connection script is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['usn'];
    $password = $_POST['psw']; // Hash the password
    $role = $_POST['role'];

    // Check if username already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username already taken. Please choose a different one.";
        $stmt->close();
    } else {
        $stmt->close(); // Close the previous statement

        // Proceed with registration if username is available
        $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password before storing it
        $conn->begin_transaction();
        try {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $role);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();

        if ($role == 'student') {
            $animal_id = $_POST['animal_id'];
            $subjects = $_POST['subjects']; // This expects multiple selection of subjects

            // Insert into students table
			$stmt = $conn->prepare("INSERT INTO students (user_id, animal_id, points_earned) VALUES (?, ?, 0)");
			$stmt->bind_param("ii", $user_id, $animal_id);
			$stmt->execute();
			$student_id = $stmt->insert_id; // Correctly retrieving the student_id

			// Enroll student in selected subjects
			foreach ($subjects as $subject_id) {
				$stmt = $conn->prepare("INSERT INTO enrollment (student_id, subject_id) VALUES (?, ?)");
				$stmt->bind_param("ii", $student_id, $subject_id); // Use the correct student_id
				$stmt->execute();
			}
        } elseif ($role == 'tutor') {
            $subjects = $_POST['subjects'];

            // Insert into tutors table
			$stmt = $conn->prepare("INSERT INTO tutors (user_id, first_name, last_name) VALUES (?, ?, ?)");
			$stmt->bind_param("iss", $user_id, $first_name, $last_name); // Assuming you are collecting names in the form
			$stmt->execute();
			$tutor_id = $stmt->insert_id; // Retrieve the auto-generated tutor_id

			// Assign tutor to selected subjects
			foreach ($subjects as $subject_id) {
				$stmt = $conn->prepare("INSERT INTO teaching (tutor_id, subject_id) VALUES (?, ?)");
				$stmt->bind_param("ii", $tutor_id, $subject_id); // Use the correct tutor_id
				$stmt->execute();
			}
        }

        // Commit transaction
        $conn->commit();
        echo "Registration successful!";
    } catch (Exception $e) {
        $conn->rollback(); // Something went wrong, rollback
        echo "Error: " . $e->getMessage();
		}
	}
}
$conn->close();
?>