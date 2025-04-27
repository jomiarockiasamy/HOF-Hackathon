from flask import Flask, request, jsonify, session, send_from_directory, redirect, url_for
from flask_cors import CORS
import mysql.connector
from mysql.connector import Error
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

app = Flask(__name__, static_folder='../frontend')
app.secret_key = os.getenv('SECRET_KEY', 'your_secret_key')  # Set your secret key here

# CORS configuration with credentials support
CORS(app, supports_credentials=True, resources={
    r"/api/*": {
        "origins": ["http://localhost:5000", "http://127.0.0.1:5000"],
        "methods": ["GET", "POST", "OPTIONS"],
        "allow_headers": ["Content-Type"]
    }
})

# Database configuration
db_config = {
    'host': 'localhost',
    'user': os.getenv('DB_USER', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'database': os.getenv('DB_NAME', 'user_info')
}

# Root route to serve the frontend
@app.route('/')
def serve_frontend():
    if 'username' in session:  # Check if the user is logged in
        return send_from_directory(app.static_folder, 'home.html')  # Serve a logged-in homepage
    else:
        return send_from_directory(app.static_folder, 'home.html')  # Serve a login page

# Serve static files
@app.route('/<path:path>')
def serve_static(path):
    return send_from_directory(app.static_folder, path)

# Test route
@app.route('/api/test', methods=['GET'])
def test():
    return jsonify({'message': 'Server is running!'})

def get_db_connection():
    try:
        connection = mysql.connector.connect(**db_config)
        return connection
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

@app.route('/api/signup', methods=['POST'])
def signup():
    data = request.json

    required_fields = ['name', 'email', 'phone', 'cashapp', 'zipcode', 'username', 'password']
    for field in required_fields:
        if not data.get(field):
            return jsonify({'error': f'{field} is required'}), 400

    try:
        connection = get_db_connection()
        if not connection:
            return jsonify({'error': 'Database connection failed'}), 500

        cursor = connection.cursor()

        cursor.execute("SELECT username FROM Users WHERE username = %s", (data['username'],))
        if cursor.fetchone():
            return jsonify({'error': 'Username already exists'}), 400

        insert_query = """
        INSERT INTO Users (name, email, phone, cashapp, zipcode, username, password)
        VALUES (%s, %s, %s, %s, %s, %s, %s)
        """
        cursor.execute(insert_query, (
            data['name'], data['email'], data['phone'],
            data['cashapp'], data['zipcode'], data['username'],
            data['password']
        ))

        connection.commit()
        return jsonify({'message': 'User registered successfully'}), 201

    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

@app.route('/api/login', methods=['POST'])
def login():
    data = request.json

    if not data.get('username') or not data.get('password'):
        return jsonify({'error': 'Username and password are required'}), 400

    try:
        connection = get_db_connection()
        if not connection:
            return jsonify({'error': 'Database connection failed'}), 500

        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT * FROM Users WHERE username = %s", (data['username'],))
        user = cursor.fetchone()

        if user and user['password'] == data['password']:
            session['user_id'] = user['id']
            session['username'] = user['username']
            return jsonify({'message': 'Login successful'}), 200
        else:
            return jsonify({'error': 'Invalid username or password'}), 401

    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

@app.route('/api/requests', methods=['GET'])
def get_requests():
    try:
        connection = get_db_connection()
        if not connection:
            return jsonify({'error': 'Database connection failed'}), 500

        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT * FROM Requests")
        requests = cursor.fetchall()

        return jsonify(requests), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

@app.route('/api/request', methods=['POST'])
def generate_request():
    data = request.json

    # Required fields for a help request
    required_fields = ['title', 'description', 'category', 'zip']
    for field in required_fields:
        if not data.get(field):
            return jsonify({'error': f'{field} is required'}), 400

    try:
        connection = get_db_connection()
        if not connection:
            return jsonify({'error': 'Database connection failed'}), 500

        cursor = connection.cursor()

        # Log the data being inserted for debugging
        print(f"Inserting request: {data}")

        # Insert the request into the Requests table
        insert_query = """
        INSERT INTO Requests (title, description, category, zip, keywords, priority_score, anonymous)
        VALUES (%s, %s, %s, %s, %s, %s, %s)
        """
        cursor.execute(insert_query, (
            data['title'], data['description'], data['category'],
            data['zip'], data.get('keywords', ''), data.get('priorityScore', 0),
            data.get('anonymous', False)
        ))

        connection.commit()
        return jsonify({'message': 'Request submitted successfully'}), 201

    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()


@app.route('/api/get_user', methods=['GET'])
def get_user():
    if 'username' in session:
        return jsonify({'username': session['username']})
    else:
        return jsonify({'username': None})

@app.route('/api/logout', methods=['POST'])
def logout():
    session.clear()
    return jsonify({'message': 'Logged out successfully'}), 200

@app.route('/api/users', methods=['GET'])
def get_users():
    try:
        connection = get_db_connection()
        if not connection:
            return jsonify({'error': 'Database connection failed'}), 500

        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT * FROM Users")
        users = cursor.fetchall()

        return jsonify(users), 200

    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

if __name__ == '__main__':
    app.run(debug=True, port=5000)
