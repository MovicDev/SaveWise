<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    redirect('signin.php');
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user data
$user = get_user_account($conn, $user_id);
if (!$user) {
    redirect('logout.php');
}

// Process notification settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_notifications'])) {
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE users SET email_notifications = ?, sms_notifications = ? WHERE id = ?");
        $stmt->bind_param("iii", $email_notifications, $sms_notifications, $user_id);
        
        if ($stmt->execute()) {
            $success = 'Notification settings updated';
            log_activity($conn, $user_id, 'notification_settings_update');
        } else {
            $error = 'Failed to update settings';
        }
    }
    
    // Get updated user data
    $user = get_user_account($conn, $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | SaveWise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        savewise: {
                            primary: '#2563eb',
                            secondary: '#1e40af',
                            accent: '#3b82f6'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <a href="dashboard.php" class="fixed top-4 left-4 text-gray-800 hover:text-gray-900 transition-colors duration-300"><i class="fa-solid fa-arrow-left"></i></a>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-cog mr-3 text-savewise-primary"></i>
                    Account Settings
                </h1>
                <p class="text-gray-600 mt-2">Manage your SaveWise account preferences and security</p>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-savewise-accent/10 text-savewise-primary mr-3">
                        <i class="fas fa-bell text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Notification Preferences</h2>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700"><?= $error ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700"><?= $success ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="update_notifications" value="1">
                    
                    <div class="space-y-4">
                        <!-- Email Notifications -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="emailNotifications" class="block text-sm font-medium text-gray-700">Email Notifications</label>
                                <p class="text-sm text-gray-500 mt-1">Receive account activity alerts via email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="emailNotifications" name="email_notifications" 
                                       class="sr-only peer" <?= ($user['email_notifications'] ?? 1) ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-savewise-primary"></div>
                            </label>
                        </div>
                        
                        <!-- SMS Notifications -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="smsNotifications" class="block text-sm font-medium text-gray-700">SMS Notifications</label>
                                <p class="text-sm text-gray-500 mt-1">Receive important alerts via text message</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="smsNotifications" name="sms_notifications" 
                                       class="sr-only peer" <?= ($user['sms_notifications'] ?? 0) ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-savewise-primary"></div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-savewise-primary hover:bg-savewise-secondary text-white font-medium rounded-lg transition duration-200">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Settings -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-3">
                        <i class="fas fa-shield-alt text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Security Settings</h2>
                </div>

                <div class="space-y-3">
                    <!-- Change Password -->
                    <a href="change-password.php" class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg transition duration-150">
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-key"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Change Password</h3>
                                <p class="text-sm text-gray-500">Update your account password</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>

                    <!-- Two-Factor Authentication -->
                    <a href="two-factor.php" class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg transition duration-150">
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Two-Factor Authentication</h3>
                                <p class="text-sm text-gray-500">Add an extra layer of security</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Enabled</span>
                            <i class="fas fa-chevron-right text-gray-400 ml-2"></i>
                        </div>
                    </a>

                    <!-- Linked Devices -->
                    <a href="linked-devices.php" class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg transition duration-150">
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg bg-purple-100 text-purple-600 mr-4">
                                <i class="fas fa-laptop"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Linked Devices</h3>
                                <p class="text-sm text-gray-500">Manage your trusted devices</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </div>
            </div>

            <!-- Preferences -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-3">
                        <i class="fas fa-sliders-h text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Preferences</h2>
                </div>

                <div class="space-y-4">
                    <!-- Default Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Default Currency</label>
                        <select id="currency" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-savewise-primary focus:ring focus:ring-savewise-primary/50 disabled:bg-gray-100 disabled:text-gray-500" disabled>
                            <option>Nigerian Naira (NGN)</option>
                            <option>US Dollar (USD)</option>
                            <option>Euro (EUR)</option>
                            <option>British Pound (GBP)</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Currency preferences coming soon</p>
                    </div>

                    <!-- Language -->
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                        <select id="language" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-savewise-primary focus:ring focus:ring-savewise-primary/50">
                            <option>English</option>
                            <option>Spanish</option>
                            <option>French</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-red-200">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-3">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Danger Zone</h2>
                </div>

                <div class="p-4 bg-red-50 rounded-lg">
                    <h3 class="font-medium text-red-800">Close Account</h3>
                    <p class="text-sm text-red-600 mt-1">Permanently delete your SaveWise account and all associated data.</p>
                    <button class="mt-3 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition duration-200" onclick="document.getElementById('close-account-modal').classList.remove('hidden')">
                        Close Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Account Modal -->
    <div id="close-account-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg font-semibold text-gray-900">Close Your Account?</h3>
                    <button onclick="document.getElementById('close-account-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600">This action cannot be undone. All your account data including transaction history will be permanently deleted.</p>
                    <p class="mt-3 font-medium text-sm text-gray-700">Please confirm your password to continue:</p>
                    <input type="password" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-savewise-primary focus:ring focus:ring-savewise-primary/50" placeholder="Enter your password">
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="document.getElementById('close-account-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-200">
                        Cancel
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition duration-200">
                        Permanently Close Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>