<?php
    include '../db/db.php';

    $stmt = $conn->prepare("
        SELECT service_name, COUNT(*) AS total
        FROM appointments
        WHERE status != 'canceled'
        GROUP BY service_name
        ORDER BY total DESC
        LIMIT 5
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    $topServices = [];
    while ($row = $result->fetch_assoc()) {
        $topServices[] = $row;
    }

    echo json_encode($topServices);