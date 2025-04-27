<?php

    $servername = "10.251.125.247";
    $username = "root";
    $password = "";
    $dbname = "user_info";  

    $conn = new mysqli($servername, $username, $password, $dbname);

    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CREATE TABLE HelpRequests IF NOT EXISTS my_database (
    requestId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('Shelter', 'Food', 'Clothing', 'Medical', 'Hygiene', 'Job Assistance', 'Transportation', 'Pet Services', 'Other') NOT NULL,
    category VARCHAR(100),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    fulfilled BOOLEAN DEFAULT FALSE,
    zip VARCHAR(100),
    keywords TEXT,
    priorityScore INT DEFAULT 0,
    anonymous BOOLEAN DEFAULT FALSE
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully!";
    } else {
        echo "Error creating table: " . $conn->error;
    }
    
    $sql = "INSERT INTO HelpRequests (userId, title, description, category, zip, keywords, priorityScore, anonymous)
    VALUES (1, 'Need Shelter', 'Looking for a temporary place to stay', 'Shelter', '90210', 'emergency, overnight', 5, FALSE)";

    if ($conn->query($sql) === TRUE) {
        echo "Table added to ";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
    
?>