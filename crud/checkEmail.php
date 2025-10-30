<?php
    session_start();
    
    include '../db/db.php';

    if (isset($_GET['email'])) {
        $email = $conn->real_escape_string($_GET['email']);
        $query = $conn->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");

        echo $query->num_rows > 0 ? "exists" : "ok";
    }