<?php
include 'includes/config.php';
require_once __DIR__ . '/includes/functions.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$error = "";
$success = "";

// Fetch user balance
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$current_balance = $user['balance'];

// Process withdrawal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $notes = trim(strip_tags($_POST['notes'] ?? 'Cash withdrawal'));

    if ($amount <= 0) {
        $error = "Amount must be positive";
    } elseif ($amount > $current_balance) {
        $error = "Insufficient balance";
    } else {
        // Get user's account_id
        $account_stmt = $conn->prepare("SELECT account_id FROM accounts WHERE user_id = ?");
        $account_stmt->bind_param("i", $user_id);
        $account_stmt->execute();
        $account_result = $account_stmt->get_result();
        $account = $account_result->fetch_assoc();
        $account_id = $account['account_id'] ?? null;

        if (!$account_id) {
            $error = "No account found for user.";
        } else {
            $conn->begin_transaction();
            try {
                // Deduct from balance
                $conn->query("UPDATE users SET balance = balance - $amount WHERE user_id = $user_id");
                // Record transaction (withdrawal is a transfer to "Bank")
                $stmt = $conn->prepare("INSERT INTO transactions (account_id, sender_id, receiver_id, amount, notes) VALUES (?, ?, 0, ?, ?)");
                $stmt->bind_param("iids", $account_id, $user_id, $amount, $notes);
                $stmt->execute();

                $conn->commit();
                $success = "Withdrawal successful!";
                $current_balance -= $amount;
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Withdrawal failed: " . $e->getMessage();
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
    <title>Withdraw Money | savewise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <a href="dashboard.php" class="fixed top-4 left-4 text-gray-700 hover:text-gray-900"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-center space-x-3">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Withdraw Money</h2>
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
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                Amount (₦)
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">₦</span>
                                </div>
                                <input type="number" 
                                       step="0.01" 
                                       id="amount" 
                                       name="amount" 
                                       required
                                       class="block w-full pl-8 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                       placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500">NGN</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Notes (Optional)
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                      placeholder="e.g. ATM withdrawal"></textarea>
                        </div>
                        
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-600 mb-1">Available Balance</p>
                            <p class="text-xl font-bold text-blue-600">₦<?= number_format($current_balance, 2) ?></p>
                        </div>
                        
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-money-bill-wave mr-2"></i> Confirm Withdrawal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-format amount input
        document.getElementById('amount').addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    </script>
</body>
</html>