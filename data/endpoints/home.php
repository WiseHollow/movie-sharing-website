<?php

require('data/connection.php');
$conn = getConnection();

function getMostViewedMovies($conn) {
  $sql = 'SELECT movies.id, views, title, thumbnail
    FROM movies
    JOIN movies_thumbnails ON movies_thumbnails.id = movies.id
    ORDER BY views DESC
    LIMIT 20';

  $result = $conn->query($sql);
  $movies = array();
  if ($result) {
    while ($row = $result->fetch_assoc()) {
      array_push($movies, $row);
    }
  }
  return $movies;
}

function getLatestMovies($conn) {
  $sql = 'SELECT movies.id, title, thumbnail
  FROM movies
  JOIN movies_thumbnails ON movies_thumbnails.id = movies.id
  ORDER BY added DESC
  LIMIT 20';

  $result = $conn->query($sql);
  $movies = array();
  if ($result) {
    while ($row = $result->fetch_assoc()) {
      array_push($movies, $row);
    }
  }
  return $movies;
}

function getFeaturedMovies($conn) {
  $sql = 'SELECT * FROM movies_featured';
  $result = $conn->query($sql);
  $movies = array();
  if ($result) {
    while ($row = $result->fetch_assoc()) {
      array_push($movies, $row);
    }
  }
  return $movies;
}

?>