<?php 
    session_start();

    include '../db/db.php';

    $user_id = $_SESSION['user_id'] ?? null;
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $date_of_birth = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $barangay = $_POST['barangay'] ?? '';
    $city = $_POST['city'] ?? '';
    $phone_number = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $emergency_firstname = $_POST['em_firstname'] ?? '';
    $emergency_lastname = $_POST['em_lastname'] ?? '';
    $emergency_relationship = $_POST['em_relationship'] ?? '';
    $emergency_number = $_POST['em_phone'] ?? '';
    $emergency_barangay = $_POST['em_barangay'] ?? '';
    $emergency_city = $_POST['em_city'] ?? '';
    $emergency_email = $_POST['em_email'] ?? '';
    $others = $_POST['others'] ?? '';
    $middleInitial = $_POST['middleInitial'] ?? '';
    $file = $_FILES['file_uploaded'];
    $existing_eye_glasses = $_POST['aye_glasses'];
    $eye_prescription = $_POST['prev_perscription'];

    $stmt = $conn->prepare("INSERT INTO patients ( `user_id`, `firstname`, `lastname`, `date_of_birth`, `gender`, `barangay`, `city`, `phone_number`, `email`, `emergency_firstname`, `emergency_lastname`, `emergency_relationship`, `emergency_number`, `emergency_barangay`, `emergency_city`, `emergency_email`, `middle_initial`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "issssssssssssssss", // i = integer, s = string (adjust as needed)
        $user_id,
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
        $emergency_email,
        $middleInitial
    );
    $fileName = '';
    if ($file['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../images/histories/";
        $targetDirS = "images/histories/";
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
    // Execute the statement
    if ($stmt->execute()) {
        $primary_physician_firstname = $_POST['primary_physician_firstname'] ?? '';
        $primary_physician_lastname = $_POST['primary_physician_lastname'] ?? '';
        $have_allergies = $_POST['allergies_medical'] ?? '';
        $current_medications = $_POST['current_medications'] ?? '';
        $conditions = '';
        $health_allergies = $_POST['vision_allergies'] ?? '';
        $contact_lenses = $_POST['contact_lens'] ?? '';
        $any_problems = $_POST['problems'] ?? '';
        $reason_for_visits = $_POST['reason_for_visit'] ?? '';
        $date = $_POST['date'] ?? '';
        $last_visit = $_POST['last_visit'] ?? '';

        if (isset($_POST['conditions'])) {
            $selected_conditions = $_POST['conditions']; // This will be an array
            foreach ($selected_conditions as $condition) {
                $conditions = $conditions . "," . htmlspecialchars($condition) ;
            }
        }

        $patient_id = $conn->insert_id; // Store patient ID in session
        $stmtMedicalHistory = $conn->prepare("INSERT INTO medical_histories (`patient_id`, `physician_firstname`, `physician_lastname`, `have_allergies`, `current_medications`, `conditions`, `health_allergies`, `contact_lenses`, `any_problems`, `reason_for_visits`, `date`, `others`, file_url, last_visit, eye_prescription, existing_eye_glasses) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtMedicalHistory->bind_param(
            "isssssssssssssss", // i = integer, s = string (11 parameters)
            $patient_id,
            $primary_physician_firstname,
            $primary_physician_lastname,
            $have_allergies,
            $current_medications,
            $conditions,
            $health_allergies,
            $contact_lenses,
            $any_problems,
            $reason_for_visits,
            $date,
            $others,
            $fileName,
            $last_visit,
            $eye_prescription,
            $existing_eye_glasses,
        );
        $stmtMedicalHistory->execute();

        $_SESSION['patient_id'] = $patient_id;
        
        header("Location: ../registration.php?success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
?>