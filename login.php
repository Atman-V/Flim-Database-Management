<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Connect to your MySQL database
    $conn = new mysqli("localhost", "root", "Sgsocl#112604", "filmdatabase");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if username and password match
    $query = "SELECT * FROM users WHERE username=? AND password=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirect to movies.html
        header("Location: movies.html");
        exit();
    } else {
        // Redirect back to login page with error message
        header("Location: login.html?error=1");
        exit();
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
