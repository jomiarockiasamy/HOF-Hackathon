import mysql.connector
from mysql.connector import Error
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

def create_database():
    try:
        # Connect to MySQL server
        connection = mysql.connector.connect(
            host='localhost',
            user=os.getenv('DB_USER', 'root'),
            password=os.getenv('DB_PASSWORD', 'heaps01')
        )
        
        if connection.is_connected():
            cursor = connection.cursor()
            
            # Create database if it doesn't exist
            cursor.execute(f"CREATE DATABASE IF NOT EXISTS {os.getenv('DB_NAME', 'user_info')}")
            cursor.execute(f"USE {os.getenv('DB_NAME', 'user_info')}")
            
            # Create Users table
            cursor.execute("""
                CREATE TABLE IF NOT EXISTS Users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(20) NOT NULL,
                    cashapp VARCHAR(50) NOT NULL,
                    zipcode VARCHAR(10) NOT NULL,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            """)
            cursor.execute("""
                            CREATE TABLE IF NOT EXISTS Requests (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            title VARCHAR(255) NOT NULL,
                            description TEXT NOT NULL,
                            category VARCHAR(100) NOT NULL,
                            zip VARCHAR(20) NOT NULL,
                            keywords VARCHAR(255) NOT NULL,
                            priority_score INT NOT NULL,
                            anonymous BOOLEAN NOT NULL,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )
                    """)
            
            print("Database and table created successfully!")
            
    except Error as e:
        print(f"Error: {e}")
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection is closed")

if __name__ == "__main__":
    create_database() 