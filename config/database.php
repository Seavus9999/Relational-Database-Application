<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');
define('DB_NAME', 'cs6400_spring23_team040');

// create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

//check connection
if ($conn->connect_error) {
    die('Connection Failed' . $conn->connect_error);
}

// echo DB_NAME . ' CONNECTED!';
