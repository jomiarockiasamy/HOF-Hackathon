<?php
$host = 'localhost';
$dbusername = 'root';
$dbpassword = '';
$dbname = 'user_info';

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$category = $input['category'];
$sortOrder = $input['sortOrder'];

// Build SQL query
if ($category === 'All') {
    $sql = "SELECT * FROM HelpRequests";
} else {
    $sql = "SELECT * FROM HelpRequests WHERE category = ?";
}

if ($sortOrder === 'priority') {
    $sql .= " ORDER BY priorityScore DESC LIMIT 10";
} else { // default newest
    $sql .= " ORDER BY createdAt DESC LIMIT 10";
}

if ($category === 'All') {
    $stmt = $conn->prepare($sql);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);

$stmt->close();
$conn->close();
?>