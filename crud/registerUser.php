<?php
    session_start();

    include '../db/db.php';

    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $suffix = $_POST['suffix'] ?? '';
    $mi = $_POST['mi'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $date_of_birth = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $barangay = $_POST['barangay'] ?? '';
    $city = $_POST['city'] ?? '';
    $phone_number = $_POST['phone'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../login.php?signup&email=Email already exists.");
        exit;
    } else {
        $role = 'patient';
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id; // Store user ID in session
            $userId = $conn->insert_id;
            $emergency_firstname = '';
            $emergency_lastname = '';
            $emergency_relationship = '';
            $emergency_number = '';
            $emergency_barangay = '';
            $emergency_city = '';
            $emergency_email = '';
            $stmt = $conn->prepare("INSERT INTO patients ( `user_id`, `firstname`, `lastname`, `date_of_birth`, `gender`, `barangay`, `city`, `phone_number`, `email`, `emergency_firstname`, `emergency_lastname`, `emergency_relationship`, `emergency_number`, `emergency_barangay`, `emergency_city`, `emergency_email`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                    "isssssssssssssss", // i = integer, s = string (adjust as needed)
                    $userId,
                    $firstname,
                    $lastname,
                    $date_of_birth,
                    $gender,
                    $barangay,
                    $city,
                    $phone_number,
                    $email,
                    $emergency_firstname,
                    $emergency_lastname,
                    $emergency_relationship,
                    $emergency_number,
                    $emergency_barangay,
                    $emergency_city,
                    $emergency_email
                );

            // Execute the statement
            if ($stmt->execute()) {
                $patient_id = $conn->insert_id; 
                $_SESSION['patient_id'] = $patient_id;
            }
            header("Location: ../index.php"); // redirect to dashboard
            exit;
        } else {
            header("Location: ../login.php?signup&error=Error creating account.");
            exit;
        }
    }
?>