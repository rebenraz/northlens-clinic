<?php 
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $duration = 0;
        $available = $_POST['available'];
        $file = $_FILES['imageUrl'];


        // Database config
        
        include '../db/db.php';
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $fileName = '';
        if ($file['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../images/services/";
            $targetDirS = "images/services/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFilename = date('Y-m-d_His') . '.' . $extension;
            $targetFile = $targetDir . $newFilename;
            $fileName = $targetDirS . $newFilename;
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            }
        } 
        $stmt = $conn->prepare("INSERT INTO services (name, description, price, minutes_duration, available, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $name, $description, $price, $duration, $available, $fileName);
        $stmt->execute();

        // Redirect or respond with success message
        header('Location: ../listofservices.php');
        exit();
    }
?>