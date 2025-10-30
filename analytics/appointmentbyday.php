   <!-- Chart (Placeholder) -->
   <?php


    ?>
   <div class="mt-4 ">
        <canvas id="appointmentsChart" class="w-full h-80"  style="height: 300px;"></canvas>
    </div>
    
    <p class="text-center text-xs text-gray-500 mt-2">Distribution of appointments by day of week</p>
    <script>
            fetch('crud/getAppointmentinWeek.php', {
            method: 'GET',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        })
        .then(response => response.text())
        .then(result => {
            const res = JSON.parse(result);
            const ctx = document.getElementById('appointmentsChart').getContext('2d');
            const appointmentsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                labels: [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Appointments',
                    data: res, // Example data
                    backgroundColor: 'rgba(37, 99, 235, 0.8)', // Tailwind blue-600
                    borderRadius: 4,
                    barThickness: 30
                }]
                },
                options: {
                scales: {
                    y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 0.1
                    }
                    }
                },
                plugins: {
                    legend: {
                    display: false
                    }
                }
                }
            });
        });
    </script>