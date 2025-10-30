<?php
 
    include '../db/db.php';
    
    $stmt = $conn->prepare("SELECT * FROM appointments");      
    $stmt->execute();
    $result = $stmt->get_result();
    $totalAppointment = $result->num_rows;
    
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE status = 'COMPLETED'");      
    $stmt->execute();
    $result = $stmt->get_result();
    $totalCompleteedAppointment = $result->num_rows;

    $stmt = $conn->prepare("SELECT * FROM appointments WHERE status = 'CANCELED'");      
    $stmt->execute();
    $result = $stmt->get_result();
    $totalCcancelAppointment = $result->num_rows;
    $totalAll = $totalAppointment;
    $totalComplete = $totalCompleteedAppointment;
    $totalCancel = $totalCcancelAppointment;
    $totalSchedule = $totalAppointment - $totalCcancelAppointment - $totalCompleteedAppointment;
    $percentCancel = 0;
    $percentComplete = 0;
    $percentSchedule = 0;
    if ($totalAll > 0) {
        $percentComplete = ($totalComplete / $totalAll) * 100;
        $percentCancel = ($totalCancel / $totalAll) * 100;
        $percentSchedule = ($totalSchedule / $totalAll) * 100;
    } else {
        $percentComplete = $percentCancel = $percentSchedule = 0;
    }

    echo json_encode([
        'totalComplete' => round($percentComplete, 2),
        'totalCancel' => round($percentCancel, 2),
        'totalSchedule' => round($percentSchedule, 2),
    ]);

