<?php
  session_start();
  include 'db/db.php';
  if (!isset($_SESSION['patient_id'])) {
    return;
  }
  // Assuming patient_id is stored in session
  $patient_id = $_SESSION['patient_id'];

  $updateSeen = $conn->prepare("UPDATE notifications SET seen = 1 WHERE patient_id = ? AND seen = 0");
  $updateSeen->bind_param("i", $patient_id);
  $updateSeen->execute();
  $updateSeen->close();
  
    $stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE patient_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class=" ">
  <?php include 'header/header.php' ?>
  <div class="bg-gray-50 min-h-screen flex flex-col items-center">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-lg p-6 mt-4">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
        Notifications
      </h2>

        <?php if (empty($notifications)): ?>
          <div class="text-center text-gray-500 py-6">
            No notifications yet.
          </div>
        <?php else: ?>
          <div class="space-y-4">
            <?php foreach ($notifications as $note): ?>
              <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-100 transition">
                <div class="flex items-start justify-between">
                  <p class="text-gray-700"><?php echo htmlspecialchars($note['message']); ?></p>
                </div>
                <div class="text-sm text-gray-500 mt-2">
                  <?php echo date("F j, Y • g:i A", strtotime($note['created_at'])); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

  </div>
</body>
</html>
