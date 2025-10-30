<?php 

    $listsofservices = [];

    include 'db/db.php';

    $stmt = $conn->prepare("SELECT * FROM services where available = 1");
   $stmt->execute();
   $result = $stmt->get_result();
   $listsofservices = $result->fetch_all(MYSQLI_ASSOC);
   $stmt->close();
   $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NorthLens - Services</title>
  <link href="src/output.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-white">

  <!-- Navbar -->
    <?php include 'header/header.php'; ?>

  <!-- Services Section -->
  <div class="flex flex-col md:flex-row p-8 gap-6">
    <!-- Left: Services List -->
    <div class="flex-1 space-y-4">

      <?php 
          foreach ($listsofservices as $service) {
      ?>
          <!-- Card 1 -->
          <div class="border rounded-xl p-4 shadow-sm hover:shadow-md transition cursor-pointer serviceDiv" 
            data-name="<?= htmlspecialchars($service['name']); ?>"
            data-description="<?= htmlspecialchars($service['description']); ?>"
            data-price="₱ <?= number_format($service['price'], 2); ?>"
            data-image="<?= isset($service['image_url']) ? htmlspecialchars($service['image_url']) : 'images/services.jpg'; ?>"
          >
            <div class="flex items-start gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-600 w-5 h-5 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <div>
                <h4 class="font-semibold text-black"><?php echo $service['name']; ?></h4>
                <p class="text-sm text-gray-600"><?php echo $service['description']; ?></p>
              </div>
            </div>
            <div class="flex justify-between text-xl font-semibold mt-3 px-6 text-blue-700">
              <span>₱ <?php echo number_format($service['price'], 2); ?></span>
            </div>
          </div>
      <?php
          }

      ?>
       <?php 
          if (isset($_SESSION['user_id'])) {
        ?>
          <div class="w-full">
            <!-- Book Appointment Button -->
            <a href="appointment.php" class="block px-2 w-full mt-4 bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700">
              Book Appointment
            </a>
          </div>
        <?php } else {
          ?>
          <div class="w-full">
            <!-- Book Appointment Button -->
            <a href="login.php" class="block px-2 w-full mt-4 bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700">
              Book Appointment
            </a>
          </div>
        <?php
          } ?>



    </div>

    <div class="w-full md:w-1/2 relative rounded-xl overflow-hidden shadow-md">
      <img src="images/services.jpg" id="imgPath" alt="Eye Exam" class="w-full h-full object-cover" />
      <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/60 to-transparent text-white">
        <h4 class="font-semibold text-lg" id="titleService">Comprehensive Eye Exam</h4>
        <p class="text-sm text-gray-200" id="descService">
          Complete assessment of eye health and vision quality with our expert optometrists.
        </p>
        <div class="flex gap-4 mt-2 text-sm">
          <span class="bg-white text-black px-3 py-1 rounded-md" id="priceService"></span>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const serviceCards = document.querySelectorAll(".serviceDiv");
      const title = document.getElementById("titleService");
      const desc = document.getElementById("descService");
      const price = document.getElementById("priceService");
      const img = document.getElementById("imgPath");

      serviceCards.forEach(card => {
        card.addEventListener("click", () => {
          // Update right-side details
          title.textContent = card.dataset.name;
          desc.textContent = card.dataset.description;
          price.textContent = card.dataset.price;

          // If your services have images
          img.src = card.dataset.image || "images/services.jpg";

          // Optional: highlight the selected card
          serviceCards.forEach(c => c.classList.remove("ring", "ring-blue-400"));
          card.classList.add("ring", "ring-blue-400");
        });
      });
    });
  </script>
</body>
</html>
