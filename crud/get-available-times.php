<?php
// DB connection
       include '../db/db.php';

    if (isset($_GET['date'])) {
        $date = $_GET['date'];
        $times = [
            "9:00AM", "9:30AM", "10:00AM", "10:30AM", "11:00AM", "11:30AM",
            "1:00PM", "1:30PM", "2:00PM", "2:30PM", "3:00PM", "3:30PM", "4:00PM", "4:30PM"
        ];

        $availableTimes = [];

        foreach ($times as $time) {
            $formattedTime = date('H:i:s', strtotime($time));

            $stmt = $conn->prepare("SELECT id FROM appointments WHERE (status='APPROVED' OR status='PENDING') AND date_selected = ? AND time_selected = ?");
            $stmt->bind_param("ss", $date, $formattedTime);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                $availableTimes[] = $time;
            }

            $stmt->close();
        }

        header('Content-Type: application/json');
        echo json_encode($availableTimes);
    }
?>