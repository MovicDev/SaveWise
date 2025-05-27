<?php 
include 'includes/config.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SaveWise</title>
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
       <a href="index.php" class="flex items-center justify-center py-4 bg-gray-100 text-gray-800 text-sm font-medium">Back to Home</a>
    
    <main class="flex-grow flex items-center py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto">
                <div class="bg-white rounded-xl shadow-md overflow-hidden p-8">
                    <div class="text-center mb-8">
                        <div class="flex justify-center mb-4">
                            <i class="fas fa-wallet text-4xl text-primary"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-dark mb-2">Create Your Account</h1>
                        <p class="text-gray-600">Start tracking your finances in minutes</p>
                    </div>

                    <?php
                   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check for duplicate username or email
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $name, $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">Username or email already exists. Please choose another.</p>
                    </div>
                </div>
            </div>';
    } else {          
    $account_number = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, account_number) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $account_number);
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $account_number = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
            $acc_stmt = $conn->prepare("INSERT INTO accounts (user_id, account_number, balance) VALUES (?, ?, 0.00)");
            $acc_stmt->bind_param("is", $user_id, $account_number);
            $acc_stmt->execute();
            $acc_stmt->close();
            echo '<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">Account created! <a href="signin.php" class="font-medium text-green-700 underline">signin here</a></p>
                        </div>
                    </div>
                </div>';
        } else {
            echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">Error: ' . $conn->error . '</p>
                        </div>
                    </div>
                </div>';
        }
    }
    $check->close();
}
                    ?>

                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    required 
                                    class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 border"
                                    placeholder="John Doe">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    required 
                                    class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 border"
                                    placeholder="you@example.com">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-3 px-4 border"
                                    placeholder="••••••••">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Minimum 8 characters</p>
                        </div>

                        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary font-medium">
                            Create Account
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </form>

                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">Already have an account?</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="signin.php" class="w-full flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-dark hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary font-medium">
                                Sign In
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>