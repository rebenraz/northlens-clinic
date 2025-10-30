    <div class="relative h-[350px]">
      <canvas id="purposeChart"></canvas>
    </div>

    <p class="text-center text-sm text-gray-500 mt-4">Top 5 reasons for appointments</p>
    <?php
        include 'db/db.php';

        $stmt = $conn->prepare("
            SELECT s.name, COUNT(*) AS total
            FROM appointments
            JOIN services s ON appointments.service_id = s.id
            WHERE status != 'CANCELED'
            GROUP BY name
            ORDER BY total DESC
            LIMIT 5
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        $services = [];
        $counts = [];

        while ($row = $result->fetch_assoc()) {
            $services[] = $row['name'];
            $counts[] = $row['total'];
        }
    ?>  
    
<script>
      const ctx = document.getElementById('purposeChart').getContext('2d');

      const labels = <?php echo json_encode($services); ?>;
      const dataValues = <?php echo json_encode($counts); ?>;

      const purposeChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Appointments',
            data: dataValues,
            backgroundColor: '#67E8F9'
          }]
        },
        options: {
          indexAxis: 'y', // Horizontal bar chart
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              beginAtZero: true,
              grid: {
                color: '#E5E7EB'
              }
            },
            y: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 14
                }
              }
            }
          },
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                color: '#38BDF8',
                font: {
                  size: 14
                }
              }
            }
          }
        }
      });
</script>