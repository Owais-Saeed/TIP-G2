import mysql.connector

def get_connection():
    servername = "localhost"
    username = "root"
    password = ""
    dbname = "tip"

    conn = mysql.connector.connect(
        host=servername,
        user=username,
        password=password,
        database=dbname
    )

    if conn.is_connected():
        print("Connected to MySQL database")
    else:
        print("Connection failed:", conn.connect_error)
