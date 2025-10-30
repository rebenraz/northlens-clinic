<?php
    // DB connection
    include '../db/db.php';

    $date_selected = $_POST['date'];
    $time_selected = $_POST['time'];
    $date = date('Y-m-d', strtotime($date_selected));
    $time = date('H:i:s', strtotime($time_selected));
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT patient_id FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
    $stmt->close();
    $message = "Your appointment has been rescheduled.";

    $patient_id = $appointment['patient_id'];

    $stmt = $conn->prepare("INSERT INTO notifications (patient_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $patient_id, $message);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE appointments SET date_selected = ?,time_selected = ? WHERE id = ?");
    $stmt->bind_param(
        'ssi',
        $date,
        $time,
        $id
    );
    if ($stmt->execute()) {

        if (isset($_GET['patientResched'])) {
            header("Location: ../appointment.php?successresched");
            exit;
        } else {
            header("Location: ../admin-appointment.php?viewall");
            exit;
        }
    }

?>