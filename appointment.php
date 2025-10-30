<?php 
session_start();
  $appointment = null;
  // Database config
 
  include 'db/db.php';
  $dateofappointment = '';
  $timeofappointment = '';
  $service = '';
  $status = '';
  $price = 0;

  $patientInfo = null;
  if (isset($_SESSION['patient_id'])) {
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE patient_id = ? AND date_selected >= CURDATE() AND (status <> 'COMPLETED' && status <> 'CANCELED')");
    $stmt->bind_param("i", $_SESSION['patient_id']);
    $stmt->execute();
    $resultpatient = $stmt->get_result();
    $appointment = $resultpatient->fetch_assoc();

    $stmt = $conn->prepare('SELECT * FROM patients WHERE id = ?');
    $stmt->bind_param("i", $_SESSION['patient_id']);
    $stmt->execute();
    $resultpatient = $stmt->get_result();
    $patient = $resultpatient->fetch_assoc();

    if ( $patient) {
      $patientInfo = $patient;
    }

    if ($appointment) {
      $dateofappointment = date('l, F j, Y', strtotime($appointment['date_selected']));
      $timeofappointment = date('h:i A', strtotime($appointment['time_selected']));
      $status = $appointment['status'];
      $price = $appointment['price'];
      $appointmentId = $appointment['id'];
      $stmtservice =  $conn->prepare("SELECT * FROM services WHERE id = ?");
      $stmtservice->bind_param("i", $appointment['service_id']);
      $stmtservice->execute();
      $res = $stmtservice->get_result();
      $serviceres = $res->fetch_assoc();
      if ($serviceres) {
        $service = $serviceres['name'];
      }
    }
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book Appointment - NorthLens</title>
  <link href="src/output.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    td.selected button {
      background-color: #2563eb;
      color: white;
      font-weight: bold;
      border-radius: 99px;
    }

    td.clickable:hover {
      background-color: #e0f2fe;
      cursor: pointer;
      border-radius: 9999px;
    }

    td.disabled {
      color: #ccc;
      pointer-events: none;
    }
    .disabled-link {
      pointer-events: none; /* Prevents clicks */
      cursor: default;     /* Changes the cursor back from a pointer */
      color: gray;         /* Visual cue that it's disabled */
    }
  </style><style>
        /* Custom styles for the progress line */
        .progress-bar-container {
            position: relative;
            height: 4px;
            background-color: #e5e7eb; /* Gray-200 */
        }
        .progress-line {
            height: 100%;
            background-color: #3b82f6; /* Blue-600 */
            transition: width 0.5s ease-in-out;
            width: 14%; 
        }
        .step-completed {
            background-color: #3b82f6 !important; /* Blue-600 */
            color: white;
            border-color: #3b82f6 !important;
        }
        .step-active {
            color: #3b82f6; /* Blue-600 */
            border-color: #3b82f6;
            background-color: white;
        }
        .time-slot {
            transition: all 0.1s ease;
        }
        .disabled {
          pointer-events: none; /* Prevents the user from clicking the link */
          cursor: not-allowed;     /* Changes the cursor from a pointer */
          opacity: 0.6;        /* Makes it look visually disabled */
        }
        @media (min-width: 1024px) {
          .lg\:w-3\/4 {
            width: 75%;
          }
          .lg\:w-2\/5 {
            width: 40%;
          }
          .lg\:w-3\/5 {
            width: 60%;
          }
        }
    </style>
</head>
<body class="bg-white min-h-screen">
<?php include 'header/header.php'; ?>
<?php 

    if ($appointment) {
?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('confirm-date').innerHTML = "<?php echo $dateofappointment; ?>";
            document.getElementById('confirm-time').innerHTML = "<?php echo $timeofappointment; ?>";
            document.getElementById('confirm-service').innerHTML = "<?php echo $service; ?>";
            document.getElementById('appointment-status').innerHTML = "<?php echo $status; ?>";
            document.getElementById('appointment-price').innerHTML = "<?php echo number_format($price, 2); ?>";
        });

      </script> 
        <h1 class="text-center font-bold text-2xl mb-4 mt-4">Your Appointment</h1>
        <div class="border p-4 rounded-md space-y-4 lg:w-1/2 md:w-full mx-auto">
            <div class="flex items-start space-x-3">
              <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
              <div>
                <p class="text-sm text-gray-500">Appointment Date</p>
                <p id="confirm-date" class="font-semibold text-black"></p>
              </div>
            </div>
            <div class="flex items-start space-x-3">
              <i class="fas fa-clock text-blue-600 text-xl"></i>
              <div>
                <p class="text-sm text-gray-500">Appointment Time</p>
                <p id="confirm-time" class="font-semibold text-black"></p>
              </div>
            </div>
            <div class="flex items-start space-x-3">
              <i class="fas fa-stethoscope text-blue-600 text-xl"></i>
              <div>
                <p class="text-sm text-gray-500">Service</p>
                <p id="confirm-service" class="font-semibold text-black"></p>
              </div>
            </div>
            <div class="flex items-start space-x-3">
              <i class="fas fa-history text-blue-600 text-xl"></i>
              <div>
                <p class="text-sm text-gray-500">Status</p>
                <p id="appointment-status" class="font-semibold text-black"></p>
              </div>
            </div>
            <div class="flex items-start space-x-3">
              <i class="fas fa-peso-sign ml-2 text-blue-600 text-xl"></i>     
              <div>
                <p class="text-sm text-gray-500">Price</p>
                <p id="appointment-price" class="font-semibold text-black"></p>
              </div>
            </div>

            <?php 
              if ($status == 'PENDING') {
            ?>
              <button type="button" onclick="reschedModal()" class="bg-blue-600 text-white px-4 py-2 rounded">Reschedule</button>
              <button type="button" onclick="openCancelModal()"  class="bg-red-600 text-white px-4 py-2 rounded">Cancel</button>
              <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                  <div class="bg-white w-full max-w-sm p-6 rounded shadow-lg text-center">
                  <h3 class="text-lg font-semibold mb-4 text-red-600">Cancel Appointment?</h3>
                  <p class="mb-4 text-gray-700">Are you sure you want to cancel this appointment? This action cannot be undone.</p>
                  <div class="flex justify-center gap-4">
                      <button onclick="closeCancelModal()" class="px-4 py-2 border rounded text-gray-700">No</button>
                      <a href="crud/updateAppointment.php?patientUpdate&status=CANCELED&id=<?php echo $appointmentId; ?>" class="px-4 py-2 bg-red-600 text-white rounded">Yes</a>
                  </div>
                  </div>
              </div>

                <div id="rescheduleModal" class="p-6 hidden fixed inset-0 bg-black bg-opacity-40 flex mx-auto items-center justify-center z-50 lg:w-full">
                  <div class="bg-white w-1/2 max-w-lg p-6 rounded shadow-lg">
                      <h3 class="text-lg font-semibold mb-4 text-green-600">Resched Appointment?</h3>
                      <form action="crud/rescheduleAppointment.php?patientResched&id=<?php echo $appointmentId; ?>" method="POST">
                          <p class="mb-4 text-gray-700">Select Date.</p>
                          <input required type="date" name="date" id="datePicker" class="w-full datePicker" min="<?= date('Y-m-d'); ?>">
                          <p class="mb-4 text-gray-700 mt-4">Select Time</p>
                          <select id="timeDropdown" name="time" class="border p-2 rounded w-full text-gray-700">
                              <option value="">Select a date first</option>
                          </select>
                          <div class="flex justify-center gap-4 mt-2">
                              <button onclick="closeReschedModal()" type="button" class="px-4 py-2 border rounded text-gray-700">Close</button>
                              <button class="px-4 py-2 bg-blue-600 text-white rounded">Reschedule</button>
                          </div>
                      </form>
                  </div>
              </div>
            <?php
              }
            ?>
        </div>
<?php
    } else {
      if ($patientInfo) {
?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
          document.getElementById('em_firstname').value = "<?php echo $patientInfo['emergency_firstname']; ?>";
          document.getElementById('em_lastname').value = "<?php echo $patientInfo['emergency_lastname']; ?>";
          document.getElementById('em_barangay').value = "<?php echo $patientInfo['emergency_barangay']; ?>";
          document.getElementById('em_city').value = "<?php echo $patientInfo['emergency_city']; ?>";
          document.getElementById('em_relationship').value = "<?php echo $patientInfo['emergency_relationship']; ?>";
          document.getElementById('em_email').value = "<?php echo $patientInfo['emergency_email']; ?>";
          document.getElementById('em_phone').value = "<?php echo $patientInfo['emergency_number']; ?>";
      });

    </script> 
<?php
      }
?>

  <div class="text-center mt-10 mb-6">
    <h2 class="text-2xl font-bold mb-1">Book your Appointment</h2>
    <p class="text-gray-600 max-w-xl mx-auto">
      Schedule your eye care visit with NorthLens Optical Clinic.
    </p>
    <?php 
      if (isset($_GET['error']) && $_GET['error'] == 'no_patient') {
    ?>
      <p class="text-red-500">
          Please register patient first.
      </p>
    <?php 
      }
    ?>
      <?php 
      if (isset($_GET['success'])) {
    ?>
      <p class="text-green-500">
        Successfully confirmed appointment
      </p>
    <?php 
      }
    ?>
  </div>
  <div class="md:w-6xl lg:w-3/4 w=full mx-auto bg-white shadow-md rounded-xl px-2 py-2">
    <div class="w-full mx-auto mt-2 py-6">
      <div id="tabs" class="flex justify-between items-center text-xs sm:text-sm text-gray-400 lg:w-3/4 md:w-full mx-auto">
        <div class="flex flex-col items-center w-1/7 ">
          <button class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-bold mb-1  step-active" id="tabselected1" data-step="1">
            <span id="title1">1</span>
            <span id="check1" class="hidden top-0 right-0 p-1 text-white check-icon">
                  <svg class="w-4 h-4" width="15px" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
          </button>
          <span>Service</span>
        </div>
        <div class="flex flex-col items-center w-1/7">
          <button class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-bold mb-1" id="tabselected2" data-step="2">
            <span id="title2">2</span>
            <span id="check2" class="hidden top-0 right-0 p-1 text-white check-icon">
                  <svg class="w-4 h-4" width="15px" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
          </button>
          <span>Date</span>
        </div>
        <div class="flex flex-col items-center w-1/7">
          <button class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-bold mb-1" id="tabselected3" data-step="3">
            <span id="title3">3</span>
            <span id="check3" class="hidden top-0 right-0 p-1 text-white check-icon">
                  <svg class="w-4 h-4" width="15px" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
          </button>
          <span>Time</span>
        </div>
        <div class="flex flex-col items-center w-1/7">
          <button class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-bold mb-1" id="tabselected4" data-step="4">
            <span id="title4">4</span>
            <span id="check4" class="hidden top-0 right-0 p-1 text-white check-icon">
                  <svg class="w-4 h-4" width="15px" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
          </button>
          <span>Contact</span>
        </div>
        <div class="flex flex-col items-center w-1/7">
          <button class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-bold mb-1" id="tabselected5" data-step="5">
            <span id="title5">5</span>
            <span id="check5" class="hidden top-0 right-0 p-1 text-white check-icon">
                  <svg class="w-4 h-4" width="15px" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
          </button>
          <span>Medical</span>
        </div>
        <div class="flex flex-col items-center w-1/7">
          <button class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-bold mb-1" id="tabselected6" data-step="6">
            <span id="title6">6</span>
            <span id="check6" class="hidden top-0 right-0 p-1 text-white check-icon">
                  <svg class="w-4 h-4" width="15px" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
          </button>
          <span>Eye Health</span>
        </div>
        <div class="flex flex-col items-center w-1/7">
          <button class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-bold mb-1" id="tabselected7" data-step="7">
            <span id="title7">7</span>
            <span id="check7" class="hidden top-0 right-0 p-1 text-white check-icon">
                  <svg class="w-4 h-4" width="15px" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
          </button>
          <span>Confirm</span>
        </div>
      </div>
      <div class="progress-bar-container w-full mt-6">
        <div class="progress-line" id="progressLine"></div>
      </div>
      <form method="POST" action="crud/insertAppointment.php" class="lg:w-3/4 md:w-full mx-auto" id="appointmentForm"  enctype="multipart/form-data">
        <input type="hidden" name="selected_service" id="selected_service">
        <input type="hidden" name="selected_date" id="selectedDateInput">
        <input type="hidden" name="selected_time" id="selectedTimeInput">

        <div class="tab-content mt-6">
          <div class="tab-panel block">
            <h2 class="text-center font-bold text-2xl mb-4">Choose a Service</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <?php 
                $stmt = $conn->prepare("SELECT * FROM services where available = 1");
                $stmt->execute();
                $result = $stmt->get_result();
                $services = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                foreach ($services as $service) {
              ?>
              <div class="border rounded-lg p-4 hover:shadow-md hover:border hover:border-blue-600 cursor-pointer service-item" 
                  data-service-id="<?php echo $service['id']; ?>" 
                  data-service-name="<?php echo $service['name']; ?>"
                  onclick="selectService(this)">
                <h4 class="font-semibold mb-1"><?php echo $service['name']; ?></h4>
                <div class="flex justify-between text-2xl font-semibold text-blue-600">
                  <span>₱ <?php echo number_format($service['price'], 2); ?></span>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>

          <div class="tab-panel hidden">
            <div class="flex justify-center mt-8">

                <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md text-center">
                  <h3 class="text-center font-bold text-2xl mb-4">Select Date</h3>
                  <div class="flex items-center justify-between mb-4">
                    <button type="button" id="prevMonth" class="text-lg">&lt;</button>
                    <span id="monthYear" class="text-base font-medium"></span>
                    <button type="button" id="nextMonth" class="text-lg">&gt;</button>
                  </div>

                  <table class="w-full table-fixed">
                    <thead class="text-gray-500">
                      <tr>
                        <th class="py-1">Sun</th>
                        <th class="py-1">Mon</th>
                        <th class="py-1">Tue</th>
                        <th class="py-1">Wed</th>
                        <th class="py-1">Thu</th>
                        <th class="py-1">Fri</th>
                        <th class="py-1">Sat</th>
                      </tr>
                    </thead>
                    <tbody id="calendar-body" class="text-black"></tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>

          <div class="tab-panel hidden">
            <h2 class="text-center font-bold text-2xl mb-4">Select Time</h2>
            <div class="grid grid-cols-4 gap-4 text-sm max-w-2xl mx-auto" id="timeslots">
              
            </div>
          </div>

          <div class="tab-panel hidden">
            <h2 class="text-center font-bold text-2xl mb-4">Emergency Contact</h2>
              <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium">Name:</label>
                  <div class="grid grid-cols-2 gap-2 mt-1">
                      <input type="text" placeholder="First Name" name="em_firstname" id="em_firstname" class="border px-3 py-2 rounded w-full"/>
                      <input type="text" placeholder="Last Name" name="em_lastname" id="em_lastname" class="border px-3 py-2 rounded w-full"/>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium">Address:</label>
                  <div class="grid grid-cols-2 gap-2 mt-1">
                      <input type="text" placeholder="Barangay/Street" name="em_barangay" id="em_barangay" class="border px-3 py-2 rounded w-full"/>
                      <input type="text" placeholder="City" name="em_city" id="em_city" class="border px-3 py-2 rounded w-full"/>
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                  <label class="block text-sm font-medium">Relationship to Patient:</label>
                  <input type="text" placeholder="Relationship" name="em_relationship"  id="em_relationship" class="mt-1 border px-3 py-2 rounded w-full"/>
                  </div>
                  <div>
                  <label class="block text-sm font-medium">Email:</label>
                  <input type="email" name="em_email"  id="em_email" class="mt-1 border px-3 py-2 rounded w-full"/>
                  </div>
              </div>

              <div class="w-full md:w-1/2">
                  <label class="block text-sm font-medium">Phone Number:</label>
                  <input type="text" placeholder="000-000-000"  id="em_phone" name="em_phone" class="mt-1 border px-3 py-2 rounded w-full"/>
              </div>
          </div>
          
          <div class="tab-panel hidden">
            <h2 class="text-center font-bold text-2xl mb-4">Medical History</h2>
              <div class="grid md:grid-cols-2 gap-6">
      <!-- Left side -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Primary Physician:</label>
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        <input type="text" placeholder="First Name" name="primary_physician_firstname" class="border px-3 py-2 rounded w-full" />
                        <input type="text" placeholder="Last Name" name="primary_physician_lastname" class="border px-3 py-2 rounded w-full" />
                    </div>
                    </div>

                    <div>
                      <label class="block text-sm font-medium">Do you have allergies?</label>
                      <div class="flex gap-4 mt-1">
                          <label class="flex items-center gap-1">
                          <input type="radio" name="allergies_medical" value="1" required class="accent-blue-600" /> Yes
                          </label>
                          <label class="flex items-center gap-1">
                          <input type="radio" name="allergies_medical" value="0" required class="accent-blue-600" /> No
                          </label>
                          
                      </div>
                        <div id="allergMedWarning" class="text-red-600 text-sm mt-1 hidden">
                          Please select Yes or No.
                        </div>
                    </div>

                    <div>
                      <label class="block text-sm font-medium">Current Medications:</label>
                      <input type="text" placeholder="" name="current_medications" class="border px-3 py-2 rounded w-full" />
                    </div>
                    <div>
                      <label class="block text-sm font-medium">Upload Medical History:</label>
                      <input type="file" placeholder=""  name="file_uploaded" class="border px-3 py-2 rounded w-full" />
                    </div>
                </div>

                <!-- Right side -->
                <div>
                    <label class="block text-sm font-medium mb-2">Do you have any of the following conditions?</label>
                    <div class="space-y-2">
                      <label class="block"><input type="checkbox" name="conditions[]" value="glaucoma" class="mr-2 accent-blue-600" /> Glaucoma</label>
                      <label class="block"><input type="checkbox" name="conditions[]" value="cataracts" class="mr-2 accent-blue-600" /> Cataracts</label>
                      <label class="block"><input type="checkbox" name="conditions[]" value="macular_degeneration" class="mr-2 accent-blue-600" /> Macular Degeneration</label>
                      <label class="block"><input type="checkbox" name="conditions[]" value="diabetic_retinopathy" class="mr-2 accent-blue-600" /> Diabetic Retinopathy</label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Others</label>
                        <input type="text" class="w-full border rounded px-3 py-2 resize-none" name="others" />
                    </div>
                    <div>
                      <label class="block text-sm font-medium">Last Visit:</label>
                      <input type="date" placeholder="" name="last_visit" class="border px-3 py-2 rounded w-full" />
                    </div>
                </div>
            </div>
          </div>

          <div class="tab-panel hidden">
            <h2 class="text-center font-bold text-2xl mb-4"> Eye Health History</h2>

              <div class="grid md:grid-cols-2 gap-6">
                <!-- Left side -->
                <div class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium">Do you have allergies?</label>
                      <div class="flex gap-4 mt-1">
                          <label class="flex items-center gap-1">
                          <input type="radio" name="vision_allergies" required value="1" class="accent-blue-600" /> Yes
                          </label>
                          <label class="flex items-center gap-1">
                          <input type="radio" name="vision_allergies" required value="0" class="accent-blue-600" /> No
                          </label>
                      </div>
                      <div id="visionWarning" class="text-red-600 text-sm mt-1 hidden">
                        Please select Yes or No.
                      </div>
                    </div>

                    <div>
                      <label class="block text-sm font-medium">Do you wear contact lenses?</label>
                      <div class="flex gap-4 mt-1">
                          <label class="flex items-center gap-1">
                          <input type="radio" name="contact_lens" required value="1" class="accent-blue-600" /> Yes
                          </label>
                          <label class="flex items-center gap-1">
                          <input type="radio" name="contact_lens" required value="0" class="accent-blue-600" /> No
                          </label>
                      </div>
                      <div id="contactLens" class="text-red-600 text-sm mt-1 hidden">
                        Please select Yes or No.
                      </div>
                    </div>

                    <div>
                      <label class="block text-sm font-medium">Do you have any problems?</label>
                      <div class="flex gap-4 mt-1">
                          <label class="flex items-center gap-1">
                          <input type="radio" name="problems" required value="1" class="accent-blue-600" /> Yes
                          </label>
                          <label class="flex items-center gap-1">
                          <input type="radio" name="problems" required value="0" class="accent-blue-600" /> No
                          </label>
                      </div>
                      <div id="problemWarning" class="text-red-600 text-sm mt-1 hidden">
                        Please select Yes or No.
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium">Do you have existing eye glasses?</label>
                      <div class="flex gap-4 mt-1">
                          <label class="flex items-center gap-1">
                          <input type="radio" name="aye_glasses" required value="1" class="accent-blue-600" /> Yes
                          </label>
                          <label class="flex items-center gap-1">
                          <input type="radio" name="aye_glasses" required value="0" class="accent-blue-600" /> No
                          </label>
                      </div>
                      <div id="visionWarning" class="text-red-600 text-sm mt-1 hidden">
                        Please select Yes or No.
                      </div>
                    </div>
                </div>

                <!-- Right side -->
                <div>
                    <div>
                      <label class="block text-sm font-medium mb-1">Previous Prescription</label>
                      <textarea class="w-full border rounded px-3 py-2 h-28 resize-none" name="prev_perscription"></textarea>
                    </div>
                </div>
            </div>
          </div>

          <div class="tab-panel hidden">
            <h2 class="text-center font-bold text-2xl mb-4">Confirm your appointment</h2>
            <div class="border p-4 rounded-md space-y-4">
              <div class="flex items-start space-x-3">
                <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                <div>
                  <p class="text-sm text-gray-500">Appointment Date</p>
                  <p id="confirm-date" class="font-semibold text-black"></p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <i class="fas fa-clock text-blue-600 text-xl"></i>
                <div>
                  <p class="text-sm text-gray-500">Appointment Time</p>
                  <p id="confirm-time" class="font-semibold text-black"></p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <i class="fas fa-stethoscope text-blue-600 text-xl"></i>
                <div>
                  <p class="text-sm text-gray-500">Service</p>
                  <p id="confirm-service" class="font-semibold text-black"></p>
                </div>
              </div>
            </div>
        </div>

        <div class="flex justify-between mt-6">
          <button type="button" onclick="prevStep()" class="text-gray-600 hover:text-black px-4 py-2 rounded-md border"><i class="fa-sharp-duotone fa-thin fa-less-than"></i> Back</button>
          <button type="button" onclick="nextStep()" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
            <span id="continueButton">
              Continue <i class="fa-sharp-duotone fa-thin fa-greater-than"></i>
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
<?php
    }
?>

    <div class="lg:w-3/4 mx-auto bg-white shadow-md rounded-xl px-2 py-2 mt-4">
      <div class="w-full mt-2">
        <h1 class="text-center font-semibold text-2xl border-b-2 border-black bg-gray-200   w-full p-2 ">Appointment History</h1>
        <table border="1" class="w-full" cellpadding="8">
          <thead>
            <tr class="bg-gray-200">
              <th>Service</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
              <th>Your Rating</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              $stmt = $conn->prepare("SELECT appointments.*, services.name FROM appointments inner join services on services.id = appointments.service_id WHERE patient_id = ? AND (status = 'COMPLETED' OR status = 'CANCELED') AND date_selected >= CURDATE()");
              $stmt->bind_param("i", $_SESSION['patient_id']);
              $stmt->execute();
              $resultpatient = $stmt->get_result();
              $appointments = $resultpatient->fetch_all(MYSQLI_ASSOC);

              foreach ($appointments as $appointment) {
            ?> 
                <tr data-id="<?= $appointment['id'] ?>">
                  <td class="text-center"><?php echo $appointment['name'] ?></td>
                  <td class="text-center"><?php echo $appointment['date_selected'] ?></td>
                  <td class="text-center"><?php echo $appointment['time_selected'] ?></td>
                  <td class="text-center"><?php echo $appointment['status'] ?></td>
                  <td class="text-center cursor-pointer">
                    <?php if ($appointment['status'] != 'CANCELED') {
                    ?>
                      <?php if (($appointment['rating'] != null)): ?>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa fa-star <?= $i <= $appointment['rating'] ? 'text-red-500' : '' ?>"></i>
                        <?php endfor; ?>
                      <?php else: ?>
                          <?php for ($i = 1; $i <= 5; $i++): ?>
                              <i class="fa fa-star star <?= $i <= $appointment['rating'] ? 'text-red-500' : '' ?>" data-value="<?= $i ?>"></i>
                          <?php endfor; ?>
                      <?php endif; ?>
                    <?PHP
                    } ?>
                   
                  </td>
                </tr>
            <?php
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
<script>

 document.querySelectorAll('tr[data-id]').forEach(row => {
  const stars = row.querySelectorAll('.star');
  if (!stars.length) return; // skip rows already rated

  stars.forEach(star => {
    star.addEventListener('mouseover', () => {
      stars.forEach(s => s.classList.toggle('hover', s.dataset.value <= star.dataset.value));
    });

    star.addEventListener('mouseout', () =>
      stars.forEach(s => s.classList.remove('hover'))
    );

    star.addEventListener('click', () => {
      const rating = star.dataset.value;
      stars.forEach(s =>
        s.classList.toggle('selected', s.dataset.value <= rating)
      );

      let confirmRating = confirm("Are you sure to rate this " + rating + "? ")
      if (confirmRating) {
        fetch('crud/rateAppointment.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `appointment_id=${encodeURIComponent(row.dataset.id)}&rating=${encodeURIComponent(rating)}`
        })
        .then(r => r.ok ? location.reload() : console.error('save failed'))
        .catch(err => console.error('Error saving rating:', err));
      }
      });
  });
});

let currentStep = 0;
let selectedServiceName = "";

function showStep(index) {
  document.querySelectorAll('.tab-panel').forEach((el, i) => {
    el.classList.toggle('hidden', i !== index);
  });
}

function nextStep() {

  if (currentStep === 0 && !document.getElementById('selected_service').value) {
    alert('Please select a service');
    return;
  }
  
  if (currentStep >= 0) {
    const selector = 'nav.nav-links a';
    const allLinksInDiv = document.querySelectorAll(selector);
    allLinksInDiv.forEach(link => {
      link.classList.add("disabled")
      link.classList.add("cursor-not-allowed")
    });
  }
  if (currentStep === 1 && !document.getElementById('selectedDateInput').value) {
    alert('Please select a date');
    return;
  }
  if (currentStep === 4) {
    const warning = document.getElementById("allergMedWarning");
    const selected = document.querySelector('input[name="allergies_medical"]:checked');
    if (!selected) {
      warning.classList.remove("hidden"); 
      return;
    } else {
      warning.classList.add("hidden");
    }
  }

  if (currentStep === 5) {
    const warning = document.getElementById("visionWarning");
    const selected = document.querySelector('input[name="vision_allergies"]:checked');
    if (!selected) {
      warning.classList.remove("hidden"); 
      return;
    } else {
      warning.classList.add("hidden");
    }

    const warningCL = document.getElementById("contactLens");
    const selectedCL = document.querySelector('input[name="contact_lens"]:checked');
    if (!selectedCL) {
      warningCL.classList.remove("hidden"); 
      return;
    } else {
      warningCL.classList.add("hidden");
    }

    const warningProb = document.getElementById("problemWarning");
    const selectedProb = document.querySelector('input[name="problems"]:checked');
    if (!selectedProb) {
      warningProb.classList.remove("hidden"); 
      return;
    } else {
      warningProb.classList.add("hidden");
    }
  }

  if (currentStep === 2 && !document.getElementById('selectedTimeInput').value) {
    alert('Please select a time');
    return;
  }

  document.querySelectorAll(".tab-btn").forEach(el => {
    el.classList.remove("bg-blue-500");
    el.classList.remove("text-white");
    el.classList.remove("step-active");
  });

  const box = document.getElementById("tabselected" + (currentStep + 2));
  if (box) {
    box.classList.add("step-active");
  }

  if (currentStep == 5) {
    document.getElementById("continueButton").innerHTML = "Confirm <i class='fa-solid fa-check'></i>"
  }

  if (currentStep <= 6) currentStep++;
  if (currentStep === 3) updateConfirmation();
  if (currentStep == 7 ) {
    let confirmSubmit = confirm("Are you sure to save this appointment ?")

    if (confirmSubmit) {
      const form = document.getElementById('appointmentForm');
      form.submit();

    }
  } else {
    if (document.getElementById('selectedDateInput').value != '') {
      loadAvailableTimes(document.getElementById('selectedDateInput').value)
    }

    showStep(currentStep);
  }


  var ctr = 0
  const boost = 14;
  const baseProgress = (currentStep / 7) * 100;
  let progressWidth = baseProgress + boost;
  progressWidth = Math.min(progressWidth, 100);
  const progressLine = document.getElementById('progressLine');
  if (progressLine) {
      progressLine.style.width = progressWidth.toFixed(2) + '%';
  }
  while(ctr < currentStep) {
    const box = document.getElementById("tabselected" + (ctr + 1));
    const checkspan = document.getElementById("check" + (ctr + 1));
    const titlespan = document.getElementById("title" + (ctr + 1));
    if (box) {
      box.classList.add("bg-blue-500");
      box.classList.add("text-white");
      box.classList.add("border-blue-600");
      box.classList.remove("step-active");
      checkspan.classList.remove("hidden");
      titlespan.classList.add("hidden");
    }
    ctr ++;
  }

}

function prevStep() {

  const prevbox = document.getElementById("tabselected" + (currentStep + 1));
  if (prevbox) {
    prevbox.classList.remove("step-active");
  }

  const box = document.getElementById("tabselected" + (currentStep));
  const checkspan = document.getElementById("check" + (currentStep));
  const titlespan = document.getElementById("title" + (currentStep));
  box.classList.add("step-active");
  box.classList.remove("bg-blue-500");
  box.classList.remove("text-white");
  box.classList.remove("border-blue-600");
  checkspan.classList.add("hidden");
  titlespan.classList.remove("hidden");
  
  if (currentStep < 7) {
    document.getElementById("continueButton").innerHTML = "Continue <i class='fa-sharp-duotone fa-thin fa-greater-than'></i>"
  }
  if (currentStep == 1) {
    const selector = 'nav.nav-links a';
    const allLinksInDiv = document.querySelectorAll(selector);
    allLinksInDiv.forEach(link => {
      link.classList.remove("disabled")
      link.classList.remove("cursor-not-allowed")
    });
  }
  var ctr = 0
  const boost = 14;
  const baseProgress = ((currentStep - 1) / 7) * 100;
  let progressWidth = baseProgress + boost;
  progressWidth = Math.min(progressWidth, 100);
  const progressLine = document.getElementById('progressLine');
  if (progressLine) {
      progressLine.style.width = progressWidth.toFixed(2) + '%';
  }
  if (currentStep > 0) currentStep--;
  showStep(currentStep);
}

function selectService(el) {
  document.querySelectorAll('.service-item').forEach(item => item.classList.remove('border-black', 'border-2'));
  el.classList.add('border-black', 'border-2');
  document.getElementById('selected_service').value = el.getAttribute('data-service-id');
  selectedServiceName = el.getAttribute('data-service-name');
}

// Time selection
document.addEventListener('DOMContentLoaded', () => {
  const timeButtons = document.querySelectorAll('.time-slot');
  timeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.time-slot').forEach(b => {
        b.classList.remove('bg-blue-500', 'text-white')
        b.classList.add('text-emerald-600')
      });
      btn.classList.add('bg-blue-500', 'text-white');
      btn.target.classList.remove('text-emerald-600');
      document.getElementById('selectedTimeInput').value = btn.getAttribute('data-time');
    });
  });
});

  const btnTs = document.getElementById('timeslots')
  if (btnTs) {
    btnTs.addEventListener('click', function(e) {
      if (e.target && e.target.classList.contains('time-slot')) {
        // Remove styles from all buttons
        document.querySelectorAll('.time-slot').forEach(b  => {
        b.classList.remove('bg-blue-500', 'text-white')
        b.classList.add('text-emerald-600')
      });

        // Add style to selected one
        e.target.classList.add('bg-blue-500', 'text-white');
        e.target.classList.remove('text-emerald-600');

        // Update hidden input
        document.getElementById('selectedTimeInput').value = e.target.getAttribute('data-time');
      }
    });
  }
// Calendar rendering script here (same as before)
function updateConfirmation() {
  document.getElementById('confirm-service').textContent = selectedServiceName;
  document.getElementById('confirm-date').textContent = document.getElementById('selectedDateInput').value;
  document.getElementById('confirm-time').textContent = document.getElementById('selectedTimeInput').value;
}
 const calendarBody = document.getElementById('calendar-body');
  const monthYear = document.getElementById('monthYear');
  const prevMonth = document.getElementById('prevMonth');
  const nextMonth = document.getElementById('nextMonth');

  const today = new Date();
  let currentMonth = today.getMonth();
  let currentYear = today.getFullYear();

  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  function renderCalendar(month, year) {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    if (calendarBody) {
    calendarBody.innerHTML = '';
    monthYear.textContent = `${monthNames[month]} ${year}`;

    let date = 1;
    for (let i = 0; i < 6; i++) {
      const row = document.createElement('tr');

      for (let j = 0; j < 7; j++) {
        const cell = document.createElement('td');
        cell.classList.add('py-2');

        if (i === 0 && j < firstDay) {
          cell.innerHTML = '';
        } else if (date > daysInMonth) {
          break;
        } else {
          const cellDate = new Date(year, month, date);
          const isPast = cellDate < new Date(today.getFullYear(), today.getMonth(), today.getDate());

          if (isPast) {
            cell.innerHTML = `<span class="text-gray-400 cursor-not-allowed">${date}</span>`;
          } else {
            cell.innerHTML = `<button type="button" id="date-${date}-${month}-${year}" class="text-blue-600 hover:bg-blue-100 rounded-full w-8 h-8">${date}</button>`;
            cell.querySelector('button').addEventListener('click', () => {
              document.getElementById('selectedDateInput').value = cellDate.toDateString();
              document.querySelectorAll('td').forEach(td => td.classList.remove('selected'));
              cell.classList.add('selected');
            });
          }

          date++;
        }

        row.appendChild(cell);
      }

      calendarBody.appendChild(row);
    }

    // Disable navigating to past months
    const isCurrentMonth = month === today.getMonth() && year === today.getFullYear();
    prevMonth.disabled = isCurrentMonth;
    prevMonth.classList.toggle('text-gray-400', isCurrentMonth);
    prevMonth.classList.toggle('cursor-not-allowed', isCurrentMonth);
    }
  }

  if (prevMonth) {
    prevMonth.addEventListener('click', () => {
      if (currentMonth === today.getMonth() && currentYear === today.getFullYear()) return;
      currentMonth--;
      if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
      }
      renderCalendar(currentMonth, currentYear);
    });
  }

  if (nextMonth) {
    nextMonth.addEventListener('click', () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      renderCalendar(currentMonth, currentYear);
    });
  }

  renderCalendar(currentMonth, currentYear);

 function loadAvailableTimes() {
    selectedDate = document.getElementById('selectedDateInput').value

    fetch('crud/timeslots.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `selected_date=${encodeURIComponent(selectedDate)}`
    })
    .then(response => response.text())
    .then(html => {
      document.getElementById('timeslots').innerHTML = html;
    });
  }

  
  function openCancelModal() {
    document.getElementById("cancelModal").classList.remove("hidden");
  }

  function closeCancelModal() {
    document.getElementById("cancelModal").classList.add("hidden");
  }

  function reschedModal() {
    document.getElementById("rescheduleModal").classList.remove("hidden");
  }
  
  function closeReschedModal() {
    document.getElementById("rescheduleModal").classList.add("hidden");
  }
  
      document.querySelectorAll('.datePicker').forEach(function(picker) {
          picker.addEventListener('change', function() {
              const selectedDate = this.value;
              fetch('crud/get-available-times.php?date=' + selectedDate)
                  .then(response => response.json())
                  .then(times => {
                    console.log(111)
                      const dropdown = document.getElementById('timeDropdown');
                      dropdown.innerHTML = ''; // clear previous
                      if (times.length === 0) {
                          dropdown.innerHTML = '<option disabled>No available time slots</option>';
                          return;
                      }

                      times.forEach(time => {
                          const option = document.createElement('option');
                          option.value = time;
                          option.textContent = time;
                          dropdown.appendChild(option);
                      });
                  })
                  .catch(err => {
                      console.error('Error fetching times:', err);
                  });
          });
      });
</script>
</body>
</html>
