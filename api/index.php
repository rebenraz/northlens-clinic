
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
/>
  <script src="https://cdn.tailwindcss.com"></script>

  <title>Northlens</title>
  <link href="src/output.css" rel="stylesheet">
</head>
<body class="text-gray-800 bg-slate-50">

  <!-- Top Navbar -->
  <?php include 'header/header.php'; ?>

  <?php
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
      $todaysAppointmentCount = 0;
      $completedtoday = 0;
      $totalpatient = 0;
      $tcompleted = 0;
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

      
      $stmt = $conn->prepare("SELECT patients.firstname, patients.lastname, appointments.date_selected as date,time_selected as time, services.name as service_name, appointments.status FROM appointments inner join patients on appointments.patient_id=patients.id inner join services on appointments.service_id=services.id WHERE date_selected = CURDATE() order by time_selected asc");      
      $stmt->execute();
      $result = $stmt->get_result();
      $todaysAppointments = $result->fetch_all(MYSQLI_ASSOC);
      $todaysAppointmentCount = $result->num_rows;
  ?>
    
     <!-- Dashboard Content -->
    <main class="p-6 md:w-full lg:w-3/4 mx-auto">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="text-3xl font-bold tracking-tight text-foreground">Dashboard</h1>
        <p class="text-sm text-slate-500">
          <?php echo date('l, F j, Y'); ?>
        </p>  </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-2 mb-6">
        <a href="admin-appointment.php" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
          Schedule Appointment</a>
        <a href="registration.php" class="border border-gray-400 text-gray-800 px-8 py-2 rounded-lg hover:bg-blue-700 hover:text-white bg-white flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-4 w-4"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>  
          Add New Patient</a>
      </div>

      <!-- Info Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
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

        <div class="rounded-lg border text-card-foreground shadow-sm group relative overflow-hidden transition-all duration-300 hover:shadow-md bg-white">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="space-y-2">
                <p class="text-sm font-medium text-slate-500">Completed Today</p>
                <p class="text-3xl font-bold tracking-tight">
                  <?php echo $completedtoday ?></p>
              </div>
              <div class="rounded-xl p-3 bg-blue-500 transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-6 w-6 text-blue-100"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
              </div>
            </div>
          </div>
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-600 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
        </div>

        
        <div class="rounded-lg border text-card-foreground shadow-sm group relative overflow-hidden transition-all duration-300 hover:shadow-md bg-white">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="space-y-2">
                <p class="text-sm font-medium text-slate-500">Total Patients</p>
                <p class="text-3xl font-bold tracking-tight">
                  <?php echo $totalpatient ?></p>
              </div>
              <div class="rounded-xl p-3 bg-blue-500 transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-6 w-6 text-blue-100"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
              </div>
            </div>
          </div>
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-600  opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
        </div>
      </div>

      <!-- Appointment List -->
      <div class="border rounded-lg p-4">
        <h3 class="font-semibold mb-2 text-xl font-semibold">Appointments for <?php echo date('l, F j, Y'); ?></h3>
        <p class="text-sm text-gray-600 mb-4">Showing <?php echo $todaysAppointmentCount; ?> appointments</p>
        <?php
          foreach($todaysAppointments as $todaysAppointment) {
        ?>
          <!-- Appointment Card 1 -->
          <div class="border rounded p-4 mb-4 flex justify-between items-center">
            <div>
              <p class="font-medium"><?php echo $todaysAppointment['firstname'] . ' '. $todaysAppointment['lastname'] ?></p>
              <p class="text-sm text-gray-600"><?php echo date('l, F j, Y', strtotime($todaysAppointment['date'])) . ' (' .  date('h:i A', strtotime($todaysAppointment['time'])) . ')' ; ?></p>
              <p class="text-sm"><?php echo strtoupper($todaysAppointment['service_name']) ?></p>
            </div>
          <?php
            $statusColors = [
                'COMPLETED' => 'green-600',
                'CANCELED'  => 'red-600',
                'PENDING'   => 'gray-600',
                'APPROVED'  => 'blue-600',
            ];

            $status = strtoupper($todaysAppointment['status']);
            $color = $statusColors[$status] ?? 'gray-400'; // fallback if status not in array
            ?>

            <span class="bg-<?= $color ?> text-white text-xs font-semibold px-3 py-1 rounded-full">
                <?= $status ?>
            </span>
          </div>
        <?php
          }
        ?>

      </div>
    </main>
  <?php
    } else {
  ?>
    
    <!-- Hero Section -->
    <section class="max-w-6xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
      <!-- Left Content -->
      <div>
        <span class="inline-block bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full mb-4 font-semibold">
          ● Modern Eye Care Solutions
        </span>
        <h2 class="text-4xl font-bold leading-tight">
          What You See <br />
          <span class="text-blue-600">Matters!</span>
        </h2>
        <p class="text-gray-600 mt-4">
          Book your eye care appointments online with NorthLens Optical Clinic.
          Professional services, modern facilities, and a seamless booking experience.
        </p>

        <div class="mt-6 flex gap-4">
            <?php 
          if (isset($_SESSION['user_id'])) {
        ?>
          <a href="appointment.php" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 text-sm">
            Book Appointment
          </a>
        <?php
          } else { 
          ?>
          <a href="login.php" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 text-sm">
            Book Appointment
          </a>
          
          <?php } ?>
          <a href="services.php" class="border border-blue-600 text-blue-600 px-5 py-2 rounded hover:bg-blue-50 text-sm">
            Our Services
          </a>
        </div>

        <div class="mt-8 flex gap-10 text-sm">
          <?php
            include 'db/db.php';
             $stmtapp = $conn->prepare('SELECT * FROM appointments WHERE rating IS NOT NULL');
              $stmtapp->execute();
              $ress = $stmtapp->get_result();
              $ratings = $ress->fetch_all(MYSQLI_ASSOC);
              $totalrating = $ress->num_rows;

              $sumrating = 0;

              foreach ($ratings as $rating) {
                  $sumrating += (int)$rating['rating'];
              }

              $averagerating = $totalrating > 0 ? $sumrating / $totalrating : 0;

      ?>
          <div>
            <p class="font-bold text-lg"><?php echo $totalrating; ?>+</p>
            <p class="text-gray-500">Happy Patients</p>
          </div>

          <div>
            <p class="font-bold text-blue-600">
              <?= number_format($averagerating, 2) ?>/5
            </p>
            <p class="text-yellow-400 text-xl">
              <?php
                $fullStars = floor($averagerating);           // whole stars
                $hasHalf   = ($averagerating - $fullStars) >= 0.5; // half star if needed
                $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);

                // full stars
                for ($i = 0; $i < $fullStars; $i++) {
                    echo '<i class="fa  fa-star"></i>';
                }
                // half star (optional)
                if ($hasHalf) {
                    echo '<i class="fa fa-star-half-alt"></i>';
                }
                // empty stars
                for ($i = 0; $i < $emptyStars; $i++) {
                    echo '<i class="fa  fa-star-o"></i>';
                }
              ?>
            </p>
          </div>
        </div>
      </div>
      <?php 
        $db = "dbnorthlens"; // your DB name
        $user = "root";    // your DB user
        $pass = "";        // your DB password

        // Create DB connection
        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $today = date('m');
        $stmtapp = $conn->prepare('SELECT * FROM appointments WHERE month(date_selected) = ?');
        $stmtapp->bind_param('s', $today);
        $stmtapp->execute();
        $result = $stmtapp->get_result();

        $countMonthAppointment = $result->num_rows;
      ?>
      <!-- Right Image -->
      <div class="relative">
        <img src="images/northlens-dashboard.png" alt="Eyeglasses" class="w-full rounded-lg shadow-md" />
        
        <!-- Top Right Tag -->
         <!-- hereeee -->
        <div class="absolute top-2 right-2 bg-blue-100 text-gray-800 text-xs px-3 py-1 rounded-full shadow-sm font-semibold">
          This month: <span class="font-bold"><?php echo  $countMonthAppointment; ?> Appointments</span>
        </div>

        <!-- Bottom Left Tag -->
        <!-- <div class="absolute bottom-2 left-2 bg-blue-100 text-gray-800 text-xs px-3 py-1 rounded-full shadow-sm font-semibold">
          Average wait time: <span class="font-bold">Just 15 minutes</span>
        </div> -->
      </div>
    </section>
  <?php
    }
  ?>
  

</body>
</html>
