<?php 
     
    include '../db/db.php';
    
    $appointments = [];
    
    $startOfWeek = new DateTime();
    $startOfWeek->modify('Monday this week');
    $startOfWeek->format('Y-m-d');

    for ($i = 0; $i < 7; $i++) {
        $day = clone $startOfWeek;
        $day->modify("+$i days");
        $date = $day->format('Y-m-d');
        $stmt = $conn->prepare('SELECT * FROM appointments where date(date_selected) = ? ');
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;

         $appointments[] =$count;

    }

    echo json_encode($appointments);


?>