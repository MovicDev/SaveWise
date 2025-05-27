<?php 
include 'includes/config.php';
require_once __DIR__ . '/includes/functions.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, account_number, balance FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | savewise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Mobile Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg lg:hidden">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="#" class="font-bold text-xl flex items-center">
                <i class="fas fa-piggy-bank mr-2"></i>savewise
            </a>
            <button id="mobileMenuButton" class="text-white focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </nav>

    <div class="flex flex-col lg:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="bg-white shadow-lg w-full lg:w-64 fixed lg:static inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out z-50" id="sidebar">
            <div class="p-4 flex flex-col h-full">
                <div class="flex items-center justify-between mb-8 lg:mb-10">
                    <a href="#" class="font-bold text-xl text-blue-600 flex items-center">
                        <i class="fas fa-piggy-bank mr-2"></i>SaveWise
                    </a>
                    <button id="closeSidebar" class="lg:hidden text-gray-500 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="text-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-semibold text-blue-600">
        <?= strtoupper(substr($user['username'], 0, 1)) ?>
    </span>
                    </div>
                    <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($user['username']) ?></h3>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                </div>

                <nav class="flex-1">
                    <ul class="space-y-2">
                        <li>
                            <a href="dashboard.php" class="flex items-center px-4 py-3 rounded-lg bg-blue-50 text-blue-600 font-medium">
                                <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="transfer.php" class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-exchange-alt mr-3"></i> Transfer Money
                            </a>
                        </li>
                        <li>
                            <a href="deposit.php" class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-plus-circle mr-3"></i> Deposit
                            </a>
                        </li>
                        <li>
                            <a href="withdraw.php" class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-minus-circle mr-3"></i> Withdraw
                            </a>
                        </li>
                        <li>
                            <a href="profile.php" class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-user mr-3"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a href="settings.php" class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-cog mr-3"></i> Settings
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="mt-auto pt-4 border-t border-gray-200">
                    <a href="logout.php" class="flex items-center px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64">
            <div class="container mx-auto px-4 py-6">
                <!-- Account Summary -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Account Summary</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Account Holder</p>
                                <p class="font-medium"><?= htmlspecialchars($user['username']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Account Number</p>
                                <p class="font-medium"><?= htmlspecialchars($user['account_number']) ?></p>
                            </div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-blue-600">Available Balance</p>
                            <p class="text-3xl font-bold text-blue-600">₦<?= number_format($user['balance'], 2) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <a href="transfer.php" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-2">
                                <i class="fas fa-exchange-alt text-lg"></i>
                            </div>
                            <span class="text-sm font-medium">Transfer</span>
                        </a>
                        <a href="deposit.php" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2">
                                <i class="fas fa-plus text-lg"></i>
                            </div>
                            <span class="text-sm font-medium">Deposit</span>
                        </a>
                        <a href="withdraw.php" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mb-2">
                                <i class="fas fa-minus text-lg"></i>
                            </div>
                            <span class="text-sm font-medium">Withdraw</span>
                        </a>
                        <a href="transactions.php" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mb-2">
                                <i class="fas fa-history text-lg"></i>
                            </div>
                            <span class="text-sm font-medium">History</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Recent Transactions</h2>
                        <a href="transactions.php" class="text-sm text-blue-600 hover:underline">View All</a>
                    </div>
                    
                    <?php if (empty($transactions)): ?>
                        <div class="text-center py-8">
                            <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                <i class="fas fa-exchange-alt text-gray-400"></i>
                            </div>
                            <p class="text-gray-500">No transactions yet</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($transactions as $txn): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= date('M d, Y', strtotime($txn['date'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?php 
                                                if ($txn['sender_id'] == $user_id) {
                                                    echo "Sent to Acc: " . substr($txn['receiver_id'], -4);
                                                } else {
                                                    echo "Received from Acc: " . substr($txn['sender_id'], -4);
                                                }
                                                ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm <?= ($txn['sender_id'] == $user_id) ? 'text-red-500' : 'text-green-500' ?>">
                                                <?= ($txn['sender_id'] == $user_id) ? '-' : '+' ?> ₦<?= number_format($txn['amount'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Spending Analytics -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Spending Analytics</h2>
                    <div class="h-64">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuButton').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
        });

        document.getElementById('closeSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
        });

        // Chart.js for Spending Analytics
        const ctx = document.getElementById('spendingChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Spending (₦)',
                    data: [0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>