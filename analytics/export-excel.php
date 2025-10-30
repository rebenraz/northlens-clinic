<?php

  include '../db/db.php';

// Default filters
$range = $_GET['range'] ?? 'this_month';
$status = $_GET['status'] ?? 'ALL';

// Build date filter
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

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=appointment-history.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr><th>Name</th><th>Type</th><th>Price</th><th>Date</th><th>Status</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".ucwords($row['firstname'].' '.$row['lastname'])."</td>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['price']."</td>";
    echo "<td>".date('m/d/Y', strtotime($row['date_selected']))."</td>";
    echo "<td>".$row['status']."</td>";
    echo "</tr>";
}
echo "</table>";
exit;
?>
