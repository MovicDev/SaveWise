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
    redirect('logout.php'); // Invalid user session
}

// Process profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = sanitize_input($_POST['name']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Validate inputs
    if (empty($name)) {
        $error = 'Name is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $name, $email, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $name;
                $success = 'Profile updated successfully';
                $user = get_user_account($conn, $user_id); // Refresh user data
                log_activity($conn, $user_id, 'profile_update');
            } else {
                $error = 'Failed to update profile';
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = 'Email already exists';
            } else {
                $error = 'Database error: ' . $e->getMessage();
                error_log("Profile Update Error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | SaveWise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981',
                        dark: '#1F2937',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    </style>
</head>
<body class="font-sans bg-gray-50 min-h-screen flex flex-col">
    <a href="dashboard.php" class="fixed top-4 left-4 text-gray-500 hover:text-gray-700 transition-colors duration-300"></a>
    
    <main class="flex-grow py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Profile Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                    <!-- Profile Header -->
                    <div class="bg-gradient-to-r from-primary to-primary/90 text-white p-8 text-center">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['username']) ?>&background=ffffff&color=4F46E5&size=120" 
                             alt="Profile" class="w-24 h-24 rounded-full border-4 border-white mx-auto mb-4">
                        <h2 class="text-2xl font-bold"><?= htmlspecialchars($user['username']) ?></h2>
                        <p class="text-white/90">Member since <?= date('F Y', strtotime($user['created_at'])) ?></p>
                    </div>
                    
                    <!-- Profile Content -->
                    <div class="p-6 md:p-8">
                        <!-- Messages -->
                        <?php if ($error): ?>
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
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
                            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
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
                        
                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Profile Information -->
                            <div>
                                <h3 class="text-xl font-bold mb-6 flex items-center">
                                    <i class="fas fa-user-circle text-primary mr-2"></i>
                                    Profile Information
                                </h3>
                                <form method="POST">
                                    <input type="hidden" name="update_profile" value="1">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" name="name" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3 border"
                                               value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                        <input type="email" name="email" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3 border"
                                               value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                    <button type="submit" class="bg-primary text-white py-2 px-6 rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                        Update Profile
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Account Details -->
                            <div>
                                <h3 class="text-xl font-bold mb-6 flex items-center">
                                    <i class="fas fa-wallet text-primary mr-2"></i>
                                    Account Details
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-primary">
                                        <p class="text-sm text-gray-500">Account Number</p>
                                        <p class="font-semibold"><?= htmlspecialchars($user['account_number']) ?></p>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-secondary">
                                        <p class="text-sm text-gray-500">Account Type</p>
                                        <p class="font-semibold">Personal Checking</p>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-primary">
                                        <p class="text-sm text-gray-500">Current Balance</p>
                                        <p class="font-semibold"><?= format_currency($user['balance']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Security Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 md:p-8">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <i class="fas fa-shield-alt text-primary mr-2"></i>
                            Security
                        </h3>
                        
                        <div class="divide-y divide-gray-200">
                            <a href="change-password.php" class="py-4 flex justify-between items-center hover:bg-gray-50 px-2 rounded-lg transition">
                                <div class="flex items-center">
                                    <i class="fas fa-key text-primary mr-4"></i>
                                    <span>Change Password</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>
                            
                            <a href="signin-history.php" class="py-4 flex justify-between items-center hover:bg-gray-50 px-2 rounded-lg transition">
                                <div class="flex items-center">
                                    <i class="fas fa-history text-primary mr-4"></i>
                                    <span>signin History</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>