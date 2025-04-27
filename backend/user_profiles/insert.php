<?php
$name = $_POST['name'];
$email = $_POST['email'];
$phonenumber = $_POST['phone'];
$cash = $_POST['cashapp'];
$zipcode = $_POST['zipcode'];
$username = $_POST['username'];
$password = $_POST['password'];


if(empty($name) || empty($email) || empty($phonenumber) || empty($cash) || empty($zipcode) || empty($username) || empty($password)) {
    echo "All fields are required.";
    die();
}
else {
    $host = 'localhost';
    $dbusername = 'users';
    $dbpassword = 'password';
    $dbname = 'user_info';
}

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO Users (name, email, phone, cashapp, zipcode, username, password) VALUES ('$name', '$email', '$phonenumber', '$cash', '$zipcode', '$username', '$password')";

$stmt = $conn->prepare($sql);

if($stmt){
    $stmt->blind_param("sssssss", $name, $email, $phonenumber, $cash, $zipcode, $username, $password);
    $stmt->execute();
    echo "User information saved successfully.";
}
else{
    echo "Error preparing statement: " . $conn->error;
}

$stmt->close();
$conn->close();

?>