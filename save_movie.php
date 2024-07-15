<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $genre = $_POST["genre"];
    $duration = $_POST["duration"];
    $plot_summary = $_POST["plot_summary"];
    $director_name = $_POST["director_name"];
    $director_gender = $_POST["director_gender"];
    $director_nationality = $_POST["director_nationality"];
    $actors = $_POST["actor_name"];
    $actor_genders = $_POST["actor_gender"];
    $actor_nationalities = $_POST["actor_nationality"];
    $budget = $_POST["budget"];
    $producer_name = $_POST["producer_name"];
    $release_date = $_POST["release_date"];
    $trailer_link = $_POST["trailer_link"];

    // Connect to your MySQL database
    $conn = new mysqli("localhost", "root", "Sgsocl#112604", "FilmDatabase");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert movie details into movies table
    $insert_movie_query = "INSERT INTO movie (title, genre, duration_minutes, plot_summary) VALUES ('$name', '$genre', '$duration', '$plot_summary')";
    if ($conn->query($insert_movie_query) === TRUE) {
        $movie_id = $conn->insert_id;

        // Insert director details into director table
        $insert_director_query = "INSERT INTO director (director_name, gender, nationality) VALUES ('$director_name', '$director_gender', '$director_nationality')";
        if ($conn->query($insert_director_query) === TRUE) {
            $director_id = $conn->insert_id;
            // Update movie entry with director_id
            $update_movie_query = "UPDATE movie SET director_id='$director_id' WHERE film_id='$movie_id'";
            $conn->query($update_movie_query);
        } else {
            echo "Error inserting director: " . $conn->error;
        }

        // Insert actors details into actors table (if not already existing)
        for ($i = 0; $i < count($actors); $i++) {
            $actor_name_parts = explode(" ", $actors[$i], 2);
            $actor_first_name = $actor_name_parts[0];
            $actor_last_name = isset($actor_name_parts[1]) ? $actor_name_parts[1] : '';

            // Check if actor already exists
            $check_actor_query = "SELECT * FROM actors WHERE first_name='$actor_first_name' AND last_name='$actor_last_name'";
            $result = $conn->query($check_actor_query);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $actor_id = $row['actor_id'];
            } else {
                // Insert actor if not already existing
                $insert_actor_query = "INSERT INTO actors (first_name, last_name, gender, nationality) VALUES ('$actor_first_name', '$actor_last_name', '{$actor_genders[$i]}', '{$actor_nationalities[$i]}')";
                if ($conn->query($insert_actor_query) !== TRUE) {
                    echo "Error inserting actor: " . $conn->error;
                } else {
                    $actor_id = $conn->insert_id;
                }
            }

            // Link the actor to the movie
            $insert_movie_actor_query = "INSERT INTO movie_actor (movie_id, actor_id) VALUES ('$movie_id', '$actor_id')";
            if ($conn->query($insert_movie_actor_query) !== TRUE) {
                echo "Error linking actor to movie: " . $conn->error;
            }
        }

        // Insert budget details into budget table
        $insert_budget_query = "INSERT INTO budget (film_id, budget_amount) VALUES ('$movie_id', '$budget')";
        if ($conn->query($insert_budget_query) !== TRUE) {
            echo "Error inserting budget: " . $conn->error;
        }

        // Insert producer details into producer table (if not already existing)
        // Check if producer already exists
        $check_producer_query = "SELECT * FROM producer WHERE producer_name='$producer_name'";
        $result = $conn->query($check_producer_query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $producer_id = $row['producer_id'];
        } else {
            // Insert producer if not already existing
            $insert_producer_query = "INSERT INTO producer (producer_name) VALUES ('$producer_name')";
            if ($conn->query($insert_producer_query) !== TRUE) {
                echo "Error inserting producer: " . $conn->error;
            } else {
                $producer_id = $conn->insert_id;
            }
        }

        // Update movie entry with producer_id
        $update_movie_query = "UPDATE movie SET producer_id='$producer_id' WHERE film_id='$movie_id'";
        if ($conn->query($update_movie_query) !== TRUE) {
            echo "Error updating movie with producer ID: " . $conn->error;
        }

        // Insert release date details into releasedate table
        $insert_release_date_query = "INSERT INTO releasedate (film_id, release_date) VALUES ('$movie_id', '$release_date')";
        if ($conn->query($insert_release_date_query) !== TRUE) {
            echo "Error inserting release date: " . $conn->error;
        }

        // Insert trailer link details into trailer table
        $insert_trailer_query = "INSERT INTO trailer (film_id, trailer_link) VALUES ('$movie_id', '$trailer_link')";
        if ($conn->query($insert_trailer_query) !== TRUE) {
            echo "Error inserting trailer link: " . $conn->error;
        }

        // Close connection
        $conn->close();

        // Redirect to movies.html
        header("Location: movies.html");
        exit();
    } else {
        echo "Error inserting movie: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>

?>

