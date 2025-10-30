<?php
    session_start();

    include '../db/db.php';

    // Get form data
    $username = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
    // Query user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $stmtpatient = $conn->prepare("SELECT * FROM patients WHERE user_id = ?");
        $stmtpatient->bind_param("i", $user['id']);
        $stmtpatient->execute();

        $res = $stmtpatient->get_result();
        $patient = $res->fetch_assoc();

        if ($patient) {
            $_SESSION['patient_id'] = $patient['id'];
        }

        header("Location: ../index.php"); // redirect to dashboard
        exit;
    } else {
        header("Location: ../login.php?login&error=Invalid email or password.");
        exit;
    }

    $stmt->close();
    } else {
        header("Location: ../login.php?login&error=Invalid email or password.");
        exit;
    }

    $conn->close();
?>
