<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book Appointment - Select Time</title>
  <link href="src/output.css" rel="stylesheet">
</head>
<body class="bg-white min-h-screen">

  <!-- Navbar -->
  <nav class="w-full px-8 py-4 flex justify-between items-center border-b shadow-sm">
    <h1 class="text-blue-600 font-bold text-xl">Northlens CMS</h1>
    <ul class="flex space-x-6 text-sm">
      <li><a href="#" class="text-gray-700 hover:text-blue-600">Home</a></li>
      <li><a href="#" class="text-gray-700 hover:text-blue-600">Services</a></li>
      <li><a href="#" class="text-gray-700 hover:text-blue-600">Register</a></li>
      <li><a href="#" class="text-blue-600 font-semibold">Appointment</a></li>
      <li><a href="#" class="text-gray-700 hover:text-blue-600">Login</a></li>
    </ul>
  </nav>

  <!-- Header -->
  <div class="text-center mt-10 mb-6">
    <span class="inline-block px-4 py-1 bg-blue-100 text-blue-600 text-sm font-medium rounded-full mb-2">
      • Appointment Scheduling
    </span>
    <h2 class="text-2xl font-bold mb-1">Book Your Appointment</h2>
    <p class="text-gray-600 max-w-xl mx-auto">
      Schedule your eye care visit with NorthLens Optical Clinic. Our streamlined booking
      process ensures you get the care you need, when you need it.
    </p>
  </div>

  <!-- Time Slot Card -->
  <div class="max-w-4xl mx-auto bg-white shadow-md rounded-xl px-6 py-6">
    
    <!-- Tabs -->
    <div class="flex justify-between text-sm text-gray-600 mb-4 border-b pb-2">
      <span class="text-blue-600 font-medium border-b-2 border-blue-600 pb-1">Select Service</span>
      <span>Select Date</span>
      <span class="text-blue-600 font-medium border-b-2 border-blue-600 pb-1">Select Time</span>
      <span>Confirm</span>
    </div>

    <!-- Time Slot Heading -->
    <div class="text-center mb-6">
      <h3 class="font-semibold text-lg">Select Time Slot</h3>
      <p class="text-gray-500 text-sm">Friday, May 9, 2025</p>
    </div>

    <!-- Time Slot Grid -->
    <div class="grid grid-cols-4 gap-4 text-sm max-w-2xl mx-auto">
      <button class="border px-4 py-2 rounded hover:bg-blue-100">9:00AM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">9:30AM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">10:00AM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">10:30AM</button>

      <button class="border px-4 py-2 rounded hover:bg-blue-100">11:00AM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">11:30AM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">1:00PM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">1:30PM</button>

      <button class="border px-4 py-2 rounded hover:bg-blue-100">2:00PM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">2:30PM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">3:00PM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">3:30PM</button>

      <button class="border px-4 py-2 rounded hover:bg-blue-100">4:00PM</button>
      <button class="border px-4 py-2 rounded hover:bg-blue-100">4:30AM</button>
    </div>

    <!-- Buttons -->
    <div class="flex justify-between mt-6">
      <button class="text-gray-600 hover:text-black px-4 py-2 rounded-md">Back</button>
      <button class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Continue</button>
    </div>
  </div>

</body>
</html>
