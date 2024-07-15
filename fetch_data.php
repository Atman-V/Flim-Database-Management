<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Input Movies</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .movie {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .details {
            font-size: 16px;
            line-height: 1.6;
        }

        .actors {
            margin-top: 10px;
        }

        .actors span {
            display: block;
        }

        .trailer {
            margin-top: 10px;
        }

        .trailer a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .trailer a:hover {
            text-decoration: underline;
        }

        .trailer.not-available {
            color: #777;
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            .movie {
                padding: 15px;
            }

            .title {
                font-size: 20px;
            }

            .details {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>User Input Movies</h1>
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

    // Fetch movies from the database including actors' names and trailer links
    $sql = "SELECT movie.title, movie.genre, director.director_name, budget.budget_amount, releasedate.release_date, GROUP_CONCAT(actors.first_name, ' ', actors.last_name SEPARATOR ', ') AS actors, trailer.trailer_link
            FROM movie
            LEFT JOIN director ON movie.director_id = director.director_id
            LEFT JOIN budget ON movie.film_id = budget.film_id
            LEFT JOIN releasedate ON movie.film_id = releasedate.film_id
            LEFT JOIN movie_actor ON movie.film_id = movie_actor.movie_id
            LEFT JOIN actors ON movie_actor.actor_id = actors.actor_id
            LEFT JOIN trailer ON movie.film_id = trailer.film_id
            GROUP BY movie.title";

    $result = $conn->query($sql);

    // Check if any movies found
    if ($result && $result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo '<div class="movie">';
            echo '<div class="title">' . $row['title'] . '</div>';
            echo '<div class="details">';
            echo '<div><strong>Genre:</strong> ' . $row['genre'] . '</div>';
            echo '<div><strong>Director:</strong> ' . $row['director_name'] . '</div>';
            echo '<div class="actors"><strong>Actors:</strong> ';
            echo $row['actors'] ? '<span>' . $row['actors'] . '</span>' : '<span>None</span>';
            echo '</div>';
            echo '<div><strong>Budget:</strong> $' . number_format($row['budget_amount'], 2) . '</div>';
            echo '<div><strong>Release Date:</strong> ' . date('M d, Y', strtotime($row['release_date'])) . '</div>';
            echo '<div class="trailer">';
            if ($row['trailer_link']) {
                echo '<a href="' . $row['trailer_link'] . '" target="_blank">Watch Trailer</a>';
            } else {
                echo '<span class="not-available">Trailer not available</span>';
            }
            echo '</div>';
            // Add more details here as needed
            echo '</div>'; // End details
            echo '</div>'; // End movie
        }
    } else {
        echo "<p>No movies found.</p>";
    }
    // Close connection
    $conn->close();
    ?>
</div>
</body>
</html>
