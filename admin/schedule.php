  <?php 
    
    include 'db/db.php';
    $listofservices = [];

    $result = $conn->query("SELECT * FROM services where available = 1");
    $listofservices = $result->fetch_all(MYSQLI_ASSOC);

  ?>
    <!-- Date & Time Selection Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Calendar Box -->
      <div class="border rounded-lg p-4  bg-white">
        <h3 class="font-semibold text-sm mb-2">Select Date</h3>
        <p class="text-xs text-gray-500 mb-4">Choose an appointment date</p>
           <div class="flex items-center justify-between mb-4">
              <button type="button" id="prevMonth" class="text-lg">&lt;</button>
              <span id="monthYear" class="text-base font-medium"></span>
              <button type="button" id="nextMonth" class="text-lg">&gt;</button>
            </div>
           <table class="w-full table-fixed">
              <thead class="text-gray-500">
                <tr >
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

      <!-- Time Slots Box -->
      <div class="border rounded-lg p-4  bg-white">
        <h3 class="font-semibold text-sm mb-2">Available Time Slots</h3>
        <p class="text-xs text-gray-500 mb-4">Select a time slot for <span id="dateTitle"></span></p>
        <div class="grid grid-cols-4 gap-3 text-sm" id="timeslots">
       
        </div>

        <div class="mt-4 text-right">
          <button type="button" id="openModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Schedule Appointment</button>
        </div>
      </div>
      <!-- Appointment Modal -->
      <div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
          <h3 class="text-lg font-semibold mb-4">Schedule Appointment</h3>

          <!-- Close Button -->
          <button type="button" id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>

          <!-- Search Input -->
          <input type="text" id="patientSearch" placeholder="Search patient..." class="border px-3 py-2 w-full rounded text-sm mb-3">

          <!-- Patient Dropdown -->
          <select id="patientDropdown" class="border px-3 py-2 w-full rounded text-sm mb-3">
            <option value="">Select Patient</option>
            <!-- Dynamically filled by JS -->
          </select>
          <span id="errorPatient" class="text-red-500 hidden">Please select patient</span>
          <select id="servicesDropdown" class="border px-3 py-2 w-full rounded text-sm mb-3">
            <option value="">Select Services</option>
            <?php 
              foreach($listofservices as $listofservice) {
            ?>
                <option value="<?php echo $listofservice['id'];?>">
                  <?php 
                    echo $listofservice['name'];
                  ?>
                </option>
            <?php
              }

            ?>
          </select>
          <span id="errorService" class="text-red-500 hidden">Please select service</span>
          <!-- Selected Date & Time -->
          <div class="text-sm text-gray-600 mb-4">
            Date: <span id="modalDate"></span> <br>
            Time: <span id="modalTime"></span>
          </div>

          <!-- Save Button -->
          <button id="saveAppointment" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm w-full">Save Appointment</button>
        </div>
      </div>

      <form >
        <input type="hidden" id="selectedDateInput">
        <input type="hidden" id="selectedTimeInput">
        <input type="hidden" id="patient_id">
        <input type="hidden" id="service_id">

      </form>
    </div>

<script>
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
            cell.innerHTML = `<button type="button" id="date-${date}-${month}-${year}" class="text-black hover:bg-blue-100 rounded-full w-8 h-8">${date}</button>`;
            cell.querySelector('button').addEventListener('click', () => {
              document.getElementById('selectedDateInput').value = cellDate.toDateString();
              document.querySelectorAll('td').forEach(td => td.classList.remove('selected'));
              cell.classList.add('selected');
              document.getElementById('dateTitle').innerHTML =  cellDate.toDateString();
              loadAvailableTimes()
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

  prevMonth.addEventListener('click', () => {
    if (currentMonth === today.getMonth() && currentYear === today.getFullYear()) return;
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    renderCalendar(currentMonth, currentYear);
  });

  nextMonth.addEventListener('click', () => {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    renderCalendar(currentMonth, currentYear);
  });

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

  document.getElementById('timeslots').addEventListener('click', function(e) {
  if (e.target && e.target.classList.contains('time-slot')) {
    // Remove styles from all buttons
    document.querySelectorAll('.time-slot').forEach(b => b.classList.remove('bg-blue-500', 'text-white'));

    // Add style to selected one
    e.target.classList.add('bg-blue-500', 'text-white');

    // Update hidden input
    document.getElementById('selectedTimeInput').value = e.target.getAttribute('data-time');
  }
});
</script>
<script>
  let allPatients = []; // Store all patients for filtering

  const modal = document.getElementById('appointmentModal');
  const openModal = document.getElementById('openModal');
  const closeModal = document.getElementById('closeModal');
  const saveAppointment = document.getElementById('saveAppointment');
  const searchInput = document.getElementById('patientSearch');
  const dropdown = document.getElementById('patientDropdown');

  // Open modal
  openModal.addEventListener('click', () => {
    const selectedDate = document.getElementById('selectedDateInput').value;
    const selectedTime = document.getElementById('selectedTimeInput').value;

    if (!selectedDate || !selectedTime) {
      alert('Please select a date and time first.');
      return;
    }

    document.getElementById('modalDate').innerText = selectedDate;
    document.getElementById('modalTime').innerText = selectedTime;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Load all patients only once
    if (allPatients.length === 0) {
      fetch('crud/get_patient.php')
        .then(response => response.json())
        .then(patients => {
          allPatients = patients;
          updateDropdown('');
        });
    } else {
      updateDropdown(searchInput.value);
    }
  });

  // Filter dropdown as you type
  searchInput.addEventListener('input', () => {
    updateDropdown(searchInput.value);
  });

  // Function to update dropdown options based on search
  function updateDropdown(filter) {
    const lowerFilter = filter.toLowerCase();
    dropdown.innerHTML = '<option value="">Select Patient</option>';

    const filtered = allPatients.filter(patient =>
      (`${patient.firstname} ${patient.lastname}`).toLowerCase().includes(lowerFilter)
    );

    filtered.forEach(patient => {
      const option = document.createElement('option');
      option.value = patient.id;
      option.textContent = `${patient.firstname} ${patient.lastname}`;
      dropdown.appendChild(option);
    });

    if (filtered.length === 0) {
      const option = document.createElement('option');
      option.textContent = "No matches found";
      option.disabled = true;
      dropdown.appendChild(option);
    }
  }

  // Close modal
  closeModal.addEventListener('click', () => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  });

  // Save appointment
  saveAppointment.addEventListener('click', () => {
    const patientId = dropdown.value;
    const serviceId =  document.getElementById('servicesDropdown').value;
    const date = document.getElementById('selectedDateInput').value;
    const time = document.getElementById('selectedTimeInput').value;
    document.getElementById('patient_id').value = dropdown.value
    const errorPatient = document.getElementById('errorPatient')
    const errorService = document.getElementById('errorService')
    errorService.classList.add('hidden')
    errorPatient.classList.add('hidden')
    if (!patientId) {
      errorPatient.classList.remove('hidden')
      return;
    }
    if (!serviceId) {
      errorService.classList.remove('hidden')
      return;
    }

    fetch('crud/save_appointment.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `patient_id=${patientId}&service_id=${serviceId}&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`
    })
    .then(response => response.text())
    .then(result => {
      modal.classList.add('hidden');
      loadAvailableTimes()
      modal.classList.remove('flex');
    });
  });
</script>


<style>
  .selected button{
    background-color: black;
    color: white;
  }
</style>

