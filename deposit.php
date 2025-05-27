<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and include config
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

// Initialize variables
$error = '';
$success = '';
$current_balance = 0;
$user_id = $_SESSION['user_id'];

// Get current balance
$stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$current_balance = $user['balance'];

// Process deposit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $description = trim(strip_tags($_POST['description'] ?? ''));
    
    // Validate amount
    if ($amount <= 0) {
        $error = "Deposit amount must be greater than zero.";
    } elseif ($amount > 1000000) { // Set reasonable deposit limit
        $error = "Maximum deposit amount is ₦1,000,000 per transaction.";
    } else {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Update user balance
            $update = $conn->prepare("UPDATE users SET balance = balance + ? WHERE user_id = ?");
            $update->bind_param("di", $amount, $user_id);
            $update->execute();
            

            $account_stmt = $conn->prepare("SELECT account_id FROM accounts WHERE user_id = ?");
$account_stmt->bind_param("i", $user_id);
$account_stmt->execute();
$account_result = $account_stmt->get_result();
$account = $account_result->fetch_assoc();
$account_id = $account['account_id'] ?? null;

if (!$account_id) {
    throw new Exception("No account found for user.");
}
           $transaction = $conn->prepare("INSERT INTO transactions 
    (account_id, sender_id, receiver_id, amount, notes, status) 
    VALUES (?, 0, ?, ?, ?, 'completed')");
$transaction->bind_param("iids", $account_id, $user_id, $amount, $description);
$transaction->execute();
            
            // Commit transaction
            $conn->commit();
            
            // Update local balance
            $current_balance += $amount;
            $success = "Successfully deposited ₦" . number_format($amount, 2);
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Transaction failed: " . $e->getMessage();
            error_log("Deposit Error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Deposit | SaveWise</title>
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
    <a href="dashboard.php" class="fixed top-4 left-4 text-gray-500 hover:text-gray-700 transition duration-300"><i class="fa-solid fa-arrow-left"></i></a>
    <main class="flex-grow py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <!-- Deposit Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 transition hover:shadow-lg">
                    <div class="p-6 md:p-8">
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-coins text-2xl"></i>
                            </div>
                            <h1 class="text-2xl font-bold text-dark mb-2">Make a Deposit</h1>
                            <p class="text-gray-600">Add funds to your SaveWise account</p>
                        </div>
                        
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
                        
                        <!-- Current Balance -->
                        <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center mb-8">
                            <span class="text-gray-600">Current Balance:</span>
                            <span class="text-xl font-semibold text-dark">₦<?= number_format($current_balance, 2) ?></span>
                        </div>
                        
                        <!-- Deposit Form -->
                        <form method="POST" class="space-y-6">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">₦</span>
                                    </div>
                                    <input type="number" 
                                           id="amount" 
                                           name="amount" 
                                           step="0.01" 
                                           min="0.01" 
                                           max="1000000" 
                                           required
                                           class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                           placeholder="0.00">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Minimum deposit: ₦100</p>
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="3"
                                          class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                          placeholder="e.g., Cash deposit, check deposit, etc."></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary font-medium">
                                <i class="fas fa-plus-circle mr-2"></i> Complete Deposit
                            </button>
                        </form>
                    </div>
                    
                    <!-- Deposit Information -->
                    <div class="bg-gray-50 p-6 md:p-8">
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="font-bold mb-4 flex items-center">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                    Deposit Info
                                </h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                        <span>Instant credit</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                        <span>No fees</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                        <span>Secure transactions</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold mb-4 flex items-center">
                                    <i class="fas fa-clock text-primary mr-2"></i>
                                    Processing Times
                                </h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <i class="fas fa-circle text-xs text-blue-500 mt-2 mr-2"></i>
                                        <span>Cash: Immediate</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-circle text-xs text-blue-500 mt-2 mr-2"></i>
                                        <span>Checks: 1 business day</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-circle text-xs text-blue-500 mt-2 mr-2"></i>
                                        <span>Wire transfers: Same day</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Deposits -->
                <?php
                $recent_stmt = $conn->prepare("SELECT amount, date, notes FROM transactions 
                                             WHERE receiver_id = ? AND sender_id = 0 
                                             ORDER BY date DESC LIMIT 5");
                $recent_stmt->bind_param("i", $user_id);
                $recent_stmt->execute();
                $recent_deposits = $recent_stmt->get_result();
                
                if ($recent_deposits->num_rows > 0): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 md:p-8">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <i class="fas fa-history text-primary mr-2"></i>
                            Recent Deposits
                        </h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php while ($deposit = $recent_deposits->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('M j, Y g:i A', strtotime($deposit['date'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                            +₦<?= number_format($deposit['amount'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= !empty($deposit['notes']) ? htmlspecialchars($deposit['notes']) : 'Deposit' ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script>
        // Client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const amountInput = document.getElementById('amount');
            const amount = parseFloat(amountInput.value);
            
            if (amount <= 0) {
                e.preventDefault();
                alert('Please enter a valid deposit amount.');
                amountInput.focus();
            }
        });
        
        // Format amount as user types
        document.getElementById('amount').addEventListener('blur', function(e) {
            const value = parseFloat(e.target.value);
            if (!isNaN(value)) {
                e.target.value = value.toFixed(2);
            }
        });
    </script>
</body>
</html>