<?php

session_start();

//include("connection.php");

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "secretdi";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

    if (array_key_exists("content", $_POST)) {


        $sql = "UPDATE users SET diary= '".mysqli_real_escape_string($conn, $_POST['content'])."'WHERE id = ".mysqli_real_escape_string($conn, $_SESSION['id'])." LIMIT 1";

        mysqli_query($conn, $sql);
}

?>
