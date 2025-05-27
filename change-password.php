<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('signin.php');
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    
    if (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $success = "Password updated successfully";
            log_activity($conn, $user_id, 'password_change');
            
            // Send email notification
            $user_email = get_user_account($conn, $user_id)['email'];
            $subject = "Password Changed - SwiftBank";
            $message = "Your password was recently changed. If you didn't make this change, please contact support immediately.";
            send_email($user_email, $subject, $message);
        } else {
            $error = "Failed to update password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | savewise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <a href="dashboard.php" class="fixed top-4 left-4 text-gray-800 hover:text-gray-900"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-key text-lg"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Change Password</h2>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php if ($error): ?>
                        <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                            <?= $success ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="passwordForm" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Current Password
                            </label>
                            <input type="password" id="current_password" name="current_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                                New Password
                            </label>
                            <input type="password" id="new_password" name="new_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <div class="mt-1 h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                                <div id="passwordStrengthBar" class="h-full transition-all duration-300"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters with uppercase, lowercase, number, and special character</p>
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirm New Password
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <p id="passwordMatch" class="mt-1 text-xs"></p>
                        </div>
                        
                        <div>
                            <button type="submit" 
                                    class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
            
            const width = (strength / 5) * 100;
            strengthBar.style.width = width + '%';
            
            if (strength <= 2) {
                strengthBar.className = 'h-full transition-all duration-300 bg-red-500';
            } else if (strength <= 4) {
                strengthBar.className = 'h-full transition-all duration-300 bg-yellow-500';
            } else {
                strengthBar.className = 'h-full transition-all duration-300 bg-green-500';
            }
        });
        
        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const confirm = this.value;
            const newPass = document.getElementById('new_password').value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirm && newPass !== confirm) {
                matchText.textContent = 'Passwords do not match';
                matchText.className = 'mt-1 text-xs text-red-500';
            } else if (confirm) {
                matchText.textContent = 'Passwords match';
                matchText.className = 'mt-1 text-xs text-green-500';
            } else {
                matchText.textContent = '';
            }
        });
    </script>
</body>
</html>