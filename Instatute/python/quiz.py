import cherrypy
import dbconnect
from cherrypy import session

# Connect to the database (optional, depending on your application structure)
conn = dbconnect.get_connection()  # Uncomment if needed for session retrieval

# ... other CherryPy application code ...

@cherrypy.expose
def your_route(self):
    # Check if user is logged in
    if 'user_id' not in session:
        # Redirect to login page
        raise cherrypy.HTTPRedirect("/login")
        # Or return a login form response

    user_id = session['user_id']

    subject_id = int(cherrypy.request.params.get('subject', ''))  # Use default value of 0

    if not subject_id:
        print("No subject selected.")
        # Optionally, provide an error message or redirect
        return "Please select a subject."  # Or display an error page

    # Connect to the database (if not connected earlier)
    with dbconnect.get_connection() as conn:
        cursor = conn.cursor()

        # Fetch the latest quiz for the selected subject
        sql = "SELECT q.quiz_id, q.quiz_name FROM quiz q WHERE q.subject_id = %s ORDER BY q.quiz_id DESC LIMIT 1"
        cursor.execute(sql, (subject_id,))
        result = cursor.fetchone()

        if result:
            quiz_id, quiz_name = result
            session['quiz_id'] = quiz_id

            # Fetch questions and options for the quiz
            sql = "SELECT q.question_id, q.question_text, o.option_id, o.option_text FROM quiz_question q JOIN quiz_option o ON q.question_id = o.question_id WHERE q.quiz_id = %s"
            cursor.execute(sql, (quiz_id,))
            questions = {}
            for row in cursor:
                question_id, question_text, option_id, option_text = row
                if question_id not in questions:
                    questions[question_id] = {'text': question_text, 'options': []}
                questions[question_id]['options'].append({'id': option_id, 'text': option_text})
            #return render_template("quiz.html", questions=questions, quiz_name=quiz_name)  # Replace with your template rendering logic
        else:
            print("No quiz available for this subject.")
            return "No quiz available for this subject."  # Or display an informative message
