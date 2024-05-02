import cherrypy
import json
from dbconnect import get_user  # Replace with your database connection logic

class LoginService(object):
    @cherrypy.expose
    @cherrypy.body_params_accepted(['application/json'])
    def POST(self):
        try:
            # Get login data from request body (replace with actual parsing)
            data = cherrypy.request.json
            username = data.get('username')
            password = data.get('password')
            
            # Validate data
            if not username or not password:
                raise ValueError("Missing username or password")
            
            # Retrieve user information from database
            user = get_user(username)
            
            # Validate credentials
            if user and user['password'] == password:
                # Login successful, return user information
                return json.dumps({'success': True, 'user': user})
            else:
                # Login failed, return error message
                return json.dumps({'success': False, 'message': 'Invalid username/password'})
        except Exception as e:
            # Handle any errors
            return json.dumps({'success': False, 'message': str(e)})



# import dbconnect

# # Start session
# import cherrypy


# # Check if form is submitted
# if cherrypy.request.method == "POST":
#     # Database connection
#     conn = dbconnect.get_connection()

#     # Check connection
#     if not conn.is_connected():
#         raise Exception("Connection failed: " + str(conn.errno))

#     # Check if form data is set
#     if "usn" in cherrypy.request.params and "psw" in cherrypy.request.params:
#         # Retrieve form data
#         username = conn.cursor().escape_string(cherrypy.request.params["usn"])
#         password = conn.cursor().escape_string(cherrypy.request.params["psw"])

#         # Query to check login credentials
#         query = f"SELECT * FROM users WHERE username='{username}' AND password='{password}'"
#         cursor = conn.cursor()
#         cursor.execute(query)
#         result = cursor.fetchall()

#         # Check if user exists
#         if len(result) == 1:
#             # User found
#             row = result[0]
#             # Store user ID in session variable
#             cherrypy.session["user_id"] = row[0]
#             # Redirect based on user role
#             if row[3] == 'student':
#                 raise cherrypy.HTTPRedirect("../htmls/student_index")
#             elif row[3] == 'tutor':
#                 raise cherrypy.HTTPRedirect("../htmls/tutor_index")
#             else:
#                 print("Invalid role")
#         else:
#             print("Invalid username/password")

#     # Close database connection
#     conn.close()

