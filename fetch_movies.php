<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Sgsocl#112604";
$database = "FilmDatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $film_id = $_POST['film_id'];
    $trailer_link = $_POST['trailer_link'];

    // Insert trailer link into the database
    $sql_insert_trailer = "INSERT INTO trailer (film_id, trailer_link) VALUES ('$film_id', '$trailer_link')";
    if ($conn->query($sql_insert_trailer) === TRUE) {
        echo "<p>Trailer link added successfully.</p>";
    } else {
        echo "<p>Error adding trailer link: " . $conn->error . "</p>";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Trailer Link</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles6.css">
</head>
<body>
  <div class="container">
    <h1>Add Trailer Link</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="film_id">Film ID:</label>
            <input type="number" name="film_id" id="film_id" required>
        </div>
        <div class="form-group">
            <label for="trailer_link">Trailer Link:</label>
            <input type="url" name="trailer_link" id="trailer_link" required>
        </div>
        <button type="submit">Add Trailer Link</button>
    </form>
  </div>
</body>
</html>
