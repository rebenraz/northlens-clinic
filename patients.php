<?php 
    session_start();
    $listsofservices = [];

    include 'db/db.php';

    $searchname = "";
    $sql_base = "SELECT 
        p.*, 
        pmh.physician_firstname, pmh.physician_lastname, pmh.have_allergies, 
        pmh.current_medications, pmh.conditions, pmh.health_allergies, 
        pmh.contact_lenses, pmh.any_problems, pmh.reason_for_visits, pmh.file_url, 
        pmh.others, pmh.date 
        FROM patients p
        LEFT JOIN medical_histories pmh ON p.id = pmh.patient_id";
        if (isset($_GET['search'])) {
        // Updated query to include email and phone_number for searching
            $searchterm = '%' . $_GET['search'] . '%';
            $sql = $sql_base . " WHERE p.firstname LIKE ? OR p.lastname LIKE ? OR p.email LIKE ? OR p.phone_number LIKE ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $searchterm, $searchterm, $searchterm, $searchterm);
            $stmt->execute();
        } else {
            
            $stmt = $conn->prepare($sql_base);
            $stmt->execute();
        }

    $result = $stmt->get_result();
    $listofpatients = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();

    if (isset($_SESSION['user_id']) && !(isset($_SESSION['role']) && $_SESSION['role'] == 'admin')) {
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Patients</title>
  <link href="src/output.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans text-sm text-gray-800">

      <?php 
        include 'header/header.php'
    ?>

    <div class="md:w-full lg:w-3/4 mx-auto px-6 py-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Patients</h2>
            <p class="text-sm text-slate-500">Manage patient records and information</p>
        </div>
      <a href="registration.php" class="bg-blue-600  text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm">Add New Patient</a>
    </div>

    <div class="mb-6 relative">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
        <input
            type="text"
            id="searchInput"
            placeholder="Search patients by name, email, or phone..."
            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
            class="flex w-full rounded-md border border-input bg-background px-3 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm h-12 pl-12 text-base shadow-sm"
        />    
    </div>

        <div class="space-y-4">

        <?php 
            foreach($listofpatients as $listofpatient) {
                $birthDate = new DateTime($listofpatient['date_of_birth']);
                $today = new DateTime();
                $age = $birthDate->diff($today)->y;
        ?>
            <div 
                class="border rounded p-4 flex justify-between items-center cursor-pointer bg-white rounded-lg hover:bg-gray-50 patient-card"
                data-patient-id="<?php echo $listofpatient['id']; ?>"
                data-firstname="<?php echo htmlspecialchars($listofpatient['firstname']); ?>"
                data-lastname="<?php echo htmlspecialchars($listofpatient['lastname']); ?>"
                data-email="<?php echo htmlspecialchars($listofpatient['email']); ?>"
                data-phone="<?php echo htmlspecialchars($listofpatient['phone_number']); ?>"
                data-dob="<?php echo htmlspecialchars($listofpatient['date_of_birth']); ?>"
                data-barangay="<?php echo htmlspecialchars($listofpatient['barangay']); ?>"
                data-city="<?php echo htmlspecialchars($listofpatient['city']); ?>"
                data-physician-fn="<?php echo htmlspecialchars($listofpatient['physician_firstname'] ?? ''); ?>"
                data-physician-ln="<?php echo htmlspecialchars($listofpatient['physician_lastname'] ?? ''); ?>"
                data-allergies="<?php echo htmlspecialchars($listofpatient['have_allergies'] ?? 0); ?>"
                data-medications="<?php echo htmlspecialchars($listofpatient['current_medications'] ?? ''); ?>"
                data-conditions="<?php echo htmlspecialchars($listofpatient['conditions'] ?? ''); ?>"
                data-reason="<?php echo htmlspecialchars($listofpatient['reason_for_visits'] ?? ''); ?>"
                data-file-url="<?php echo htmlspecialchars($listofpatient['file_url'] ?? ''); ?>"**                
                data-contact-lenses="<?php echo htmlspecialchars($listofpatient['contact_lenses'] ?? 0); ?>"
            >
            <div>
                <p class="text-xl font-semibold capitalize text-foreground"><?php echo $listofpatient['firstname'] . ' ' . $listofpatient['lastname'] ?></p>
                <p class="text-gray-600">
                    <p class="flex gap-2 mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-4 w-4 text-blue-500"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                        <?php echo $listofpatient['email']  ?> 
                    </p>
                    <p class="flex gap-2 mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-4 w-4 text-blue-500"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <?php echo $listofpatient['phone_number']  ?> 
                    </p>
                <div class="flex gap-6 mt-1 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4 text-blue-500"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                        <p>
                            <span class="font-medium">Date of Birth</span><br />
                            <?php 
                                $birthDate = new DateTime($listofpatient['date_of_birth']);
                                $today = new DateTime();
                                $age = $birthDate->diff($today)->y;
                            ?>
                            <?php echo date('F d, Y', strtotime($listofpatient['date_of_birth']))  ?> (<?php echo $age  ?> years)
                        </p>
                    </div>
                </div>
                </div>
                <span class="text-blue-600 border py-2 px-2 rounded-lg hover:text-blue-800 text-sm font-medium">View Details</span>
            </div>
        <?php
            }
        ?>

                <?php if (empty($listofpatients)) : ?>
            <p class="text-center text-gray-500 py-8">No patients found. Try a different search term or add a new patient.</p>
        <?php endif; ?>
    </div>
  </div>

    <div id="patientModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white border rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="modalPatientName"></h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600 text-2xl font-semibold">
                    &times;
                </button>
            </div>
            
            <div class="max-h-96 overflow-y-auto pr-4">
                <h4 class="text-lg font-semibold mb-2 text-blue-700">Personal & Contact Information</h4>
                <div class="grid grid-cols-2 gap-y-2 gap-x-6 text-gray-700 mb-6 border-b pb-4">
                <p><span class="font-medium text-gray-600">Email:</span> <span id="modalEmail"></span></p>
                <p><span class="font-medium text-gray-600">Phone:</span> <span id="modalPhone"></span></p>
                <p><span class="font-medium text-gray-600">Date of Birth:</span> <span id="modalDOB"></span></p>
                <p><span class="font-medium text-gray-600">Age:</span> <span id="modalAge"></span></p>
                    <p class="col-span-2"><span class="font-medium text-gray-600">Address:</span> <span id="modalAddress"></span></p>
            </div>

                <h4 class="text-lg font-semibold mb-2 text-blue-700">Medical History</h4>
                <div class="space-y-3 text-gray-700 mb-6">
                    <p><span class="font-medium text-gray-600">Primary Physician:</span> <span id="modalPhysician"></span></p>
                    <p><span class="font-medium text-gray-600">Reason for Visit:</span> <span id="modalReason"></span></p>
                    <p><span class="font-medium text-gray-600">Current Conditions:</span> <span id="modalConditions" class="whitespace-pre-wrap"></span></p>
                    <p><span class="font-medium text-gray-600">Current Medications:</span> <span id="modalMedications" class="whitespace-pre-wrap"></span></p>
                    <p><span class="font-medium text-gray-600">Allergies:</span> <span id="modalAllergies"></span></p>
                    <p><span class="font-medium text-gray-600">Wears Contact Lenses:</span> <span id="modalContacts"></span></p>
                    
                    <a 
                        href="#" 
                        target="_blank" 
                        id="viewFile"
                        class="text-blue-600 underline hover:text-blue-800 mt-2"
                    >
                        View Medical File
                    </a>
                </div>
            </div>

                        <div class="mt-6 flex justify-end gap-3">
                <a id="editPatientLink" href="#" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Edit Patient Info</a>
                <button id="closeModalBottom" class="border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-100 text-sm">Close</button>
            </div>
        </div>
    </div>
  </div>

 <script>
    // --- Search Functionality ---
    const input = document.getElementById('searchInput');
    let debounceTimer;

    input.addEventListener('keyup', function () {
        clearTimeout(debounceTimer); // Reset the timer on every keyup

        debounceTimer = setTimeout(() => {
        const query = input.value.trim();
        const params = new URLSearchParams(window.location.search);

        if (query) {
            params.set('search', query);
        } else {
            params.delete('search');
        }

        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.location.href = newUrl; // Navigate after delay
        }, 500); // Delay of 500ms
    });
   const modal = document.getElementById('patientModal');
     const closeButtons = [
      document.getElementById('closeModal'),
      document.getElementById('closeModalBottom'),
     ];
     const patientCards = document.querySelectorAll('.patient-card');

    function formatYesNo(value) {
        return value == 1 ? 'Yes' : 'No';
    }

     function openModal(patient) {
         // Calculate age
         const dobDate = new Date(patient.dob);
         const today = new Date();
         let age = today.getFullYear() - dobDate.getFullYear();
         const m = today.getMonth() - dobDate.getMonth();
         if (m < 0 || (m === 0 && today.getDate() < dobDate.getDate())) {
            age--;
         }

         // Format DOB for display
         const options = { year: 'numeric', month: 'long', day: 'numeric' };
         const formattedDOB = dobDate.toLocaleDateString('en-US', options);

         // --- Populate Personal/Contact Info ---
         document.getElementById('modalPatientName').textContent = `${patient.firstname} ${patient.lastname}`;
         document.getElementById('modalEmail').textContent = patient.email;
         document.getElementById('modalPhone').textContent = patient.phone;
         document.getElementById('modalDOB').textContent = formattedDOB;
         document.getElementById('modalAge').textContent = `${age} years`;
        document.getElementById('modalAddress').textContent = `${patient.barangay}, ${patient.city}`;

        // --- Populate Medical History Info ---
        const physicianName = patient.physicianFn || patient.physicianLn 
            ? `${patient.physicianFn} ${patient.physicianLn}`.trim() 
            : 'N/A';
        document.getElementById('modalPhysician').textContent = physicianName;

        document.getElementById('modalReason').textContent = patient.reason || 'N/A';
        document.getElementById('modalConditions').textContent = patient.conditions || 'N/A';
        document.getElementById('modalMedications').textContent = patient.medications || 'N/A';
        
        // Use the helper function for boolean fields
        document.getElementById('modalAllergies').textContent = formatYesNo(patient.allergies);
        document.getElementById('modalContacts').textContent = formatYesNo(patient.contactLenses);
        document.getElementById('modalContacts').textContent = formatYesNo(patient.contactLenses);
        if (patient.fileUrl) {
            document.getElementById('viewFile').href = (patient.fileUrl);
            document.getElementById('viewFile').textContent = "View Medical History";
        } else {
            document.getElementById('viewFile').href = "#";
            document.getElementById('viewFile').textContent = "No  Mdeical History";
        }


      document.getElementById('editPatientLink').href = `editpatientinformation.php?id=${patient.id}`;

      modal.classList.remove('hidden');
      modal.classList.add('flex');
   }

   function closeModal() {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
   }

   patientCards.forEach(card => {
      card.addEventListener('click', function (e) {
         if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
            return;
         }

         const patientData = {
            id: this.dataset.patientId,
            firstname: this.dataset.firstname,
            lastname: this.dataset.lastname,
            email: this.dataset.email,
            phone: this.dataset.phone,
            dob: this.dataset.dob,
            barangay: this.dataset.barangay,
            city: this.dataset.city,
            physicianFn: this.dataset.physicianFn,
            physicianLn: this.dataset.physicianLn,
            allergies: this.dataset.allergies,
            medications: this.dataset.medications,
            conditions: this.dataset.conditions,
            reason: this.dataset.reason,
            contactLenses: this.dataset.contactLenses,
            fileUrl: this.dataset.fileUrl,
        };
        openModal(patientData);
    });
     });

        closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
     });

     // Close when clicking outside the modal
    window.addEventListener('click', (event) => {
    if (event.target === modal) {
        closeModal();
    }
     });
    // --- End Modal Functionality ---
</script>
</body>
</html>