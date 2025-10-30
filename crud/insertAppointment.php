<?php 
    session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Database config
        include '../db/db.php';

        $service_id = (int) $_POST['selected_service'];
        $date_selected = $_POST['selected_date'];
        $time_selected = $_POST['selected_time'];
        $date = date('Y-m-d', strtotime($date_selected));
        $time = date('H:i:s', strtotime($time_selected)); 

        $emergency_firstname = $_POST['em_firstname'] ?? '';
        $emergency_lastname = $_POST['em_lastname'] ?? '';
        $emergency_relationship = $_POST['em_relationship'] ?? '';
        $emergency_number = $_POST['em_phone'] ?? '';
        $emergency_barangay = $_POST['em_barangay'] ?? '';
        $emergency_city = $_POST['em_city'] ?? '';
        $emergency_email = $_POST['em_email'] ?? '';
        $others = $_POST['others'] ?? '';

        $primary_physician_firstname = $_POST['primary_physician_firstname'] ?? '';
        $primary_physician_lastname = $_POST['primary_physician_lastname'] ?? '';
        $have_allergies = $_POST['allergies_medical'] ?? '';
        $current_medications = $_POST['current_medications'] ?? '';
        $conditions = '';
        $health_allergies = $_POST['vision_allergies'] ?? '';
        $contact_lenses = $_POST['contact_lens'] ?? '';
        $any_problems = $_POST['problems'] ?? '';
        $reason_for_visits = $_POST['reason_for_visit'] ?? '';
        $last_visit = $_POST['last_visit'] ?? '';
        $file = $_FILES['file_uploaded'];
        $existing_eye_glasses = $_POST['aye_glasses'];
        $eye_prescription = $_POST['prev_perscription'];

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
        $userId = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $stmt = $conn->prepare("UPDATE patients SET emergency_firstname = ? , emergency_lastname = ?, emergency_relationship = ?, emergency_number = ?, emergency_barangay = ?, emergency_city = ?, emergency_email = ? WHERE id = ?");
            $stmt->bind_param(
                'sssssssi',
                $emergency_firstname,
                $emergency_lastname,
                $emergency_relationship,
                $emergency_number,
                $emergency_barangay,
                $emergency_city,
                $emergency_email,
                $_SESSION['patient_id'],
            );
            $stmt->execute();
            
            if (isset($_SESSION['patient_id'])) {

                
                $stmt = $conn->prepare("SELECT * FROM medical_histories WHERE patient_id = ?");
                $stmt->bind_param("s", $_SESSION['patient_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $mediHist = $result->fetch_assoc();
                if ($mediHist ) {
                        
                    if (!empty($fileName)) {
                        $stmtMedicalHistory = $conn->prepare("
                            UPDATE medical_histories 
                            SET 
                                physician_firstname = ?, 
                                physician_lastname = ?, 
                                have_allergies = ?, 
                                current_medications = ?, 
                                conditions = ?, 
                                health_allergies = ?, 
                                contact_lenses = ?, 
                                any_problems = ?, 
                                reason_for_visits = ?, 
                                others = ?, 
                                file_url = ?, 
                                last_visit = ?, 
                                eye_prescription = ?, 
                                existing_eye_glasses = ?
                            WHERE patient_id = ?
                        ");

                        $stmtMedicalHistory->bind_param(
                            "sssssssssssssii",
                            $primary_physician_firstname,
                            $primary_physician_lastname,
                            $have_allergies,
                            $current_medications,
                            $conditions,
                            $health_allergies,
                            $contact_lenses,
                            $any_problems,
                            $reason_for_visits,
                            $others,
                            $fileName, 
                            $last_visit,
                            $eye_prescription,
                            $existing_eye_glasses,
                            $_SESSION['patient_id']
                        );
                    } else {
                        $stmtMedicalHistory = $conn->prepare("
                            UPDATE medical_histories 
                            SET 
                                physician_firstname = ?, 
                                physician_lastname = ?, 
                                have_allergies = ?, 
                                current_medications = ?, 
                                conditions = ?, 
                                health_allergies = ?, 
                                contact_lenses = ?, 
                                any_problems = ?, 
                                reason_for_visits = ?, 
                                others = ?, 
                                last_visit = ?, 
                                eye_prescription = ?, 
                                existing_eye_glasses = ?
                            WHERE patient_id = ?
                        ");

                        $stmtMedicalHistory->bind_param(
                            "ssssssssssssii",
                            $primary_physician_firstname,
                            $primary_physician_lastname,
                            $have_allergies,
                            $current_medications,
                            $conditions,
                            $health_allergies,
                            $contact_lenses,
                            $any_problems,
                            $reason_for_visits,
                            $others,
                            $last_visit,
                            $eye_prescription,
                            $existing_eye_glasses,
                            $_SESSION['patient_id']
                        );
                    }

                    $stmtMedicalHistory->execute();
                } else {
                    $stmtMedicalHistory = $conn->prepare("INSERT INTO medical_histories (`patient_id`, `physician_firstname`, `physician_lastname`, `have_allergies`, `current_medications`, `conditions`, `health_allergies`, `contact_lenses`, `any_problems`, `reason_for_visits`, `others`, file_url, last_visit,eye_prescription, existing_eye_glasses) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmtMedicalHistory->bind_param(
                        "issssssssssssss", 
                        $_SESSION['patient_id'],
                        $primary_physician_firstname,
                        $primary_physician_lastname,
                        $have_allergies,
                        $current_medications,
                        $conditions,
                        $health_allergies,
                        $contact_lenses,
                        $any_problems,
                        $reason_for_visits,
                        $others,
                        $fileName,
                        $last_visit,
                        $eye_prescription,
                        $existing_eye_glasses,
                    );
                    $stmtMedicalHistory->execute();
                }
                $price = 0;
                $select_stmt = $conn->prepare("SELECT price FROM services WHERE id = ?");
                $select_stmt->bind_param('i', $service_id);
                $select_stmt->execute();
                
                $result = $select_stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $service_data = $result->fetch_assoc();
                    $price = $service_data['price']; 
                }
                $select_stmt->close();

                $stmt = $conn->prepare("INSERT INTO appointments (patient_id,service_id,date_selected,time_selected,price) values (?, ?, ?, ?, ?)");
                $stmt->bind_param(
                    'iissi',
                    $_SESSION['patient_id'],
                    $service_id,
                    $date,
                    $time,
                    $price
                );
                
                
                if ($stmt->execute()) {
                    header("Location: ../appointment.php?success");
                    exit;
                }
            } else {
                if ($stmt->execute()) {
                    header("Location: ../appointment.php?error=no_patient");
                    exit;
                }
            }
        }
    }