<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NorthLens</title>
  <link href="src/output.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800">

  <!-- Navbar -->
  <nav class="flex justify-between items-center p-6 shadow-sm">
    <div class="text-blue-600 text-xl font-bold">Northlens</div>
    <div class="space-x-6">
      <a href="#" class="text-gray-700 hover:text-blue-600">Home</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Services</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Register</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Appointment</a>
      <a href="#" class="text-blue-600 font-medium hover:underline">Login</a>
    </div>
  </nav>

  <!-- Main Section -->
  <div class="flex justify-center items-center min-h-screen bg-gray-50 px-6 py-12">
    <!-- Left Side -->
    <div class="w-full max-w-md mr-16 hidden lg:block">
      <h1 class="text-3xl font-bold text-blue-700 mb-2">NorthLens</h1>
      <p class="text-gray-600 text-lg">Optical Clinic Appointment System</p>
      <p class="mt-4 text-gray-500">Streamline your optical clinic operations with our comprehensive management solution.</p>
    </div>

    <!-- Signup/Login Box -->
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
      <h2 class="text-xl font-semibold text-center mb-2">Signup to NorthLens</h2>
      <p class="text-sm text-center text-gray-500 mb-6">Enter your credentials to access the clinic management system</p>

      <!-- Tabs -->
      <div class="flex mb-6">
        <a class="w-1/2 py-2 font-medium bg-gray-200 rounded-l-md text-center" href="/login.php">Login</a>
        <button class="w-1/2 py-2 font-medium bg-indigo-100 text-indigo-700 rounded-r-md">Signup</button>
      </div>

      <!-- Form -->
      <form class="space-y-4">
        <div class="flex space-x-4">
          <input type="text" placeholder="Firstname" class="w-1/2 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
          <input type="text" placeholder="Lastname" class="w-1/2 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>

        <input type="email" placeholder="name@example" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>

        <div class="relative">
          <input type="password" placeholder="Password" class="w-full border rounded px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
          <span class="absolute right-3 top-2.5 text-gray-400 cursor-pointer">👁️</span>
        </div>

        <div class="relative">
          <input type="password" placeholder="Confirm Password" class="w-full border rounded px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
          <span class="absolute right-3 top-2.5 text-gray-400 cursor-pointer">👁️</span>
        </div>

        <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
      </form>
    </div>
  </div>

</body>
</html>
