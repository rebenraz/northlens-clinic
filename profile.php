<?php 
    session_start();
    
    include 'db/db.php';
    $firstname = "";
    $lastname = "";
    $email = "";

    if (isset($_GET['changePassword'])) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];
            $fname = $_POST['first_name'];
            $lname = $_POST['last_name'];
            $cpass = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            $patient_id = $_SESSION['patient_id'];

            if (empty($fname) || empty($lname)) {
                echo "<script>alert('First name and last name are required!'); history.back();</script>";
                exit;
            }
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user) {
                echo "<script>alert('User not found.'); history.back();</script>";
                exit;
            }
            
            if (!(empty($cpass) && empty($new_password) && empty($confirm_password))) {
                if (empty($cpass) || empty($new_password) || empty($confirm_password)) {
                    echo "<script>alert('Please fill in all password fields.'); window.location.href='profile.php?filluppassword';</script>";
                    exit;
                }

                if (!password_verify($cpass, $user['password'])) {
                    echo "<script>alert('Current password is incorrect.'); window.location.href='profile.php?currentpassincorrect';</script>";
                    exit;
                }

                if ($new_password !== $confirm_password) {
                    echo "<script>alert('New password and confirm password do not match.'); window.location.href='profile.php?passwordnotmatch';</script>";
                    exit;
                }
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedPassword, $userId);
                $stmt->execute();
                $stmt->close();
            }


            $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ? WHERE id = ?");
            $stmt->bind_param("ssi", $fname, $lname, $userId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE patients SET firstname = ?, lastname = ? WHERE id = ?");
            $stmt->bind_param("ssi", $fname, $lname, $patient_id);
            $stmt->execute();
            $stmt->close();

            echo "<script>window.location.href='profile.php?passwordchanged';</script>";
            exit;
        }
    }
    
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $firstname =  $user['firstname'];
        $lastname =  $user['lastname'];
        $email =  $user['email'];
    } else {
        return;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Profile</title>
</head>
<body>
    <?php include 'header/header.php'; ?>
    <div class="min-h-screen bg-gray-100 p-6 flex items-start justify-center">
        <div class="w-full max-w-4xl bg-white shadow-xl rounded-lg overflow-hidden">
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-user-circle text-blue-600 mr-2"></i> Profile Settings
                </h1>
                <p class="text-sm text-gray-500">Update your account information and manage your security.</p>
            </div>

            <form action="/profile.php?changePassword" method="POST" enctype="multipart/form-data" class="divide-y divide-gray-200">

                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Basic Information</h2>
                    <div class="flex flex-col md:flex-row md:space-x-8">

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:w-full">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input 
                                    required
                                    type="text" 
                                    id="first_name" 
                                    name="first_name" 
                                    value="<?php echo $firstname; ?>"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input 
                                    required
                                    type="text" 
                                    id="last_name" 
                                    name="last_name" 
                                    value="<?php echo $lastname; ?>"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                            </div>
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="<?php echo $email; ?>"
                                    readonly 
                                    class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none sm:text-sm cursor-not-allowed"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Change Password</h2>
                    <p class="text-sm text-gray-500 mb-4">Leave these fields blank if you don't want to change your password.</p>
                    <?php
                        if (isset($_GET['filluppassword'])) {
                    ?>
                        <p class="text-red-500">Please fill up password</p>
                    <?php
                        }
                        if (isset($_GET['passwordchanged'])) {
                    ?>
                        <p class="text-green-500">Profile Updated !</p>
                    <?php
                        }
                    ?>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        
                    <div class="relative">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                            Current Password
                        </label>
                        <input type="password" id="current_password" name="current_password" 
                            placeholder="Enter current password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-gray-700 
                                focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        />
                        <!-- Eye icon -->
                        <button type="button" onclick="togglePassword('current_password', this)" 
                            class="absolute inset-y-0 right-3 flex items-center mt-6 text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                        <?php
                            if (isset($_GET['currentpassincorrect'])) {
                        ?>
                            <p class="text-red-500">Current password is incorrect</p>
                        <?php
                            }
                        ?>
                    </div>

                    <!-- New Password -->
                    <div class="relative">
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                        New Password
                        </label>
                        <input type="password" id="new_password" name="new_password" 
                        placeholder="Enter new password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-gray-700 
                                focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                         />
                        <button type="button" onclick="togglePassword('new_password', this)" 
                        class="absolute inset-y-0 right-3 flex items-center mt-6 text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        </button>
                        <?php
                            if (isset($_GET['passwordnotmatch'])) {
                        ?>
                            <p class="text-red-500">Password doesnt match</p>
                        <?php
                            }
                        ?>
                    </div>

                    <!-- Confirm Password -->
                    <div class="relative">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm New Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                            placeholder="Confirm new password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-gray-700 
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                         />
                        <button type="button" onclick="togglePassword('confirm_password', this)" 
                        class="absolute inset-y-0 right-3 flex items-center mt-6 text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        </button>
                    </div>

                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 text-right">
                    <button 
                        type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                    >
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector("svg");

            if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" 
                d="M3.98 8.223a.75.75 0 011.06.02L6.94 10.5m10.12 0a.75.75 0 011.06-.02 9.75 9.75 0 01-14.12 0 .75.75 0 01.02-1.06l12-12zM9.53 9.53a3 3 0 014.94 3.94" />`;
            } else {
            input.type = "password";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" 
                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" 
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
            }
        }
    </script>
</body>
</html>