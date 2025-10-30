<?php
    session_start();

    include '../db/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id     = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
        if ($id && $rating >= 1 && $rating <= 5) {
            $stmt = $conn->prepare("UPDATE appointments SET rating = ? WHERE id = ?");
            $stmt->bind_param("ii", $rating, $id);
            $stmt->execute();
        }
        exit;
    }