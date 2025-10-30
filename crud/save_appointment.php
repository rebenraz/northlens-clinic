<?php 
    session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        include '../db/db.php';

        $patient_id = (int) $_REQUEST['patient_id'];
        $service_id = (int) $_REQUEST['service_id'];
        $date_selected = $_REQUEST['date'];
        $time_selected = $_REQUEST['time'];
        $date = date('Y-m-d', strtotime($date_selected));
        $time = date('H:i:s', strtotime($time_selected)); 

        $userId = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $price = 0;


        if ($user) {
            $select_stmt = $conn->prepare("SELECT price FROM services WHERE id = ?");
            $select_stmt->bind_param('i', $service_id);
            $select_stmt->execute();
            
            $result = $select_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $service_data = $result->fetch_assoc();
                $price = $service_data['price']; 
            }
            $select_stmt->close();

            $stmt = $conn->prepare("INSERT INTO appointments (patient_id,service_id,date_selected,time_selected,status,price) values (?, ?, ?, ?,'APPROVED', ?)");
            $stmt->bind_param(
                'iissi',
                $patient_id,
                $service_id,
                $date,
                $time,
                $price,
            );
             $stmt->execute();
        }
    }