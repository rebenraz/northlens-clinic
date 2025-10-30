<?php
    $host = "localhost";
    $db = "dbnorthlens"; // your DB name
    $user = "root";    // your DB user
    $pass = "";        // your DB password

    // Create DB connection
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>