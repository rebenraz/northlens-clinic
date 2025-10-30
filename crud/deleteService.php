<?php 

    session_start();
    
    include '../db/db.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Prepare and execute delete statement
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header('Location: ../listofservices.php?success=Service deleted successfully.');
            exit();
        } else {
            header('Location: ../listofservices.php?error=Error deleting service.');
            exit();
        }

        $stmt->close();
    } else {
        header('Location: ../listofservices.php?error=Invalid request.');
        exit();
    }
?>