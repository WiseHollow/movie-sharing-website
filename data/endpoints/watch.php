<?php

// Get our connection function.
require('data/connection.php');
// Establish connection to database.
$conn = getConnection();

function getMovie($conn, $id) {
  // Prepare statement to get all data related to movie of id.
  // Here is the sql for ratings when we want it. JOIN movies_ratings ON movies.id = movies_ratings.id
  $sql = $conn->prepare('SELECT * FROM movies
    JOIN movies_description ON movies.id = movies_description.id
    LEFT JOIN movies_countries ON movies.id = movies_countries.movie_id
    WHERE movies.id = ? AND active = 1');
  $sql->bind_param('i', $id);
  $sql->execute();
  $result = $sql->get_result();

  // Create variable to store movie data.
  $movie = array();

  while ($row = $result->fetch_assoc()) {
    // Store movie data and break out of loop. Should only be 1 result.
    $movie = $row;
    break;
  }

  // We must increment our movie views on the server, plus what is being sent to the client.
  // First we have to check if the movie data was populated.
  if (isset($movie['views'])) {
    incrementViews($conn, $id);
    $movie['views'] = $movie['views'] + 1;
  }

  // Return movie.
  return $movie;
}

function getMovieUrls($conn, $id) {
  $sql = $conn->prepare('SELECT url, language
    FROM movies_urls
    WHERE movies_urls.movie_id = ?');
  $sql->bind_param('i', $id);
  $sql->execute();
  $result = $sql->get_result();

  $urls = array();

  while ($row = $result->fetch_assoc()) {
    array_push($urls, $row);
  }

  return $urls;
}

function getGenres($conn, $id) {
  $sql = $conn->prepare('SELECT genre FROM genres
    JOIN movies_genres ON movies_genres.movie_id = ?
    WHERE genres.id = movies_genres.genre_id');
  $sql->bind_param('i', $id);
  $sql->execute();
  $result = $sql->get_result();

  // Where we store all the Genres
  $genres = array();

  // Store them!
  while ($row = $result->fetch_assoc()) {
    array_push($genres, $row['genre']);
  }

  return $genres;
}

function getSimilarMovies($conn, $genres) {
  $inArray = array();
  foreach ($genres as $genre) {
    array_push($inArray, "'" . $genre . "'");
  }
  $in = implode(', ', $inArray);

  $sql = 'SELECT DISTINCT v.id, title, thumbnail
    FROM movies v
    JOIN movies_genres vg ON vg.movie_id = v.id
    JOIN movies_thumbnails vt ON vt.id = v.id
    WHERE vg.genre_id IN
    (
      SELECT g.id
      FROM genres g
      WHERE g.genre IN (' . $in . ')
    )';
  $result = $conn->query($sql);
  $movies = array();
  if ($result) {
    while ($row = $result->fetch_assoc()) {
      array_push($movies, $row);
    }
  }
  return $movies;
}

function getTags($conn, $id) {

}

function incrementViews($conn, $id) {
  $sql = $conn->prepare('UPDATE movies SET views = views + 1 WHERE movies.id = ?');
  $sql->bind_param('i', $id);
  $sql->execute();
}

function getTrailer($conn, $id) {
  $sql = $conn->prepare('SELECT url FROM movies_trailers WHERE id = ?');
  $sql->bind_param('i', $id);
  $sql->execute();
  $result = $sql->get_result();
  $trailer = '';
  while ($row = $result->fetch_assoc()) {
    $trailer = $row['url'];
    break;
  }
  return $trailer;
}

function thumbsUp($conn, $id) {
  // TODO: Requires IP address to be checked.
}

function thumbsDown($conn, $id) {
  // TODO: Requires IP address to be checked.
}

?>
