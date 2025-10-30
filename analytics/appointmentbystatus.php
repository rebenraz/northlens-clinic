<div class="max-w-4xl mx-auto">
  <div class="flex justify-center w-full">
    <div class="chart-container">
      <canvas id="statusChart"></canvas>
    </div>
  </div>
  <p class="text-center text-xl text-gray-500 mt-4">
    Distribution of appointments by status
  </p>
</div>

<style>
.chart-container {
  position: relative;
  width: 100%;
  max-width: 600px; /* controls chart width */
  height: 400px; /* 🎯 adjust this for height */
}

@media (max-width: 640px) {
  .chart-container {
    height: 300px; /* smaller height on mobile */
  }
}
</style>

<script>
const ctx = document.getElementById('statusChart').getContext('2d');

fetch('crud/getAnalyticsSchedule.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
})
.then(response => response.text())
.then(result => {
  const parsed = JSON.parse(result);

  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Scheduled', 'Completed', 'Cancelled'],
      datasets: [{
        data: [parseInt(parsed.totalSchedule), parsed.totalComplete, parsed.totalCancel],
        backgroundColor: ['#3B82F6', '#10B981', '#FBBF24'],
        borderWidth: 1,
        anchor: 'end',
        align: 'end',
        offset: 2,
      }]
    },
    options: {
      maintainAspectRatio: false, // 👈 makes height responsive to container
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#374151',
            font: { size: 14 }
          }
        },
        datalabels: {
          color: '#000',
          anchor: 'end',
          align: 'end',
          offset: -5,
          font: { weight: 'bold' }
        }
      }
    },
    plugins: [ChartDataLabels]
  });
});
</script>
