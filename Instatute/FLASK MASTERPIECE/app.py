from flask import Flask, request, render_template, redirect, url_for, flash, session
import mysql.connector
from mysql.connector import Error
import os

app = Flask(__name__)
app.secret_key = os.urandom(24)  # Generate a random secret key

def get_db_connection():
    db_config = {
        'user': 'root',
        'password': '',
        'host': '127.0.0.1',
        'database': 'tip',
        'raise_on_warnings': True
    }
    try:
        connection = mysql.connector.connect(**db_config)
        return connection
    except Error as e:
        print(f"Error connecting to MySQL Platform: {e}")
        return None

#LOGIN BLOCK

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        # Use the existing get_db_connection function
        connection = get_db_connection()
        if connection is None:
            flash('Database connection could not be established.')
            return render_template('login.html')

        cursor = connection.cursor()
 
        # Retrieve form data
        username = request.form['usn']
        password_attempt = request.form['psw']
 
        # Query to check login credentials
        query = "SELECT user_id, password, role FROM users WHERE username=%s"
        cursor.execute(query, (username,))
        user_data = cursor.fetchone()
 
        # Check if user exists and password matches
        if user_data and user_data[1] == password_attempt:  # Assuming password is at index 1
            # Store user ID and role in session
            session['user_id'] = user_data[0]
            session['role'] = user_data[2]
            
            # Redirect based on user role
            if session['role'] == 'student':
                return redirect(url_for('student_index'))
            elif session['role'] == 'tutor':
                return redirect(url_for('tutor_index'))
            elif session['role'] == 'admin':
                return redirect(url_for('register'))
            else:
                flash("Invalid role")
                return render_template('login.html')
        else:
            flash('Invalid username/password')
            return render_template('login.html')
 
        # Ensure the cursor and connection are closed
        cursor.close()
        connection.close()
    else:
        # If request method is GET or form submission failed, render the login page
        return render_template('login.html')


# BLOCK FOR REGISTRATION

@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form['usn']
        password = request.form['psw']  # Not hashing the password
        role = request.form['role']
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute("SELECT user_id FROM users WHERE username = %s", (username,))
        if cursor.fetchone():
            flash('Username already taken. Please choose a different one.')
            return redirect(url_for('register'))

        cursor.execute("INSERT INTO users (username, password, role) VALUES (%s, %s, %s)",
                       (username, password, role))
        user_id = cursor.lastrowid

        if role == 'student':
            animal_id = request.form['animal_id']
            subjects = request.form.getlist('subjects[]')
            cursor.execute("INSERT INTO students (user_id, animal_id, points_earned) VALUES (%s, %s, 0)",
                           (user_id, animal_id))
            student_id = cursor.lastrowid
            for subject_id in subjects:
                cursor.execute("INSERT INTO enrollment (student_id, subject_id) VALUES (%s, %s)",
                               (student_id, subject_id))

        elif role == 'tutor':
            first_name = request.form.get('first_name', '')
            last_name = request.form.get('last_name', '')
            subjects = request.form.getlist('subjects[]')
            cursor.execute("INSERT INTO tutors (user_id, first_name, last_name) VALUES (%s, %s, %s)",
                           (user_id, first_name, last_name))
            tutor_id = cursor.lastrowid
            for subject_id in subjects:
                cursor.execute("INSERT INTO teaching (tutor_id, subject_id) VALUES (%s, %s)",
                               (tutor_id, subject_id))

        conn.commit()
        cursor.close()
        conn.close()
        flash('Registration successful!')
        return redirect(url_for('login'))

    return render_template('registration.html')

# Route for the student index page
@app.route('/student_index')
def student_index():
    # Check if user is logged in
    if 'user_id' in session:
        return render_template('student_index.html')
    else:
        return redirect(url_for('login'))  # Redirect to login page if not logged in
 
# Route for the tutor index page
@app.route('/tutor_index')
def tutor_index():
    # Check if user is logged in
    if 'user_id' in session:
        return render_template('tutor_index.html')
    else:
        return redirect(url_for('login'))  # Redirect to login page if not logged in
 
#tutor subjects block
@app.route('/tutor_subjects')
def tutor_subjects():
    if 'user_id' not in session:
        flash('User is not logged in.')
        return redirect(url_for('login'))

    user_id = session['user_id']
    conn = get_db_connection()
    if conn is None:
        flash("Database connection could not be established.")
        return render_template('tutor_subjects.html', subjects=[])

    cursor = conn.cursor()
    try:
        query = """
        SELECT DISTINCT Subjects.subject_name
        FROM Subjects
        JOIN Teaching ON Subjects.subject_id = Teaching.subject_id
        JOIN Tutors ON Teaching.tutor_id = Tutors.tutor_id
        WHERE Tutors.user_id = %s
        """
        cursor.execute(query, (user_id,))
        subjects = cursor.fetchall()

        if not subjects:
            flash('No subjects assigned.')
            return render_template('tutor_subjects.html', subjects=[])

        return render_template('tutor_subjects.html', subjects=subjects)
    except Exception as e:
        flash('Failed to fetch subjects: {}'.format(str(e)))
        return render_template('tutor_subjects.html', subjects=[])
    finally:
        cursor.close()
        conn.close()

#tutor quiz block

@app.route('/tutor_quiz', methods=['GET', 'POST'])
def tutor_quiz():
    if 'user_id' not in session:
        flash('Please log in to access this page.')
        return redirect(url_for('login'))
    
    user_id = session['user_id']
    subject = request.args.get('subject')
    
    if request.method == 'POST':
        if not subject:
            flash('Subject not specified.')
            return redirect(url_for('tutor_subjects'))
        
        conn = get_db_connection()
        cursor = conn.cursor()

        try:
            cursor.execute("SELECT subject_id FROM subjects WHERE subject_name = %s", (subject,))
            subject_id_result = cursor.fetchone()
            if not subject_id_result:
                flash("Invalid subject name.")
                return redirect(url_for('tutor_subjects'))

            subject_id = subject_id_result[0]
            cursor.execute("INSERT INTO Quiz (subject_id, tutor_id) VALUES (%s, %s)", (subject_id, user_id))
            quiz_id = cursor.lastrowid

            success = True
            for i in range(1, 6):  # Assuming 5 questions
                question_text = request.form.get(f'question_{i}')
                if not question_text:
                    continue

                cursor.execute("INSERT INTO quiz_question (question_text, quiz_id) VALUES (%s, %s)", (question_text, quiz_id))
                question_id = cursor.lastrowid

                for opt in ['A', 'B', 'C', 'D']:
                    option_text = request.form.get(f'option_{i}_{opt}')
                    if option_text:
                        is_correct = '1' if request.form.get(f'correct_option_{i}') == opt else '0'
                        cursor.execute("INSERT INTO quiz_option (option_text, question_id, option_type) VALUES (%s, %s, %s)", (option_text, question_id, is_correct))
                    else:
                        success = False
                        break

                if not success:
                    flash(f'Error inserting options for question {i}.')
                    break

            if success:
                conn.commit()
                flash('Quiz submission successful.')
            else:
                conn.rollback()

        except mysql.connector.Error as err:
            conn.rollback()
            flash(f'Error processing your request: {str(err)}', 'error')
        finally:
            cursor.close()
            conn.close()

        return redirect(url_for('tutor_quiz', subject=subject))
    
    return render_template('tutor_quiz.html', subject=subject)

#Student SUBJECT FUNCTION block

# Function to retrieve subjects for a student
def get_subjects(user_id):
    connection = mysql.connector.connect(
        host= "localhost",
        user= "root",
        password= "",
        database= "tip"
    )
    cursor = connection.cursor()
 
    # Query to get student ID based on user ID
    cursor.execute("SELECT student_id FROM students WHERE user_id = %s", (user_id,))
    student_data = cursor.fetchone()
 
    if student_data:
        student_id = student_data[0]
 
        # Query to get enrolled subjects for the student
        cursor.execute("""
            SELECT s.subject_id, s.subject_name
            FROM subjects s
            INNER JOIN enrollment e ON s.subject_id = e.subject_id
            WHERE e.student_id = %s
        """, (student_id,))
        subjects = cursor.fetchall()
 
        cursor.close()
        connection.close()
 
        return subjects
    else:
        return None


# Route for the student subjects page BLOCK

@app.route('/student_subjects')
def student_subjects():
    # Check if user is logged in
    if 'user_id' in session:
        user_id = session['user_id']
        subjects = get_subjects(user_id)
        if subjects:
            return render_template('student_subjects.html', subjects=subjects, get_subjects=get_subjects)
        else:
            return "You are not enrolled in any subjects."
    else:
        return redirect(url_for('login'))  # Redirect to login page if not logged in


# Route for starting the quiz
@app.route('/start_quiz', methods=['POST'])
def start_quiz():
    if 'user_id' not in session:
        flash('Please log in to access this page.')
        return redirect(url_for('login'))

    subject_id = request.form.get('subject')
    if not subject_id:
        flash('No subject selected.')
        return redirect(url_for('student_subjects'))

    return redirect(url_for('student_quiz', subject=subject_id))


# DISPLAY QUIZ BLOCK
@app.route('/student_quiz', methods=['GET'])
def student_quiz():
    if 'user_id' not in session:
        return redirect(url_for('login'))

    subject_id = request.args.get('subject', None)
    if not subject_id:
        flash("No subject selected.")
        return redirect(url_for('student_index'))

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    # Fetch the latest quiz for the selected subject
    cursor.execute("""
        SELECT quiz_id, quiz_name FROM quiz
        WHERE subject_id = %s
        ORDER BY quiz_id DESC LIMIT 1
    """, (subject_id,))
    quiz = cursor.fetchone()

    if not quiz:
        flash("No quiz available for this subject.")
        return redirect(url_for('student_subjects'))

    session['quiz_id'] = quiz['quiz_id']  # Store quiz_id in session for later use

    # Fetch questions and options for the quiz
    cursor.execute("""
        SELECT q.question_id, q.question_text, o.option_id, o.option_text
        FROM quiz_question q
        JOIN quiz_option o ON q.question_id = o.question_id
        WHERE q.quiz_id = %s
    """, (quiz['quiz_id'],))
    questions = {}
    for row in cursor.fetchall():
        question_id = row['question_id']
        if question_id not in questions:
            questions[question_id] = {'text': row['question_text'], 'options': []}
        questions[question_id]['options'].append({'option_id': row['option_id'], 'option_text': row['option_text']})

    cursor.close()
    conn.close()

    return render_template('student_quiz.html', quiz_name=quiz['quiz_name'], questions=questions)

#SUBMIT QUIZ BLOCK
@app.route('/submit_quiz', methods=['POST'])
def submit_quiz():
    if 'user_id' not in session or 'quiz_id' not in session:
        return redirect(url_for('login'))

    quiz_id = session['quiz_id']
    user_id = session['user_id']
    answers = {key.split('[')[-1].split(']')[0]: value for key, value in request.form.items()}  # Processing to extract question IDs correctly

    conn = get_db_connection()
    cursor = conn.cursor()

    # Fetch the student ID associated with the user
    cursor.execute("SELECT student_id FROM students WHERE user_id = %s", (user_id,))
    student_data = cursor.fetchone()
    if student_data is None:
        flash("Student ID not found.")
        return redirect(url_for('student_index'))

    student_id = student_data[0]

    # Prepare to fetch the correct answers
    format_strings = ','.join(['%s'] * len(answers))
    cursor.execute(f"SELECT question_id, option_id FROM quiz_option WHERE question_id IN ({format_strings}) AND option_type = '1'", tuple(answers.keys()))

    correct_answers = {str(row[0]): str(row[1]) for row in cursor.fetchall()}

    # Calculate the score
    score = sum(1 for question_id, selected_option_id in answers.items() if correct_answers.get(str(question_id), None) == selected_option_id)

    # Record the attempt
    try:
        cursor.execute("INSERT INTO attempts (quiz_id, student_id, score) VALUES (%s, %s, %s)", (quiz_id, student_id, score))
        conn.commit()
        flash(f"Quiz submitted successfully! Your score: {score}")
    except Exception as e:
        conn.rollback()
        flash("Failed to record quiz attempt. Error: " + str(e))
    finally:
        cursor.close()
        conn.close()

    return redirect(url_for('student_index'))



if __name__ == '__main__':
    app.run(debug=True, port=5000)