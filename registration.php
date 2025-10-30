<?php
    
    include 'db/db.php';
    session_start();

    $firstname = $lastname = $barangay = $city = $dob = $phone = $gender = $email = "";
    $em_firstname = $em_lastname = $em_barangay = $em_city = $em_relationship = $em_email = $em_phone = "";
    $primary_physician_firstname = $primary_physician_lastname = $allergies_medical = $current_medications = "";
    $conditions = [];
    $vision_allergies = $contact_lens = $problems = $reason_for_visit = $appointment_date = "";
    if (isset($_SESSION['patient_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'patient') {
        $stmtpatient = $conn->prepare("SELECT * FROM patients WHERE id = ?");
        $stmtpatient->bind_param("i", $_SESSION['patient_id']);
        $stmtpatient->execute();
        $resultpatient = $stmtpatient->get_result();
        $patient = $resultpatient->fetch_assoc();

        if ($patient) {
            $firstname = $patient['firstname'];
            $lastname = $patient['lastname'];
            $barangay = $patient['barangay'];
            $city = $patient['city'];
            $dob = $patient['date_of_birth'];
            $phone = $patient['phone_number'];
            $gender = $patient['gender'];
            $email = $patient['email'];
            $em_firstname = $patient['emergency_firstname'];
            $em_lastname = $patient['emergency_lastname'];
            $em_barangay = $patient['emergency_barangay'];
            $em_city = $patient['emergency_city'];
            $em_relationship = $patient['emergency_relationship'];
            $em_email = $patient['emergency_email'];
            $em_phone = $patient['emergency_number'];

            $stmtmh = $conn->prepare("SELECT * FROM medical_histories WHERE patient_id = ?");
            $stmtmh->bind_param("i", $_SESSION['patient_id']);
            $stmtmh->execute();
            $res = $stmtmh->get_result();
            $mh = $res->fetch_assoc();

            if ($mh) {
              
                $primary_physician_firstname = $mh['physician_firstname'];
                $primary_physician_lastname = $mh['physician_lastname'];
                $allergies_medical = $mh['have_allergies'];
                $current_medications = $mh['current_medications'];
                $conditions = explode(',', $mh['conditions']);
                $vision_allergies = $mh['health_allergies'];
                $contact_lens = $mh['contact_lenses'];
                $problems = $mh['any_problems'];
                $reason_for_visit = $mh['reason_for_visits'];
                $appointment_date = $mh['date'];
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Northlens Registration</title>
  <link href="src/output.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Pre-fill inputs with PHP values
            document.querySelector('input[name="firstname"]').value = "<?php echo $firstname; ?>";
            document.querySelector('input[name="lastname"]').value = "<?php echo $lastname; ?>";
            document.querySelector('input[name="barangay"]').value = "<?php echo $barangay; ?>";
            document.querySelector('input[name="city"]').value = "<?php echo $city; ?>";
            document.querySelector('input[name="dob"]').value = "<?php echo $dob; ?>";
            document.querySelector('input[name="phone"]').value = "<?php echo $phone; ?>";
            document.querySelector('input[name="email"]').value = "<?php echo $email; ?>";

            document.querySelector('input[name="em_firstname"]').value = "<?php echo $em_firstname; ?>";
            document.querySelector('input[name="em_lastname"]').value = "<?php echo $em_lastname; ?>";
            document.querySelector('input[name="em_barangay"]').value = "<?php echo $em_barangay; ?>";
            document.querySelector('input[name="em_city"]').value = "<?php echo $em_city; ?>";
            document.querySelector('input[name="em_relationship"]').value = "<?php echo $em_relationship; ?>";
            document.querySelector('input[name="em_email"]').value = "<?php echo $em_email; ?>";
            document.querySelector('input[name="em_phone"]').value = "<?php echo $em_phone; ?>";

            document.querySelector('input[name="primary_physician_firstname"]').value = "<?php echo $primary_physician_firstname; ?>";
            document.querySelector('input[name="primary_physician_lastname"]').value = "<?php echo $primary_physician_lastname; ?>";
            document.querySelector('input[name="current_medications"]').value = "<?php echo $current_medications; ?>";
            document.querySelector('input[name="date"]').value = "<?php echo $appointment_date; ?>";

            // Set gender radio button
            if ("<?php echo $gender; ?>" === 'male') {
            document.querySelector('input[name="gender"][value="male"]').checked = true;
            } else if ("<?php echo $gender; ?>" === 'female') {
            document.querySelector('input[name="gender"][value="female"]').checked = true;
            }

            // Set allergies, contact lens, problems
            if ("<?php echo $allergies_medical; ?>" === '1') {
            document.querySelector('input[name="allergies_medical"][value="1"]').checked = true;
            } else {
            document.querySelector('input[name="allergies_medical"][value="0"]').checked = true;
            }

            if ("<?php echo $vision_allergies; ?>" === '1') {
            document.querySelector('input[name="vision_allergies"][value="1"]').checked = true;
            } else {
            document.querySelector('input[name="vision_allergies"][value="0"]').checked = true;
            }

            if ("<?php echo $contact_lens; ?>" === '1') {
            document.querySelector('input[name="contact_lens"][value="1"]').checked = true;
            } else {
            document.querySelector('input[name="contact_lens"][value="0"]').checked = true;
            }

            if ("<?php echo $problems; ?>" === '1') {
            document.querySelector('input[name="problems"][value="1"]').checked = true;
            } else {
            document.querySelector('input[name="problems"][value="0"]').checked = true;
            }

            // Set condition checkboxes
            <?php foreach ($conditions as $cond): ?>
            var el = document.querySelector('input[name="conditions[]"][value="<?php echo trim($cond); ?>"]');
            if (el) el.checked = true;
            <?php endforeach; ?>
        });
    </script>

  <?php include 'header/header.php'; ?>

  <!-- Main Content -->
  <main class="md:w-full lg:w-3/4 mx-auto p-6">
    <!-- Registration Tag -->
    <div class="mb-4">
      <span class="inline-block bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full font-semibold">
        ● Registration Form
      </span>
    </div>

    <h2 class="text-lg font-semibold mb-2">Please complete the required information in the fields below to complete your registration process.</h2>

    <!-- Patient Information -->
    <h3 class="text-xl font-bold mt-8 mb-4">Patient Information</h3>
    <?php 
        if ((isset($_SESSION['patient_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'patient')) {
    ?>
        <form class="space-y-8" method="POST" action="crud/updatePatientInformation.php?id=<?php echo $_SESSION['patient_id'] ?? 0 ?>"  enctype="multipart/form-data">
    <?php
        } else {
   
    ?> 
        <form class="space-y-8" method="POST" action="crud/registerPatient.php" enctype="multipart/form-data">
    <?php
        }
    ?>
      <!-- Patient Name and Address -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Name: <i class="text-red-500">*</i></label>
          <div class="grid grid-cols-3 gap-2 mt-1">
            <input type="text" required name="firstname" placeholder="First Name" class="border px-3 py-2 rounded w-full"/>
            <input type="text" name="middleInitial" placeholder="Middle Initial" maxlength="1" class="border px-3 py-2 rounded w-full"/>
            <input type="text" required name="lastname" placeholder="Last Name" class="border px-3 py-2 rounded w-full"/>
          </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Address:<i class="text-red-500">*</i></label>
            <div class="grid grid-cols-2 gap-2 mt-1">
                <input type="text" required name="barangay" placeholder="Barangay/Street" class="border px-3 py-2 rounded w-full"/>
                <input type="text" required name="city" placeholder="City" class="border px-3 py-2 rounded w-full"/>
            </div>
            </div>
        </div>

      <!-- DOB and Phone -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex gap-2">
                <div style="width: 75%;">
                    <label class="block text-sm font-medium">Date Birth: <i class="text-red-500">*</i></label>
                    <input type="date" name="dob"
                        id="dob"  required class="mt-1 border px-3 py-2 rounded w-full"/>
                </div>
                <div >
                    Age: 
                    <input type="text" readonly name="age" id="age" placeholder="0" class="mt-1 border px-3 py-2 rounded w-full"/>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Phone Number:</label>
                <input type="text" name="phone" placeholder="000-000-000" class="mt-1 border px-3 py-2 rounded w-full"/>
            </div>
        </div>

      <!-- Gender and Email -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Gender: <i class="text-red-500">*</i></label>
                <div class="mt-2 flex gap-4 items-center">
                    <label class="flex items-center gap-1">
                    <input type="radio" name="gender" value="male" required class="accent-blue-600" />
                        Male
                    </label>
                    <label class="flex items-center gap-1">
                    <input type="radio" name="gender" value="female" required class="accent-blue-600" />
                        Female
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Email:</label>
                <input type="email" value="northlens@gmail.com" name="email" class="mt-1 border px-3 py-2 rounded w-full"/>
            </div>
        </div>

        <!-- Emergency Contact Section -->
        <hr class="my-6 border-gray-300"/>
        <h3 class="text-xl font-bold mb-4">Emergency Contact</h3>

      <!-- Emergency Name and Address -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
            <label class="block text-sm font-medium">Name:</label>
            <div class="grid grid-cols-2 gap-2 mt-1">
                <input type="text" placeholder="First Name" name="em_firstname" class="border px-3 py-2 rounded w-full"/>
                <input type="text" placeholder="Last Name" name="em_lastname" class="border px-3 py-2 rounded w-full"/>
            </div>
            </div>

            <div>
            <label class="block text-sm font-medium">Address:</label>
            <div class="grid grid-cols-2 gap-2 mt-1">
                <input type="text" placeholder="Barangay/Street" name="em_barangay" class="border px-3 py-2 rounded w-full"/>
                <input type="text" placeholder="City" name="em_city" class="border px-3 py-2 rounded w-full"/>
            </div>
            </div>
        </div>

        <!-- Relationship, Email, Phone -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
            <label class="block text-sm font-medium">Relationship to Patient:</label>
            <input type="text" placeholder="Relationship" name="em_relationship" class="mt-1 border px-3 py-2 rounded w-full"/>
            </div>
            <div>
            <label class="block text-sm font-medium">Email:</label>
            <input type="email" value="northlens@gmail.com" name="em_email" class="mt-1 border px-3 py-2 rounded w-full"/>
            </div>
        </div>

        <div class="w-full md:w-1/2">
            <label class="block text-sm font-medium">Phone Number:</label>
            <input type="text" placeholder="000-000-000" name="em_phone" class="mt-1 border px-3 py-2 rounded w-full"/>
        </div>

    <!-- Medical History -->
    <h3 class="text-xl font-bold mt-12 mb-4">Medical History</h3>
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
                <div>
                    <label class="block text-sm font-medium mb-1">Others</label>
                    <input type="text" class="w-full border rounded px-3 py-2 resize-none" name="others" />
                </div>
            </div>
                    <label class="block text-sm font-medium">Last Visit:</label>
                    <input type="date" placeholder="" name="last_visit" class="border px-3 py-2 rounded w-full" />
            </div>
            <div>
        </div>
    </div>

    <!-- Vision and Eye Health History -->
    <h3 class="text-xl font-bold mt-12 mb-4">Eye Health History</h3>
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
            </div>
            
            <div>
                <label class="block text-sm font-medium">Do you have existing eye glasses?</label>
                <div class="flex gap-4 mt-1">
                    <label class="flex items-center gap-1">
                    <input type="radio" name="aye_glasses" required value="1" class="accent-blue-600" /> Yes
                    </label>
                    <label class="flex items-center gap-1">
                    <input type="radio" name="aye_glasses" required value="0"class="accent-blue-600" /> No
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

    <!-- Date and Submit -->
    <div class="flex items-center justify-between mt-8">
        <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Submit</button>
    </div>

    </form>
  </main>
    <script>
        document.getElementById('dob').addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();

        if (!isNaN(dob.getTime())) { // valid date check
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();

            // Adjust if birthday hasn't happened yet this year
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
            }

            document.getElementById('age').value = age >= 0 ? age : 0;
        } else {
            document.getElementById('age').value = '';
        }
        });
    </script>
</body>
</html>
