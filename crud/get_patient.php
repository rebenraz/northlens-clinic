<?php
    
    include '../db/db.php';

    $result = $conn->query("SELECT id, firstname, lastname FROM patients ORDER BY firstname ASC");
    $patients = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($patients);