<?php

$servername = "localhost";
$username = "root";
$password = "root";

if (array_key_exists('email', $_POST) OR array_key_exists('password', $_POST)) {

// Create connection
$conn = mysqli_connect($servername, $username, $password, 'secretdi');
// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}
}

?>