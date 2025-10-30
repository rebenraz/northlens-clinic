<?php
    include 'db/db.php';

    $userId = $_SESSION['user_id'];

    $currentPass = '';
    $confirmPass = '';
    $newPass = '';
    $passdidnotmatch = false;
    $sucessChange = false;
    $currentpassdidnotmatch = false;

    if (isset($_GET['updateInfo'])) {
        $fname = $_POST['firstName'];
        $lname = $_POST['lastName'];

        $stmt = $conn->prepare('UPDATE users set firstname= ?, lastname = ?  WHERE id = ?');
        $stmt->bind_param('ssi', $fname, $lname, $userId);
        $stmt->execute();
        if ($_POST['currentPassword'] != '' && $_POST['newPassword'] != '' && $_POST['confirmPassword'] != '') {
            $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $currentPass = $_POST['currentPassword'];
            $confirmPass = $_POST['confirmPassword'];
            $newPass = $_POST['newPassword'];
            if ($_POST['newPassword'] != $_POST['confirmPassword']) {
                $passdidnotmatch = true;
            } else if (password_verify($user['password'], $currentPass)) {
                $currentpassdidnotmatch = true;
            } else {
                $hashed_password = password_hash($newPass, PASSWORD_DEFAULT);
                $pass = $_POST['currentPassword'];
                $stmt = $conn->prepare('UPDATE users set password = ? WHERE id = ?');
                $stmt->bind_param('si', $hashed_password, $userId);
                $stmt->execute();
                $currentPass = '';
                $confirmPass = '';
                $newPass = '';
            }
        }
    
    }

    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
?>

<form id="profileForm" class="space-y-6 px-4 bg-white py-2" method="POST" action="?settings&updateInfo">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="firstName" class="block text-sm font-medium text-slate-700 mb-1">First name</label>
            <input id="firstName" value="<?php echo  $user['firstname'] ?>"  name="firstName" type="text" autocomplete="given-name" required
                class="block w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500" />
        </div>


        <div>
            <label for="lastName" class="block text-sm font-medium text-slate-700 mb-1">Last name</label>
            <input id="lastName" name="lastName" value="<?php echo  $user['lastname'] ?>"type="text" autocomplete="family-name" required
            class="block w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500" />
        </div>
    </div>


    <div>
        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input id="email" name="email" type="email" value="<?php echo  $user['email'] ?>" autocomplete="email" disabled 
        class="block w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500" />
    </div>


    <fieldset class="pt-4 border-t border-slate-100">
    <legend class="text-sm font-medium text-slate-700">Change password</legend>
    <p class="text-sm text-slate-500 mt-1">Only fill these fields if you want to change your password.</p>


    <div class="mt-4 space-y-4">
        <div>
            <label for="currentPassword" class="block text-sm font-medium text-slate-700 mb-1">Current password</label>
            <div class="relative">
                <input id="currentPassword" value="<?php echo $currentPass ?>" name="currentPassword" type="password" autocomplete="current-password"
                    class="block w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500" />
                <button type="button" class="absolute inset-y-0 right-2 px-2 flex items-center" onclick="toggleVisibility('currentPassword', this)">
                <span class="sr-only">Toggle password visibility</span>
                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                </button>
                <?php 
                    if ($currentpassdidnotmatch) {
                ?>
                    <span class="text-red-500">Current Password is incorrect</span>
                <?php
                    }
                    if ($sucessChange) {
                ?>
                    <span class="text-green-500">Password Successfully Changed</span>
                <?php
                    }
                ?>
            </div>
        </div>


        <div>
            <label for="newPassword" class="block text-sm font-medium text-slate-700 mb-1">New password</label>
            <div class="relative">
                <input value="<?php echo $newPass ?>" id="newPassword" name="newPassword" type="password" autocomplete="new-password"
                class="block w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500" />
                <button type="button" class="absolute inset-y-0 right-2 px-2 flex items-center" onclick="toggleVisibility('newPassword', this)">
                <span class="sr-only">Toggle password visibility</span>
                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                </button>
            </div>
            <p id="pwdHelp" class="mt-1 text-xs text-slate-500">Use 8+ characters with a mix of letters and numbers.</p>
        </div>


        <div>
            <label for="confirmPassword"class="block text-sm font-medium text-slate-700 mb-1">Confirm new password</label>
            <div class="relative">
                <input id="confirmPassword"  value="<?php echo $confirmPass ?>"  name="confirmPassword" type="password" autocomplete="new-password"
                class="block w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500" />
                <button type="button" class="absolute inset-y-0 right-2 px-2 flex items-center" onclick="toggleVisibility('confirmPassword', this)">
                <span class="sr-only">Toggle password visibility</span>
                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                </button>
                <?php 
                    if ($passdidnotmatch) {
                ?>
                    <span class="text-red-500">Password didnot match</span>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </div>
</form>

<script>
function toggleVisibility(inputId, btn) {
const input = document.getElementById(inputId);
if (!input) return;
if (input.type === 'password') {
input.type = 'text';
btn.setAttribute('aria-pressed', 'true');
} else {
input.type = 'password';
btn.setAttribute('aria-pressed', 'false');
}
}


function resetForm() {
document.getElementById('profileForm').reset();
document.getElementById('result').textContent = '';
document.getElementById('confirmHelp').classList.add('hidden');
}


function handleSubmit() {
const firstName = document.getElementById('firstName').value.trim();
const lastName = document.getElementById('lastName').value.trim();
const email = document.getElementById('email').value.trim();
const newPassword = document.getElementById('newPassword').value;
const confirmPassword = document.getElementById('confirmPassword').value;


// simple client-side validation for example
if (newPassword || confirmPassword) {
if (newPassword.length < 8) {
document.getElementById('result').innerHTML = '<span class="text-rose-600">New password must be at least 8 characters.</span>';
return;
}
if (newPassword !== confirmPassword) {
document.getElementById('confirmHelp').classList.remove('hidden');
return;
}
}


// Normally here you'd send data to the server (fetch/axios) — example result display only
document.getElementById('confirmHelp').classList.add('hidden');
document.getElementById('result').innerHTML = '<span class="text-green-600">Profile updated successfully (demo).</span>';


// clear password fields after success for security
document.getElementById('currentPassword').value = '';
document.getElementById('newPassword').value = '';
document.getElementById('confirmPassword').value = '';
}
</script>