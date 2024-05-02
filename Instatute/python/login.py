import dbconnect

# Start session
import os
import cherrypy

# Check if form is submitted
if cherrypy.request.method == "POST":
    # Database connection
    conn = dbconnect.get_connection()

    # Check connection
    if not conn.is_connected():
        raise Exception("Connection failed: " + str(conn.errno))

    # Check if form data is set
    if "usn" in cherrypy.request.params and "psw" in cherrypy.request.params:
        # Retrieve form data
        username = conn.cursor().escape_string(cherrypy.request.params["usn"])
        password = conn.cursor().escape_string(cherrypy.request.params["psw"])

        # Query to check login credentials
        query = f"SELECT * FROM users WHERE username='{username}' AND password='{password}'"
        cursor = conn.cursor()
        cursor.execute(query)
        result = cursor.fetchall()

        # Check if user exists
        if len(result) == 1:
            # User found
            row = result[0]
            # Store user ID in session variable
            cherrypy.session["user_id"] = row[0]
            # Redirect based on user role
            if row[3] == 'student':
                raise cherrypy.HTTPRedirect("../htmls/student_index")
            elif row[3] == 'tutor':
                raise cherrypy.HTTPRedirect("../htmls/tutor_index")
            else:
                print("Invalid role")
        else:
            print("Invalid username/password")

    # Close database connection
    conn.close()

