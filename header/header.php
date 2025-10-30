
<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $approvedAppointment = 0;
    if (isset($_SESSION['patient_id']) && $_SESSION['patient_id'] != null) {
     
      include 'db/db.php';
        $numberofpending = 0;
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT *  FROM appointments where status = 'APPROVED' AND patient_id = ?");
        $stmt->bind_param('i', $_SESSION['patient_id']);
        $stmt->execute();
        $res =  $stmt->get_result();
        $approvedAppointment =  $res->num_rows;
    }


    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
          
        include 'db/db.php';
        $numberofpending = 0;
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SELECT *  FROM appointments where status = 'PENDING'");
        $stmt->execute();
        $result = $stmt->get_result();
        $numberofpending = $result->num_rows;
        $stmt->close();
        $conn->close();
?>
<style>
  /* ========== BASIC STYLES ========== */
  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    flex-wrap: wrap;
  }

  header .logo {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: bold;
    font-size: 1.25rem;
    color: #2563eb; /* blue-600 */
  }

  header .admin-badge {
    background: #2563eb;
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 4px;
  }

  nav {
    display: flex;
    align-items: center;
    gap: 24px;
    transition: all 0.3s ease-in-out;
  }

  nav a {
    text-decoration: none;
    color: #111827;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  nav a:hover {
    color: #2563eb;
  }

  .logout-btn {
    border: 1px solid #e5e7eb;
    padding: 4px 12px;
    border-radius: 6px;
    text-align: center;
  }

  .logout-btn:hover {
    background: #f3f4f6;
  }

  /* ========== MOBILE ONLY ========== */
  #menuToggle {
    display: none;
    font-size: 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
  }

  @media (max-width: 768px) {
    #menuToggle {
      display: block;
    }

    nav {
      flex-direction: column;
      width: 100%;
      margin-top: 10px;
      border-top: 1px solid #e5e7eb;
      padding-top: 10px;
      display: none; /* hidden by default */
    }

    nav.active {
      display: flex; /* show when toggled */
    }

    .logout-btn {
      width: 100%;
    }
  }
</style>
<header>
  <!-- Logo Section -->
  <div class="logo">
    <span>Northlens</span>
    <span class="admin-badge">ADMIN</span>
  </div>

  <!-- Hamburger button (mobile only) -->
  <button id="menuToggle">☰</button>

  <!-- Navigation -->
  <nav id="mainNav">
    <a href="index.php" id="dashboardLink" class="py-2 px-2 rounded-lg">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
         stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
          <path stroke-linecap="round" stroke-linejoin="round" 
        d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75h-5.25V15H9v6.75H3.75A.75.75 0 013 21V9.75z" />
      </svg>
      Dashboard
    </a>
    <a href="patients.php" id="patientLink" class="py-2 px-2 rounded-lg">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
          stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
        <path stroke-linecap="round" stroke-linejoin="round" 
              d="M18 18.75a6 6 0 10-12 0m12 0a6 6 0 10-12 0m12 0v.75a.75.75 0 01-.75.75h-10.5a.75.75 0 01-.75-.75v-.75m9-9a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      Patients
    </a>
    <a href="admin-appointment.php" id="appointmentLink" class="py-2 px-2 rounded-lg">
       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M4.5 6.75A2.25 2.25 0 016.75 4.5h10.5a2.25 2.25 0 012.25 2.25v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18.75v-12z" />
      </svg>
      Appointments
      <?php if ($numberofpending > 0) { echo '(' . $numberofpending . ')'; } ?>
    </a>
    <a href="analytics.php" id="analyticLink" class="py-2 px-2 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 3v18h18M9 17l3-3 4 4 4-8" />
      </svg>
      Analytics</a>
    <a href="listofservices.php" id="servicesLink" class="py-2 px-2 rounded-lg">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
          stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
        <path stroke-linecap="round" stroke-linejoin="round" 
              d="M3 10.5l1.5 6.75A2.25 2.25 0 006.75 19.5h2.25a2.25 2.25 0 002.25-2.25V12M21 10.5l-1.5 6.75A2.25 2.25 0 0117.25 19.5H15a2.25 2.25 0 01-2.25-2.25V12M3 10.5h18m-9 0V9.75a2.25 2.25 0 012.25-2.25h.75m-3 0h-.75A2.25 2.25 0 009.75 9.75V10.5" />
      </svg> 
      Services</a>
    <a href="crud/logout.php" class="logout-btn text-red-500">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
        stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1 text-red-500">
        <path stroke-linecap="round" stroke-linejoin="round"
        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15m3-3h-9m9 0l-3 3m3-3l-3-3" />
      </svg>
      Logout
    </a>
  </nav>
</header>

<script>
  document.getElementById('menuToggle').addEventListener('click', () => {
    document.getElementById('mainNav').classList.toggle('active');
  });
  
 document.addEventListener("DOMContentLoaded", function () {
    if (window.location.href.includes("index")) {
      const dashboardLink = document.getElementById("dashboardLink");

      if (dashboardLink) {
        dashboardLink.classList.add("bg-blue-500");
        dashboardLink.classList.add("text-white");
        const svg = dashboardLink.querySelector("svg");
        if (svg) {
          svg.classList.add("text-white");
        }
      }
    }
    else if (window.location.href.includes("patients")) {
      const dashboardLink = document.getElementById("patientLink");

      if (dashboardLink) {
        dashboardLink.classList.add("bg-blue-500");
        dashboardLink.classList.add("text-white");
        const svg = dashboardLink.querySelector("svg");
        if (svg) {
          svg.classList.add("text-white");
        }
      }
    }
    else if (window.location.href.includes("appointment")) {
      const dashboardLink = document.getElementById("appointmentLink");

      if (dashboardLink) {
        dashboardLink.classList.add("bg-blue-500");
        dashboardLink.classList.add("text-white");
        const svg = dashboardLink.querySelector("svg");
        if (svg) {
          svg.classList.add("text-white");
        }
      }
    }
    else if (window.location.href.includes("analytics")) {
      const dashboardLink = document.getElementById("analyticLink");

      if (dashboardLink) {
        dashboardLink.classList.add("bg-blue-500");
        dashboardLink.classList.add("text-white");
        const svg = dashboardLink.querySelector("svg");
        if (svg) {
          svg.classList.add("text-white");
        }
      }
    }
    else if (window.location.href.includes("services")) {
      const dashboardLink = document.getElementById("servicesLink");

      if (dashboardLink) {
        dashboardLink.classList.add("bg-blue-500");
        dashboardLink.classList.add("text-white");
        const svg = dashboardLink.querySelector("svg");
        if (svg) {
          svg.classList.add("text-white");
        }
      }
    }
  });
</script>
<?php
    } else {
?>
 <header class="header " style="padding: 20px 40px;">
  <div class="logo">
    <h1 class="brand">
      <img src="images/northlens-eye.png" width="60" alt="">
      <span>Northlens</span>
    </h1>
  </div>

  <button id="menuToggle" class="menu-toggle">☰</button>

  <nav id="navbar" class="nav-links">

    <a href="index.php" class="flex">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
         stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
          <path stroke-linecap="round" stroke-linejoin="round" 
        d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75h-5.25V15H9v6.75H3.75A.75.75 0 013 21V9.75z" />
      </svg>

      <span class="ml-1" style="margin-left: 5px">
        Home
      </span>
    </a>

    <a href="services.php" class="flex">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
          stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
        <path stroke-linecap="round" stroke-linejoin="round" 
              d="M3 10.5l1.5 6.75A2.25 2.25 0 006.75 19.5h2.25a2.25 2.25 0 002.25-2.25V12M21 10.5l-1.5 6.75A2.25 2.25 0 0117.25 19.5H15a2.25 2.25 0 01-2.25-2.25V12M3 10.5h18m-9 0V9.75a2.25 2.25 0 012.25-2.25h.75m-3 0h-.75A2.25 2.25 0 009.75 9.75V10.5" />
      </svg>

      <span class="ml-1" style="margin-left: 5px">
        Services
      </span>
    </a>

    <?php if (isset($_SESSION['user_id'])) { ?>
      <a class="flex gap-2" href="appointment.php">
       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M4.5 6.75A2.25 2.25 0 016.75 4.5h10.5a2.25 2.25 0 012.25 2.25v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18.75v-12z" />
      </svg>
       Appointment 
        <?php if ($approvedAppointment > 0) echo '(' . $approvedAppointment . ')'; ?>
      </a>
      <button id="profileButton" type="button" 
          class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
              stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
          </svg>
          <span>Profile</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
              stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
          </svg>
        </button>
        <div id="profileDropdown" 
            class="hidden lg:absolute right-4 top-14 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-20">
          <a href="profile.php" 
            class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
              <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.518-.875 3.318.924 2.443 2.442a1.724 1.724 0 001.066 2.574c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.875 1.518-.924 3.318-2.442 2.443a1.724 1.724 0 00-2.574 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.518.875-3.318-.924-2.443-2.442a1.724 1.724 0 00-1.066-2.574c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.875-1.518.924-3.318 2.442-2.443.96.553 2.148.247 2.574-1.066z" />
              <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            My Profile
          </a>
          
          <a href="notifications.php" 
            class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
              <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M7.5 8.25h9m-9 3h6m-7.5 4.5h10.5A2.25 2.25 0 0021 13.5V6.75A2.25 2.25 0 0018.75 4.5H5.25A2.25 2.25 0 003 6.75v10.5L6 15.75h12.75" />
            </svg>

            Message 
            <?php 
                if (isset($_SESSION['patient_id']) && $_SESSION['patient_id'] != null) {  
                  $countStmt = $conn->prepare("SELECT COUNT(*) AS unread FROM notifications WHERE patient_id = ? AND seen = 0");
                  $countStmt->bind_param("i", $_SESSION['patient_id']);
                  $countStmt->execute();
                  $countResult = $countStmt->get_result()->fetch_assoc();
                  $unreadCount = $countResult['unread'];
                  $countStmt->close();
                  if ($unreadCount > 0) {
                    echo '(' . $unreadCount . ')';
                  }
                }
            ?>

          </a>

          <a href="crud/logout.php" 
            class="flex items-center px-4 py-2 text-red-500 hover:bg-gray-100 hover:text-red-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
              <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15m3-3h-9m9 0l-3 3m3-3l-3-3" />
            </svg>
            Logout
          </a>
        </div>
    <?php } else { ?>
      <a href="login.php" class="flex text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
         stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1 text-blue-600">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15m3-3h-9m9 0l-3 3m3-3l-3-3" />
    </svg>
      <span class="ml-1 text-blue-600" style="margin-left: 5px">
        Login
      </span>
    </a>
    <?php } ?>
  </nav>
</header>

<style>
/* 🔹 Layout */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #ddd;
  position: relative;
  background: white;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.3rem;
  font-weight: bold;
  color: #2563eb; /* blue-600 */
}

/* 🔹 Desktop nav */
.nav-links {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.nav-links a {
  text-decoration: none;
  color: #333;
  font-size: 0.95rem;
  transition: color 0.2s;
}

.nav-links a:hover {
  color: #2563eb;
}

/* 🔹 Mobile menu button */
.menu-toggle {
  display: none;
  font-size: 1.6rem;
  background: none;
  border: none;
  cursor: pointer;
}

/* 🔹 Responsive behavior */
@media (max-width: 768px) {
  .menu-toggle {
    display: block;
  }

  .nav-links {
    display: none;
    flex-direction: column;
    background: white;
    position: absolute;
    top: 100%;
    right: 0;
    left: 0;
    padding: 1rem;
    border-top: 1px solid #ddd;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  }

  .nav-links.active {
    display: flex;
  }

  .nav-links a {
    padding: 0.5rem 0;
  }
}
</style>

<script>
const toggleBtn = document.getElementById('menuToggle');
const nav = document.getElementById('navbar');

toggleBtn.addEventListener('click', () => {
  nav.classList.toggle('active');
});

 const profileBtn = document.getElementById('profileButton');
  const dropdown = document.getElementById('profileDropdown');

  profileBtn.addEventListener('click', () => {
    dropdown.classList.toggle('hidden');
  });

  // Close dropdown if clicked outside
  document.addEventListener('click', (e) => {
    if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
</script>
<?php
    }
?>