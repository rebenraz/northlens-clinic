<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NorthLens</title>
  <link href="src/output.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800">

  <!-- Navbar -->
    <?php include 'header/header.php'; ?>

  <!-- Main Section -->
<div class="flex flex-col lg:flex-row min-h-screen bg-gray-50">

  <!-- Left Side -->
  <div class="lg:flex md:w-full sm:w-full flex-col justify-center items-start lg:w-1/2 px-6 py-6 bg-blue-600 text-white" style="padding: 50px 50px;">
    <div class="flex items-center mb-6 md:w-full sm:w-full  w-full mt-12">
      <div class="bg-white rounded-lg p-2 text-center flex-shrink-0">
        <img src="images/northlens-eye.png" alt="NorthLens Logo" class="h-8 w-10 " />
      </div>
      <div class="ml-6 leading-tight">
        <h1 class="text-4xl font-bold ml-3 px-2">NorthLens</h1>
        <p class="text-md opacity-90  ml-3  px-2">Optical Clinic</p>
      </div>
    </div>

    <h2 class="text-4xl font-bold mb-4 mt-12" style="margin-top: 80px;">Streamline your optical clinic operations</h2>
    <p class="text-white/90 leading-relaxed mb-8">
      Manage appointments, patient records, and prescriptions with our comprehensive
      management solution designed specifically for eye care professionals.
    </p>
    <div  style="margin-top: 150px;" class="flex items-center mb-6 w-full mt-12 space-x-2 ">
        <svg xmlns="http://www.w3.org/2000/svg" 
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
            stroke="currentColor" class="h-8 w-8 text-white">
          <path stroke-linecap="round" stroke-linejoin="round" 
                d="M9 12.75l2.25 2.25L15 9.75M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z" />
        </svg>

        <div class="ml-6 leading-tight">
          <h1 class="font-bold ml-3 px-2">Easy Scheduling</h1>
          <p class="text-md opacity-90  ml-3  px-2">Manage patient appointments efficiently</p>
        </div>  
    </div>
  </div>

  <!-- Right Side -->
  <div class="flex flex-col justify-center items-center md:w-full lg:w-1/2 py-12 box-border border-2" style="padding-left: 50px;padding-right: 50px;">
    <div class="bg-white shadow-2xl rounded-xl p-8 lg:w-3/4 box-border border-2">

      <?php if (isset($_GET['signup'])) { ?>
        <h2 class="text-2xl font-semibold text-center mb-2">Create an Account</h2>
        <p class="text-sm text-center text-gray-500 mb-6">
          Enter your details to sign up and start managing your clinic efficiently.
        </p>
        <div class="flex mb-6 bg-gray-200 rounded-md overflow-hidden">
          <a href="?login" class="w-1/2 py-2 font-medium text-gray-600 text-center">Login</a>
          <a href="?signup" class="w-1/2 py-2 font-medium bg-white text-blue-700 text-center shadow-inner">Sign Up</a>
        </div>

        <!-- Signup Form -->
        <form method="POST" action="crud/registerUser.php" class="space-y-4">
          <div class="flex space-x-3">
            <input type="text" name="firstname" required placeholder="Firstname" class="w-1/2 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
            <input type="text" name="lastname" required placeholder="Lastname" class="w-1/2 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
          </div>

          <div class="flex space-x-3">
            <input type="text" name="mi" maxlength="1" placeholder="M.I." class="w-1/2 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
            <input type="text" name="suffix" placeholder="Suffix" class="w-1/2 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
          </div>

          <div class="flex space-x-3">
            <div class="w-1/2">
              <label class="block text-sm font-medium text-gray-600">Contact</label>
              <input type="text" name="phone" placeholder="000-000-0000" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="w-1/2">
              <label class="block text-sm font-medium text-gray-600">Birthdate</label>
              <input type="date" name="dob" id="dob" required class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>

          <div class="flex space-x-3">
            <div class="w-1/2">
              <label class="block text-sm font-medium text-gray-600">Gender</label>
              <div class="flex gap-4 mt-1">
                <label class="flex items-center gap-1">
                  <input type="radio" name="gender" value="male" required class="accent-blue-600" /> Male
                </label>
                <label class="flex items-center gap-1">
                  <input type="radio" name="gender" value="female" required class="accent-blue-600" /> Female
                </label>
              </div>
            </div>
            <div class="w-1/2">
              <label class="block text-sm font-medium text-gray-600">Age</label>
              <div class="flex gap-4 mt-1">
                <label class="flex items-center gap-1">
                  <input type="text" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" readonly id="age" /> 
                </label>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600">Address</label>
            <div class="grid grid-cols-2 gap-2">
              <input type="text" name="barangay" required placeholder="Barangay/Street" class="border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
              <input type="text" name="city" required placeholder="City" class="border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>

          <input type="email" name="email" required placeholder="Email Address" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
          <div class="relative w-full">
            <input 
              type="password" 
              name="password" 
              id="password" 
              required 
              placeholder="Password"
              class="w-full border rounded px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-500"
            />

            <!-- Eye Toggle (inside the input) -->
            <button 
                type="button"
                onclick="togglePassword()" 
                style="top: 22px;"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
              >
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" 
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M2.25 12C3.75 7.5 7.5 4.5 12 4.5s8.25 3 9.75 7.5
                          c-1.5 4.5-5.25 7.5-9.75 7.5S3.75 16.5 2.25 12z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
          </div>
           <div class="relative w-full">
            <input 
              type="password" 
              name="confirm_password" 
              id="cpassword" 
              required 
              placeholder="Confirm Password"
              class="w-full border rounded px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-500"
            />

            <!-- Eye Toggle (inside the input) -->
            <button 
                type="button"
                onclick="toggleCPassword()" 
                style="top: 22px;"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
              >
                <svg id="ceyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" 
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M2.25 12C3.75 7.5 7.5 4.5 12 4.5s8.25 3 9.75 7.5
                          c-1.5 4.5-5.25 7.5-9.75 7.5S3.75 16.5 2.25 12z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
          </div>

          <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Sign Up
          </button>
        </form>

      <?php } else { ?>
        <h2 class="text-2xl font-semibold text-center mb-2">Welcome Back</h2>
        <p class="text-sm text-center text-gray-500 mb-6">Enter your credentials to access the clinic management system</p>

        <div class="flex mb-6 bg-gray-200 rounded-md overflow-hidden">
          <a href="?login" class="w-1/2 py-2 font-medium bg-white text-blue-700 text-center shadow-inner">Login</a>
          <a href="?signup" class="w-1/2 py-2 font-medium text-gray-600 text-center">Sign Up</a>
        </div>

        <?php if (isset($_GET['error'])): ?>
          <div class="mb-4 text-red-600 text-sm text-center">
            Invalid username or password.
          </div>
        <?php endif; ?>

        <form method="POST" action="crud/loginFunction.php" class="space-y-4">
          <input type="email" name="email" required placeholder="Email Address" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" />
          <div class="relative w-full">
            <input 
              type="password" 
              name="password" 
              id="password" 
              required 
              placeholder="Password"
              class="w-full border rounded px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-500"
            />

            <!-- Eye Toggle (inside the input) -->
            <button 
                type="button"
                onclick="togglePassword()" 
                style="top: 22px;"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
              >
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" 
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M2.25 12C3.75 7.5 7.5 4.5 12 4.5s8.25 3 9.75 7.5
                          c-1.5 4.5-5.25 7.5-9.75 7.5S3.75 16.5 2.25 12z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
          </div>
          <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Login
          </button>
          <div class="text-center mt-2">
            <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
          </div>
        </form>
      <?php } ?>

    </div>
  </div>
</div>


<script>
  
    
    const form = document.getElementById('login-form');
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    if (form) {
      document.getElementById('login-form').addEventListener('submit', function (e) {
        const username = document.getElementById('username');
        const password = document.getElementById('password');
        let valid = true;

        if (!username.value.trim()) {
          username.classList.add('ring-2', 'ring-red-500');
          valid = false;
        } else {
          username.classList.remove('ring-2', 'ring-red-500');
        }

        if (!password.value.trim()) {
          password.classList.add('ring-2', 'ring-red-500');
          valid = false;
        } else {
          password.classList.remove('ring-2', 'ring-red-500');
        }

        if (!valid) {
          e.preventDefault(); // stop form submission
        }
      });
    }

   const emailInput = document.getElementById("email");
    const emailWarning = document.getElementById("emailWarning");
    const signupBtn = document.querySelector("#signup-form button");

    if (emailInput) {
      emailInput.addEventListener("keyup", function () {
        const email = emailInput.value.trim();
        if (!email) return;

        fetch("crud/checkEmail.php?email=" + encodeURIComponent(email))
          .then(res => res.text())
          .then(result => {
            if (result === "exists") {
              emailWarning.textContent = "⚠️ This email is already registered.";
              emailWarning.classList.remove("hidden");
              signupBtn.disabled = true;
              signupBtn.classList.add("opacity-50", "cursor-not-allowed");
            } else {
              emailWarning.textContent = "";
              emailWarning.classList.add("hidden");
              signupBtn.disabled = false;
              signupBtn.classList.remove("opacity-50", "cursor-not-allowed");
            }
          })
          .catch(err => console.error("Error checking email:", err));
      });
    }

    const signform = document.getElementById("signup-form");
    const signpassword = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    const warning = document.getElementById("passwordWarning");
    if (confirmPassword) {
      confirmPassword.addEventListener("keyup", function (e) {
        if (signpassword.value !== confirmPassword.value) {
          warning.textContent = "⚠️  Passwords do not match!";
          warning.classList.remove("hidden");
          signupBtn.disabled = true;
          signupBtn.classList.add("opacity-50", "cursor-not-allowed");
        } else {
          warning.textContent = "";
          warning.classList.add("hidden");
          signupBtn.disabled = false;
          signupBtn.classList.remove("opacity-50", "cursor-not-allowed");
        }
      });
    }

  function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const isHidden = passwordInput.type === 'password';

    passwordInput.type = isHidden ? 'text' : 'password';

    // Toggle eye icon (switch between open and closed)
    eyeIcon.outerHTML = isHidden
      ? `<svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" 
              viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
              class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M3.98 8.223A10.477 10.477 0 001.5 12c1.5 4.5 5.25 7.5 10.5 7.5 
                     3.042 0 5.79-1.224 7.77-3.223M6.228 6.228A9.715 9.715 0 0112 4.5c4.5 0 8.25 3 
                     9.75 7.5a10.478 10.478 0 01-1.272 2.707M6.228 6.228L3 3m0 0l18 18" />
          </svg>`
      : `<svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" 
              viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
              class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M2.25 12C3.75 7.5 7.5 4.5 12 4.5s8.25 3 9.75 7.5
                     c-1.5 4.5-5.25 7.5-9.75 7.5S3.75 16.5 2.25 12z" />
            <circle cx="12" cy="12" r="3" />
          </svg>`;
  }

  function toggleCPassword() {
    const passwordInput = document.getElementById('cpassword');
    const eyeIcon = document.getElementById('ceyeIcon');
    const isHidden = passwordInput.type === 'password';

    passwordInput.type = isHidden ? 'text' : 'password';

    // Toggle eye icon (switch between open and closed)
    eyeIcon.outerHTML = isHidden
      ? `<svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" 
              viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
              class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M3.98 8.223A10.477 10.477 0 001.5 12c1.5 4.5 5.25 7.5 10.5 7.5 
                     3.042 0 5.79-1.224 7.77-3.223M6.228 6.228A9.715 9.715 0 0112 4.5c4.5 0 8.25 3 
                     9.75 7.5a10.478 10.478 0 01-1.272 2.707M6.228 6.228L3 3m0 0l18 18" />
          </svg>`
      : `<svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" 
              viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
              class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M2.25 12C3.75 7.5 7.5 4.5 12 4.5s8.25 3 9.75 7.5
                     c-1.5 4.5-5.25 7.5-9.75 7.5S3.75 16.5 2.25 12z" />
            <circle cx="12" cy="12" r="3" />
          </svg>`;
  }
  const dob = document.getElementById('dob');

  if (dob) {
    document.getElementById('dob').addEventListener('change', function() {
      const dob = new Date(this.value);
      const today = new Date();
        if (!isNaN(dob.getTime())) { // valid date check
          let age = today.getFullYear() - dob.getFullYear();
          const m = today.getMonth() - dob.getMonth();

          // Adjust if birthday hasn't happened yet this year
          if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
          age--;
          }

          document.getElementById('age').value = age >= 0 ? age : 0;
      } else {
          document.getElementById('age').value = '';
      }
    });
  }
</script>

</body>
</html>
