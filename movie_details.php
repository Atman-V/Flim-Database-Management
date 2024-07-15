<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movie Details</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles6.css">
</head>
<body>
  <div class="container">
    <h1>Movie Details</h1>

    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "Sgsocl#112604";
    $dbname = "FilmDatabase";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if film_id is provided in the URL
    if (isset($_GET['id'])) {
        $film_id = $_GET['id'];

        // Fetch movie details from the database
        $sql = "SELECT movie.title AS title, movie.genre AS genre, movie.director_id AS director_id, director.director_name AS director_name, director.gender AS director_gender, director.nationality AS director_nationality, producer.producer_name AS producer_name, budget.budget_amount AS budget_amount, releasedate.release_date AS release_date FROM movie 
        LEFT JOIN director ON movie.director_id = director.director_id 
        LEFT JOIN producer ON movie.producer_id = producer.producer_id 
        LEFT JOIN budget ON movie.film_id = budget.film_id 
        LEFT JOIN releasedate ON movie.film_id = releasedate.film_id
        WHERE movie.film_id = $film_id";
        $result = $conn->query($sql);

        // Check if movie found
        if ($result && $result->num_rows > 0) {
            // Output data of the movie
            $row = $result->fetch_assoc();
            echo "<h2>" . $row["title"] . "</h2>";
            echo "<p><strong>Genre:</strong> " . $row["genre"] . "</p>";
            echo "<p><strong>Actors:</strong> ";
            $actor_sql = "SELECT actors.first_name, actors.gender, actors.nationality FROM actors 
                            INNER JOIN movie_actor ON actors.actor_id = movie_actor.actor_id 
                            WHERE movie_actor.movie_id = $film_id";
            $actor_result = $conn->query($actor_sql);
            if ($actor_result && $actor_result->num_rows > 0) {
                while ($actor_row = $actor_result->fetch_assoc()) {
                    echo $actor_row["first_name"] . " - " . $actor_row["gender"] . " - " . $actor_row["nationality"] . "<br>";
                }
            } else {
                echo "No actors found for this movie.";
            }
            echo "</p>";
            echo "<p><strong>Director:</strong> " . $row["director_name"] . " - " . $row["director_gender"] . " - " . $row["director_nationality"] . "</p>";
            echo "<p><strong>Producer:</strong> " . ($row["producer_name"] ?? "No producer found for this movie.") . "</p>";
            echo "<p><strong>Budget:</strong> $" . number_format($row["budget_amount"], 2) . "</p>";
            echo "<p><strong>Release Date:</strong> " . $row["release_date"] . "</p>";
        } else {
            echo "<p>No movie found with the provided ID.</p>";
        }
    } else {
        echo "<p>Movie ID not provided.</p>";
    }

    // Close connection
    $conn->close();
    ?>

  </div>
</body>
</html>
