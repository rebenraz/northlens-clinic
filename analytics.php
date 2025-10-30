<?php

  session_start();
  
  if (isset($_SESSION['user_id']) && !(isset($_SESSION['role']) && $_SESSION['role'] == 'admin')) {
      return;
  }
?>

<!DOCTYPE html>
<html lang="en" class="bg-white text-gray-900">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Analytics</title>
  <link href="src/output.css" rel="stylesheet">
  <script src="js/chart.umd.js"></script>
  <script src="js/chartjs-plugin-datalabels.js"></script>

</head>
<body class="font-sans bg-slate-50">

  <?php include 'header/header.php' ?>
    <?php
        $todaysAppointmentCount = 0;
        $completedtoday = 0;
        $totalpatient = 0;
        $tcompleted = 0;
        $earnings = 0;
        $todaysAppointments = [];
        
        include 'db/db.php';

        $stmt = $conn->prepare("SELECT * FROM patients");
        $stmt->execute();
        $result = $stmt->get_result();
        $totalpatient = $result->num_rows;

        $stmt = $conn->prepare("SELECT * FROM appointments WHERE date_selected = CURDATE() AND status = 'COMPLETED'");      
        $stmt->execute();
        $result = $stmt->get_result();
        $completedtoday = $result->num_rows;

        $stmt = $conn->prepare("SELECT * FROM appointments");      
        $stmt->execute();
        $result = $stmt->get_result();
        $totalAppointment = $result->num_rows;
        
        $stmt = $conn->prepare("SELECT SUM(price) AS total_price FROM appointments WHERE status = 'COMPLETED'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $earnings = $row['total_price'] ?? 0;

        $stmt = $conn->prepare("SELECT * FROM appointments WHERE status = 'COMPLETED'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalCompleteedAppointment = $result->num_rows;

        $stmt = $conn->prepare("SELECT * FROM appointments WHERE status = 'CANCELED'");      
        $stmt->execute();
        $result = $stmt->get_result();
        $totalCcancelAppointment = $result->num_rows;
        $completePercentage = 0;
        $cancelPercentage = 0;
        if ($totalAppointment != 0) {
          $completePercentage = ($totalCompleteedAppointment / $totalAppointment) * 100;
          $cancelPercentage = $totalCcancelAppointment / $totalAppointment * 100;
        }

        $stmt = $conn->prepare("SELECT patients.firstname, patients.lastname, appointments.date_selected as date,time_selected as time, services.name as service_name, appointments.status FROM appointments inner join patients on appointments.patient_id=patients.id inner join services on appointments.service_id=services.id WHERE date_selected = CURDATE() order by time_selected asc");      
        $stmt->execute();
        $result = $stmt->get_result();
        $todaysAppointments = $result->fetch_all(MYSQLI_ASSOC);
        $todaysAppointmentCount = $result->num_rows;
    ?>
    <div class=" md:w-full lg:w-3/4 mx-auto">
        <!-- Page Title -->
      <div class="mb-4 p-2 mt-2">
        <h2 class="text-3xl font-bold tracking-tight">Analytics</h2>
        <p class="text-sm text-slate-500">Clinic performance metrics and insights</p>
      </div>

      <!-- KPI Boxes -->
      <div class="grid grid-cols-4 md:grid-cols-4 gap-3 mb-6">
        <div class="rounded-lg border text-card-foreground shadow-sm group relative overflow-hidden transition-all duration-300 hover:shadow-md bg-white">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="space-y-2">
                <p class="text-sm font-medium text-slate-500">Today's Appointments</p>
                <p class="text-3xl font-bold tracking-tight">
                  <?php echo $todaysAppointmentCount ?>
                </p>
              </div>
              <div class="rounded-xl p-3 bg-blue-500 transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-6 w-6 text-blue-100"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
              </div>
            </div>
          </div>
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-600 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
        </div>

        <div class="rounded-lg border text-card-foreground text-white shadow-sm group relative overflow-hidden transition-all duration-300 hover:shadow-md bg-emerald-600">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="space-y-2">
                <p class="text-sm font-medium text-white">Completion Rate</p>
                <p class="text-3xl font-bold tracking-tight">
                  <?php echo number_format($completePercentage) ?>%
                </p>
              </div>
              <div class="rounded-xl p-3 bg-emerald-500 transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-6 w-6 text-success-foreground"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>              </div>
            </div>
          </div>
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-700 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
        </div>

        <div class="rounded-lg border text-card-foreground text-white shadow-sm group relative overflow-hidden transition-all duration-300 hover:shadow-md bg-red-600">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="space-y-2">
                <p class="text-sm font-medium text-white">Cancellation Rate</p>
                <p class="text-3xl font-bold tracking-tight">
                  <?php echo number_format($cancelPercentage) ?>%
                </p>
              </div>
              <div class="rounded-xl p-3 bg-red-500 transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-down h-6 w-6 text-destructive-foreground"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline><polyline points="16 17 22 17 22 11"></polyline></svg>              </div>
            </div>
          </div>
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-red-700 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
        </div>

        <div class="rounded-lg border text-card-foreground text-white shadow-sm group relative overflow-hidden transition-all duration-300 hover:shadow-md bg-blue-600">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="space-y-2">
                <p class="text-sm font-medium text-white">Earnings</p>
                <p class="text-3xl font-bold tracking-tight">
                  <?php echo number_format($earnings, 2) ?>
                </p>
              </div>
              <div class="rounded-xl p-3 bg-blue-500 transition-transform duration-300 group-hover:scale-110">
                <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.6 16.733c.234.269.548.456.895.534a1.4 1.4 0 0 0 1.75-.762c.172-.615-.446-1.287-1.242-1.481-.796-.194-1.41-.861-1.241-1.481a1.4 1.4 0 0 1 1.75-.762c.343.077.654.26.888.524m-1.358 4.017v.617m0-5.939v.725M4 15v4m3-6v6M6 8.5 10.5 5 14 7.5 18 4m0 0h-3.5M18 4v3m2 8a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z"/>
                </svg>
              </div>
            </div>
          </div>
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-700 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
        </div>
      </div>

      <!-- Analytics Section -->
      <div class=" rounded-lg ">
        <div class="bg-white w-full p-4 mb-2 border rounded-lg">
          <h3 class="text-lg font-semibold mb-1">Appointment Analytics</h3>
          <p class="text-sm text-gray-500 mb-4">Appointment statistics and trends</p>

          <!-- Tabs -->
          <div class="flex space-x-2 mb-4 text-sm">
            <a href="?" class="<?php echo !isset($_GET['status']) && !isset($_GET['purpose']) && !isset($_GET['report']) ? 'bg-blue-500 text-white' : 'bg-blue-100'; ?> px-2 py-2 rounded">Appointments by Day</a>

            <a href="?status" class="<?php echo isset($_GET['status']) ? 'bg-blue-500 text-white' : 'bg-blue-100'; ?> px-2 py-2 rounded">Status Distribution</a>

            <a href="?purpose" class="<?php echo isset($_GET['purpose']) ? 'bg-blue-500 text-white' : 'bg-blue-100'; ?> px-2 py-2 rounded">Appointment Purpose</a>

            <a href="?report" class="<?php echo isset($_GET['report']) ? 'bg-blue-500 text-white' : 'bg-blue-100'; ?> px-2 py-2 rounded">Report</a>

          </div>
        </div>
        <div class="p-4 bg-white border rounded-lg">
          <?php 
            if (!isset($_GET['status']) && !isset($_GET['purpose']) && !isset($_GET['report'])) {
              include 'analytics/appointmentbyday.php';
            } else if ( isset($_GET['status'])) {
              include 'analytics/appointmentbystatus.php';
            } else if ( isset($_GET['purpose'])) {
              include 'analytics/appointmentpurpose.php';
            } else if ( isset($_GET['report'])) {
              include 'analytics/report.php';
            } 

          ?>

        </div>
      </div>
    </div>
  

</body>
</html>
