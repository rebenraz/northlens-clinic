<?php
  // Database config
  include '../db/db.php';

  $selected_date = $_POST['selected_date'];
  $user_id = 1; // replace this with your actual session user ID or input
  $date = date('Y-m-d', strtotime($selected_date));

  $times = ["9:00AM", "9:30AM", "10:00AM", "10:30AM", "11:00AM", "11:30AM",
            "1:00PM", "1:30PM", "2:00PM", "2:30PM", "3:00PM", "3:30PM", "4:00PM", "4:30PM"];

  foreach ($times as $time) {
    // Convert time to 24h format for querying DB
    $formattedTime = date('H:i:s', strtotime($time));

    $stmt = $conn->prepare("SELECT id FROM appointments WHERE status <> 'CANCELED' AND date_selected = ? AND time_selected = ?");
    $stmt->bind_param("ss", $date, $formattedTime);
    $stmt->execute();
    $stmt->store_result();

    $isBooked = $stmt->num_rows > 0;
    $disabled = $isBooked ? 'disabled class="cursor-not-allowed px-4 py-2 rounded text-red-500 border border-red-800 "' : 'class="border px-4 py-2 rounded hover:bg-blue-100 time-slot text-emerald-600"';

    echo "<button type='button' $date $disabled data-time='$time'>$time</button> ";
  }
?>