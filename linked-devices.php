<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('signin.php');
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle device removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_device'])) {
    $device_id = $_POST['device_id'];
    
    $stmt = $conn->prepare("DELETE FROM trusted_devices WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $device_id, $user_id);
    
    if ($stmt->execute()) {
        $success = "Device removed successfully";
        log_activity($conn, $user_id, 'device_removed', "Device ID: $device_id");
    } else {
        $error = "Failed to remove device";
    }
}

// Get trusted devices
$stmt = $conn->prepare("SELECT * FROM trusted_devices WHERE user_id = ? ORDER BY last_used DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$devices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linked Devices | SaveWise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-laptop mr-3 text-savewise-primary"></i>
                    Linked Devices
                </h1>
                <p class="text-gray-600 mt-2">Manage devices that have access to your SaveWise account</p>
            </div>

            <!-- Info Alert -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Trusted devices don't require full authentication when logging in from recognized locations.
                        </p>
                    </div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
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
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
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

            <!-- Devices List -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <?php if (empty($devices)): ?>
                    <div class="text-center py-12">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 text-gray-400 mb-4">
                            <i class="fas fa-laptop-slash"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No Trusted Devices</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Your trusted devices will appear here when you enable "Remember this device" during login.
                        </p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($devices as $device): ?>
                            <li class="<?= ($device['device_token'] === ($_COOKIE['device_token'] ?? '')) ? 'bg-green-50' : 'hover:bg-gray-50' ?>">
                                <div class="px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <?php if (strpos($device['user_agent'], 'Mobile') !== false): ?>
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                    <i class="fas fa-mobile-alt"></i>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                                    <i class="fas fa-laptop"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?= get_device_name($device['user_agent']) ?></div>
                                                <div class="text-sm text-gray-500">
                                                    Last used: <?= date('M j, Y g:i A', strtotime($device['last_used'])) ?>
                                                    <?php if ($device['ip_address']): ?>
                                                        • <?= $device['ip_address'] ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <?php if ($device['device_token'] === ($_COOKIE['device_token'] ?? '')): ?>
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    This Device
                                                </span>
                                            <?php else: ?>
                                                <form method="POST">
                                                    <input type="hidden" name="device_id" value="<?= $device['id'] ?>">
                                                    <button type="submit" name="remove_device" class="ml-4 p-2 text-gray-400 hover:text-red-500 rounded-full hover:bg-red-50">
                                                        <i class="fas fa-trash-alt"></i>
                                                        <span class="sr-only">Remove</span>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>