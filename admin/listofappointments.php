    <div class="w-full lg:w-full bg-white rounded-lg">
        <div class="">

        </div>
        <h2 class="text-xl font-semibold border-b p-6">Appointment Lists</h2>
        
        <div class="overflow-x-auto bg-white rounded shadow-md">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100 text-left">
                        <tr class="border-b data-[state=selected]:bg-muted transition-colors hover:bg-muted/50 bg-card">
                            <th class="p-4 align-middle [&:has([role=checkbox])]:pr-0 font-medium">Service</th>
                            <th class="py-2 px-4 border">Patient Name</th>
                            <th class="py-2 px-4 border">Date</th>
                            <th class="py-2 px-4 border">Time</th>
                            <th class="py-2 px-4 border">Status</th>
                            <th class="py-2 px-4 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            
                            include 'db/db.php';
                            $stmt = $conn->prepare("SELECT appointments.status as status_name, appointments.id, patients.firstname, patients.lastname, services.name as service_name, date_selected, time_selected  FROM appointments inner join patients on appointments.patient_id=patients.id inner join services on appointments.service_id=services.id ORDER BY appointments.date_selected desc");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $listofappointments = $result->fetch_all(MYSQLI_ASSOC);
                            $stmt->close();
                            $conn->close();
                             
                            foreach ($listofappointments as $listofappointment) {
                        ?>
                            <tr class="border-b data-[state=selected]:bg-slate-50 transition-colors hover:bg-slate-50 bg-white">
                                <td class="p-4 align-middle [&:has([role=checkbox])]:pr-0 font-medium"><?php echo $listofappointment['service_name'] ?></td>
                                <td class="text-center px-2 py-2"><?php echo $listofappointment['firstname'] . ' ' . $listofappointment['lastname'] ; ?></td>
                                <td class="text-center px-2 py-2"><?php echo date('F d, Y', strtotime($listofappointment['date_selected'])) ?></td>
                                <td class="text-center px-2 py-2"><?php echo date('h:i A', strtotime($listofappointment['time_selected'])) ?></td>
                                <td class="text-center px-2 py-2">
                                    <?php 
                                        if ($listofappointment['status_name'] == 'COMPLETED') {
                                            echo '<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium bg-green-50 text-green-500 hover:bg-green-200 border-green-200">' . $listofappointment['status_name'] . '</span>';
                                        }
                                        else if ($listofappointment['status_name'] == 'CANCELED') {
                                            echo '<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium bg-red-50 text-red-500 hover:bg-red-200 border-red-200">' . $listofappointment['status_name'] . '</span>';
                                        }
                                        else if ($listofappointment['status_name'] == 'APPROVED') {
                                            echo '<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium bg-blue-50 text-blue-500 hover:bg-blue-200 border-blue-200">' . $listofappointment['status_name'] . '</span>';
                                        }
                                        else {
                                            echo '<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium bg-blue-50 text-slate-500 hover:bg-slate-200 border-slate-200">' . $listofappointment['status_name'] . '</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-center py-2">
                                    <?php 
                                        if ($listofappointment['status_name'] == 'PENDING') {
                                    ?>
                                        <button type="button" onclick="openApprove(<?php echo $listofappointment['id']; ?>)"  class="bg-blue-600 text-white px-4 py-2 rounded">Approve</button>
                                        <button type="button" onclick="openCancel(<?php echo $listofappointment['id']; ?>)" class="bg-red-600 text-white px-4 py-2 rounded">Cancel</button>
                                    <?php
                                        } else if ($listofappointment['status_name'] == 'APPROVED') {
                                    ?>
                                        <button type="button" onclick="openDone(<?php echo $listofappointment['id']; ?>)"  class="bg-blue-600 text-white px-4 py-2 rounded">Completed</button>
                                        <button type="button" onclick="reschedModal(<?php echo $listofappointment['id']; ?>)" class="bg-red-600 text-white px-4 py-2 rounded">Reschedule</button>
                                    <?php    
                                        }
                                    ?>
                                </td>
                            </tr>

                            <!-- Approve Modal -->
                            <div id="approveModal<?php echo $listofappointment['id']; ?>" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                                <div class="bg-white w-full max-w-sm p-6 rounded shadow-lg text-center">
                                <h3 class="text-lg font-semibold mb-4 text-blue-600">Approve Appointment?</h3>
                                <p class="mb-4 text-gray-700">Are you sure you want to approve this appointment? This action cannot be undone.</p>
                                <div class="flex justify-center gap-4">
                                    <button onclick="closeApproveModal(<?php echo $listofappointment['id']; ?>)" class="px-4 py-2 border rounded text-gray-700">Cancel</button>
                                    <a href="crud/updateAppointment.php?status=APPROVED&id=<?php echo $listofappointment['id']; ?>" class="px-4 py-2 bg-blue-600 text-white rounded">Approve</a>
                                </div>
                                </div>
                            </div>
                            <!-- Cancel Modal -->

                            <div id="cancelModal<?php echo $listofappointment['id']; ?>" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                                <div class="bg-white w-full max-w-sm p-6 rounded shadow-lg text-center">
                                <h3 class="text-lg font-semibold mb-4 text-red-600">Cancel Appointment?</h3>
                                <p class="mb-4 text-gray-700">Are you sure you want to cancel this appointment? This action cannot be undone.</p>
                                <div class="flex justify-center gap-4">
                                    <button onclick="closeCancelModal(<?php echo $listofappointment['id']; ?>)" class="px-4 py-2 border rounded text-gray-700">Close</button>
                                    <a href="crud/updateAppointment.php?status=CANCELED&id=<?php echo $listofappointment['id']; ?>" class="px-4 py-2 bg-red-600 text-white rounded">Cancel</a>
                                </div>
                                </div>
                            </div>

                            <div id="doneModal<?php echo $listofappointment['id']; ?>" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                                <div class="bg-white w-full max-w-sm p-6 rounded shadow-lg text-center">
                                <h3 class="text-lg font-semibold mb-4 text-green-600">Completed Appointment?</h3>
                                <p class="mb-4 text-gray-700">Are you sure you want to complete this appointment? This action cannot be undone.</p>
                                <div class="flex justify-center gap-4">
                                    <button onclick="closeDoneModal(<?php echo $listofappointment['id']; ?>)" class="px-4 py-2 border rounded text-gray-700">Close</button>
                                    <a href="crud/updateAppointment.php?status=COMPLETED&id=<?php echo $listofappointment['id']; ?>" class="px-4 py-2 bg-blue-600 text-white rounded">Completed</a>
                                </div>
                                </div>
                            </div>

                            <div id="rescheduleModal<?php echo $listofappointment['id']; ?>" class="p-6 hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center  lg:w-full z-50">
                                <div class="bg-white w-1/2 max-w-lg p-6 rounded shadow-lg">
                                    <h3 class="text-lg font-semibold mb-4 text-green-600">Resched Appointment?</h3>
                                    <form action="crud/rescheduleAppointment.php?id=<?php echo $listofappointment['id']; ?>" method="POST">
                                        <p class="mb-4 text-gray-700">Select Date.</p>
                                        <input required type="date" name="date" id="datePicker<?php echo $listofappointment['id']; ?>" class="w-full datePicker" min="<?= date('Y-m-d'); ?>">
                                        <p class="mb-4 text-gray-700 mt-4">Select Time</p>
                                        <select id="timeDropdown<?php echo $listofappointment['id']; ?>" name="time" class="border p-2 rounded w-full text-gray-700">
                                            <option value="">Select a date first</option>
                                        </select>
                                        <div class="flex justify-center gap-4 mt-2">
                                            <button onclick="closeReschedModal(<?php echo $listofappointment['id']; ?>)" type="button" class="px-4 py-2 border rounded text-gray-700">Close</button>
                                            <button class="px-4 py-2 bg-blue-600 text-white rounded">Reschedule</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php
                            }
                        ?>

                    </tbody>
                </table>
        </div>
    </div>

    <script>
        function openApprove(id) {
            const approveModal = document.getElementById('approveModal' + id);
            approveModal.classList.remove('hidden');
        }


        function closeApproveModal(id) {
            const approveModal = document.getElementById('approveModal' + id);
            approveModal.classList.add('hidden');
        }
        
        function openCancel(id) {
            const approveModal = document.getElementById('cancelModal' + id);
            approveModal.classList.remove('hidden');
        }

        function closeCancelModal(id) {
            const approveModal = document.getElementById('cancelModal' + id);
            approveModal.classList.add('hidden');
        }

        function openDone(id) {
            const approveModal = document.getElementById('doneModal' + id);
            approveModal.classList.remove('hidden');
        }

        function closeDoneModal(id) {
            const approveModal = document.getElementById('doneModal' + id);
            approveModal.classList.add('hidden');
        }

        function reschedModal(id) {
            
            const approveModal = document.getElementById('rescheduleModal' + id);
            approveModal.classList.remove('hidden');
        }

        function closeReschedModal(id) {
            
            const approveModal = document.getElementById('rescheduleModal' + id);
            approveModal.classList.add('hidden');
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.datePicker').forEach(function(picker) {
                picker.addEventListener('change', function() {
                    const selectedDate = this.value;
                    fetch('crud/get-available-times.php?date=' + selectedDate)
                        .then(response => response.json())
                        .then(times => {
                            const appointmentId = this.id.replace('datePicker', '');
                            const dropdown = document.getElementById('timeDropdown' + appointmentId);
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
        })
    </script>