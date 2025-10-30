<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Appointments - NorthLens</title>
  <link href="src/output.css" rel="stylesheet">
</head>
<body class="bg-slate-50 text-gray-800">

  <!-- Top Navbar -->
  <?php 
    include 'header/header.php';

    if (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin')) {
      ?>
  <!-- Main Content -->
  <main class="p-6 md:w-full lg:w-3/4 mx-auto">
    <!-- Header -->
    <div class="mb-4">
      <h2 class="text-3xl font-bold tracking-tight">Appointments</h2>
      <p class="text-sm text-slate-500">Manage all clinic appointments and scheduling policies</p>
    </div>

    <!-- Admin Info -->
    <div class="border border-blue-300 bg-blue-50 text-blue-700 p-4 rounded mb-4 text-sm">
      <strong>Administrative View</strong><br />
      You have access to all appointment management features and can override scheduling rules.
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2 mb-6">
      <a href="?" class="border px-4 py-2 rounded-lg hover:bg-gray-100 text-sm <?php if (!isset($_GET['viewall']) && !isset($_GET['settings'])) { echo 'bg-blue-500 text-white hover:bg-blue-500'; } ?>">Schedule New</a>
      <a href="?viewall" class="border px-4 py-2 rounded-lg hover:bg-gray-100 text-sm <?php if (isset($_GET['viewall'])) { echo 'bg-blue-500 text-white hover:bg-blue-500'; } ?>">View All</a>
      <a href="?settings" class="border px-4 py-2 rounded-lg hover:bg-gray-100 text-sm <?php if (isset($_GET['settings'])) { echo 'bg-blue-500 text-white hover:bg-blue-500'; } ?>">Settings</a>
    </div>
    <?php 
      if (isset($_GET['viewall'])) {
          include 'admin/listofappointments.php';
      } else if (isset($_GET['settings'])) {
        include 'admin/settings.php';
      } else {
        include 'admin/schedule.php';
      }
    
    ?>
  </main>
      <?php
    } else {
      echo 'PAGE NOT FOUND';
    }
  ?>


</body>
</html>
