<?php 


    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $duration = $_POST['duration'];
        $available = $_POST['available'];
        $file = $_FILES['imageUrl'];

        include '../db/db.php';
        
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

        $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ?, minutes_duration = ?, available = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("ssdissi", $name, $description, $price, $duration, $available,  $fileName, $id);
        $stmt->execute();

        // Redirect or respond with success message
        header('Location: ../listofservices.php');
        exit();
    }
?>