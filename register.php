<?php
// MySQL database credentials
$servername = "localhost";
$username = "root";
$password = "Sgsocl#112604";
$dbname = "filmdatabase";

// Connect to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Escape special characters in password
    $password = mysqli_real_escape_string($conn, $password);

    // Check if username already exists
    $check_query = "SELECT * FROM users WHERE username=?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists";
    } else {
        // Insert new user into database
        $insert_query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ss", $username, $password);

        if ($insert_stmt->execute() === TRUE) {
            echo "Registered successfully";
            // Redirect to 2nd page
            header("Location: 2ndpage.html");
            exit();
        } else {
            echo "Error: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }

    // Close statements
    $check_stmt->close();
}

// Close connection
$conn->close();
?>
