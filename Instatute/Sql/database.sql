CREATE TABLE animals (
    animal_id INT AUTO_INCREMENT PRIMARY KEY,
    animal_name VARCHAR(255),
    required_points_to_level_up INT,
    current_level INT
);


CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    password VARCHAR(255),
    role VARCHAR(50),
    profile_picture VARCHAR(255)
);
 

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    animal_id INT,
    points_earned INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id)
);
 

CREATE TABLE tutors (
    tutor_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
 

CREATE TABLE subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(255),
    tutor_id INT,
    FOREIGN KEY (tutor_id) REFERENCES tutors(tutor_id)
);
 

CREATE TABLE enrollment (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject_id INT,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);
 

CREATE TABLE teaching (
    teaching_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT,
    subject_id INT,
    FOREIGN KEY (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);
 

CREATE TABLE quiz (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_name VARCHAR(255),
    subject_id INT,
    tutor_id INT,
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (tutor_id) REFERENCES tutors(tutor_id)
);
 

CREATE TABLE attempts (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    student_id INT,
    score INT,
    FOREIGN KEY (quiz_id) REFERENCES quiz(quiz_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);
 

CREATE TABLE quiz_question (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT,
    subject_id INT,
    quiz_id INT,
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (quiz_id) REFERENCES quiz(quiz_id)
);
 

CREATE TABLE quiz_option (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    option_text TEXT,
    question_id INT,
    option_type VARCHAR(5),
    FOREIGN KEY (question_id) REFERENCES quiz_question(question_id)
);
