<div >   
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-700">Appointment History</h2>
        <?php
            $currentRange = $_GET['range'] ?? 'this_month';
            $currentStatus = $_GET['appointmentStatus'] ?? 'ALL';
        ?>
        <div class="flex items-center gap-2">
            <select name="dateFilter" id="filterRange"
                class="flex h-10 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 w-40">
                <option value="this_day" <?= $currentRange === 'this_day' ? 'selected' : '' ?>>This Day</option>
                <option value="this_week" <?= $currentRange === 'this_week' ? 'selected' : '' ?>>This Week</option>
                <option value="this_month" <?= $currentRange === 'this_month' ? 'selected' : '' ?>>This Month</option>
                <option value="this_year" <?= $currentRange === 'this_year' ? 'selected' : '' ?>>This Year</option>
            </select>

            <select name="statusFilter" id="filterStatus"
                class="flex h-10 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 w-40">
                <option value="ALL" <?= $currentStatus === 'ALL' ? 'selected' : '' ?>>All Status</option>
                <option value="PENDING" <?= $currentStatus === 'PENDING' ? 'selected' : '' ?>>Pending</option>
                <option value="APPROVED" <?= $currentStatus === 'APPROVED' ? 'selected' : '' ?>>Approved</option>
                <option value="COMPLETED" <?= $currentStatus === 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
                <option value="CANCELED" <?= $currentStatus === 'CANCELED' ? 'selected' : '' ?>>Canceled</option>
            </select>
            <div>
                <button id="filterBtn" class="ml-2 px-4 py-2 bg-blue-600 rounded-lg text-white hover:bg-blue-700">
                    Filter
                </button>
                <button id="exportExcelBtn" class="px-4 py-2 bg-emerald-600 rounded-lg text-white hover:bg-emerald-700">
                    Export to Excel
                </button>
                <button id="printPdfBtn" class="px-4 py-2 bg-red-600 rounded-lg text-white hover:bg-red-700">
                    Print PDF
                </button>
            </div>

        </div>
    </div>
    <div class="overflow-x-auto mt-4">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b transition-colors data-[state=selected]:bg-muted hover:bg-slate-50 bg-slate-50">
                    <th class="h-12 px-4 text-left align-middle text-muted-foreground [&:has([role=checkbox])]:pr-0 font-semibold">Name</th>
                    <th class="h-12 px-4 text-left align-middle text-muted-foreground [&:has([role=checkbox])]:pr-0 font-semibold">Type</th>
                    <th class="h-12 px-4 text-left align-middle text-muted-foreground [&:has([role=checkbox])]:pr-0 font-semibold">Price</th>
                    <th class="h-12 px-4 text-left align-middle text-muted-foreground [&:has([role=checkbox])]:pr-0 font-semibold">Date</th>
                    <th class="h-12 px-4 text-left align-middle text-muted-foreground [&:has([role=checkbox])]:pr-0 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    
                    include 'db/db.php';
                    $range = $_GET['range'] ?? 'this_month';
                    $status = $_GET['appointmentStatus'] ?? 'ALL';

                    $listofappointments = [];
                    switch ($range) {
                        case 'this_day':
                            $whereDate = "DATE(appointments.date_selected) = CURDATE()";
                            break;
                        case 'this_week':
                            $whereDate = "YEARWEEK(appointments.date_selected, 1) = YEARWEEK(CURDATE(), 1)";
                            break;
                        case 'this_month':
                            $whereDate = "MONTH(appointments.date_selected) = MONTH(CURDATE()) 
                                        AND YEAR(appointments.date_selected) = YEAR(CURDATE())";
                            break;
                        case 'this_year':
                            $whereDate = "YEAR(appointments.date_selected) = YEAR(CURDATE())";
                            break;
                        default:
                            $whereDate = "1=1";
                            break;
                    }
                    $whereStatus = ($status != 'ALL') ? "AND appointments.status = '$status'" : "";
                    $query = "
                        SELECT appointments.price, patients.firstname, patients.lastname, services.name, appointments.date_selected, appointments.status
                        FROM appointments
                        INNER JOIN patients ON appointments.patient_id = patients.id
                        INNER JOIN services ON appointments.service_id = services.id
                        WHERE $whereDate $whereStatus
                        ORDER BY appointments.date_selected DESC
                    ";
                    
                    $result = $conn->query($query); 

                    $listofappointments = $result->fetch_all(MYSQLI_ASSOC);
                
                    foreach($listofappointments as $appointment) {
                ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2 align-middle [&:has([role=checkbox])]:pr-0 font-medium capitalize"><?php echo $appointment['firstname'] . ' ' . $appointment['lastname']; ?></td>
                            <td class="p-2 align-middle [&:has([role=checkbox])]:pr-0 font-sm"><?php echo $appointment['name'] ; ?></td>
                            <td class="p-2 align-middle [&:has([role=checkbox])]:pr-0 font-sm"><?php echo number_format($appointment['price'], 2) ; ?></td>
                            <td class="p-2 align-middle [&:has([role=checkbox])]:pr-0 font-sm"><?php echo date('m/d/Y', strtotime($appointment['date_selected'])) ; ?></td>
                            <td class="p-2 align-middle [&:has([role=checkbox])]:pr-0 font-sm">
                       
                                <?php 
                                    if ($appointment['status'] == 'COMPLETED') {
                                        echo '<span class="inline-flex items-center rounded-full border px-2 py-2 text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium bg-green-50 text-green-500 hover:bg-green-200 border-green-200">' . $appointment['status'] . '</span>';
                                    }
                                    else if ($appointment['status'] == 'CANCELED') {
                                        echo '<span class="inline-flex items-center rounded-full border px-2 py-2 text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium bg-red-50 text-red-500 hover:bg-red-200 border-red-200">' . $appointment['status'] . '</span>';
                                    }
                                    else if ($appointment['status'] == 'APPROVED') {
                                        echo '<span class="inline-flex items-center rounded-full border px-2 py-2 text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium bg-blue-50 text-blue-500 hover:bg-blue-200 border-blue-200">' . $appointment['status'] . '</span>';
                                    }
                                    else {
                                        echo '<span class="inline-flex items-center rounded-full border px-2 py-2 text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium bg-blue-50 text-slate-500 hover:bg-slate-200 border-slate-200">' . $appointment['status'] . '</span>';
                                    }
                                ?>
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
  document.getElementById('filterBtn').addEventListener('click', function() {
    const range = document.getElementById('filterRange').value;
    const status = document.getElementById('filterStatus').value;
    window.location.href = `?report&range=${range}&appointmentStatus=${status}`;
  });


  document.getElementById('exportExcelBtn').addEventListener('click', function() {
    const range = document.getElementById('filterRange').value;
    const status = document.getElementById('filterStatus').value;
    window.open(`analytics/export-excel.php?range=${range}&status=${status}`, '_blank');
  });

  document.getElementById('printPdfBtn').addEventListener('click', function() {
    const range = document.getElementById('filterRange').value;
    const status = document.getElementById('filterStatus').value;
    window.open(`analytics/export-pdf.php?range=${range}&status=${status}`, '_blank');
  });
</script>