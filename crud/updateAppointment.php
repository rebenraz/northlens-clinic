<?php 

    session_start();

    // Get form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $available = $_POST['available'];

    include '../db/db.php';
           // // === Mailtrap SMTP credentials ===
    // $smtpServer = "sandbox.smtp.mailtrap.io";
    // $port = 2525;
    // $usernamesmtp = "835c56fb0e5fcf"; // Replace this
    // $passwordsmtp = "d225878675c445"; // Replace this

    // // === Email details ===
    // $from = "sender@example.com";
    // $to = $username;
    // $subject = "Mailtrap Test from Pure PHP (XAMPP)";
    // $message = "Hello! This is a test email sent from XAMPP using pure PHP and Mailtrap.";

    // // === Headers ===
    // $headers = "From: $from\r\n";
    // $headers .= "To: $to\r\n";
    // $headers .= "Subject: $subject\r\n";
    // $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // // === Connect to Mailtrap ===
    // $socket = fsockopen($smtpServer, $port, $errno, $errstr, 10);
    // if (!$socket) {
    //     die("Failed to connect: $errstr ($errno)");
    // }

    // function sendCommand($socket, $command) {
    //     fwrite($socket, $command . "\r\n");
    //     $response = fgets($socket, 512);
    //     echo nl2br(htmlspecialchars($response));
    //     return $response;
    // }

    // // === SMTP Handshake ===
    // fgets($socket); // read server greeting
    // sendCommand($socket, "EHLO localhost");
    // sendCommand($socket, "AUTH LOGIN");
    // sendCommand($socket, base64_encode($usernamesmtp));
    // sendCommand($socket, base64_encode($passwordsmtp));
    // sendCommand($socket, "MAIL FROM:<$from>");
    // sendCommand($socket, "RCPT TO:<$to>");
    // sendCommand($socket, "DATA");

    // // === Send the email content ===
    // fwrite($socket, "$headers\r\n$message\r\n.\r\n");
    // $response = fgets($socket, 512);
    // echo nl2br(htmlspecialchars($response));

    // // === Close the connection ===
    // sendCommand($socket, "QUIT");
    // fclose($socket);

    // echo "<br><b>Email sent successfully (check your Mailtrap inbox)!</b>";

        $status = $_GET['status'];
        $id = $_GET['id'];

        $stmt = $conn->prepare("SELECT patient_id FROM appointments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointment = $result->fetch_assoc();
        $stmt->close();

        $patient_id = $appointment['patient_id'];

        $message = '';
        if ($status === 'APPROVED') {
            $message = "Your appointment has been approved.";
        } elseif ($status === 'CANCELED') {
            $message = "Your appointment has been canceled.";
        } elseif ($status === 'COMPLETED') {
            $message = "Your appointment has been marked as completed.";
        }

        $stmt = $conn->prepare("INSERT INTO notifications (patient_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $patient_id, $message);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        if (isset($_GET['patientUpdate'])) { 
            header('Location: ../appointment.php?canceled');
            exit();
        } else {
            header('Location: ../admin-appointment.php?viewall');
            exit();
        }
    
?>