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

// Process transfer
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient_account = trim($_POST['recipient_account']);
    $amount = floatval($_POST['amount']);
    $notes = trim($_POST['notes']);

    // Validate
    if ($amount <= 0) {
        $error = "Amount must be positive";
    } elseif ($amount > $current_balance) {
        $error = "Insufficient balance";
    } else {
        // Check if recipient exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE account_number = ?");
        $stmt->bind_param("s", $recipient_account);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $recipient = $result->fetch_assoc();
            $recipient_id = $recipient['id'];

            // Begin transaction
            $conn->begin_transaction();
            try {
                // Deduct from sender
                $conn->query("UPDATE users SET balance = balance - $amount WHERE user_id = $user_id");
                // Add to recipient
                $conn->query("UPDATE users SET balance = balance + $amount WHERE user_id = $recipient_id");
                // Record transaction
                $stmt = $conn->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, notes) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iids", $user_id, $recipient_id, $amount, $notes);
                $stmt->execute();

                $conn->commit();
                $success = "Transfer successful!";
                $current_balance -= $amount; // Update local balance
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Transfer failed: " . $e->getMessage();
            }
        } else {
            $error = "Recipient account not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money | savewise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<a href="dashboard.php" class="fixed top-4 left-4 text-gray-800 hover:text-gray-900"><i class="fa-solid fa-arrow-left"></i></a>
    
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-center space-x-3">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-exchange-alt text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Transfer Money</h2>
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
                            <label for="recipient_account" class="block text-sm font-medium text-gray-700 mb-1">
                                Recipient Account Number
                            </label>
                            <input type="text" 
                                   id="recipient_account" 
                                   name="recipient_account" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                Amount (₦)
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   id="amount" 
                                   name="amount" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Notes (Optional)
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
                        </div>
                        
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-600 mb-1">Current Balance</p>
                            <p class="text-xl font-bold text-blue-600">₦<?= number_format($current_balance, 2) ?></p>
                        </div>
                        
                        <button type="submit" 
                                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Confirm Transfer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>