<?php
echo "🧪 Testing Delete User API Endpoint\n\n";

// First, let's get a valid token by simulating login
echo "1. Getting authentication token...\n";

require_once 'api/controllers/AuthController.php';
require_once 'api/config/database.php';

// Simulate login to get token
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Create temp file for login input
$loginData = json_encode([
    'email' => 'admin@elitetrack.com',
    'password' => 'admin123'
]);
$tempLoginFile = tempnam(sys_get_temp_dir(), 'login_test');
file_put_contents($tempLoginFile, $loginData);

// Override php://input for login
$originalStdin = fopen('php://stdin', 'r');
$tempStdin = fopen($tempLoginFile, 'r');
$GLOBALS['php://input'] = $tempStdin;

// Try to login
ob_start();
$authController = new AuthController();
$authController->login();
$loginOutput = ob_get_clean();

fclose($tempStdin);
fclose($originalStdin);
unlink($tempLoginFile);

echo "Login response: $loginOutput\n";

$loginResponse = json_decode($loginOutput, true);
if (!$loginResponse || !isset($loginResponse['token'])) {
    echo "❌ Failed to get authentication token\n";
    exit;
}

$token = $loginResponse['token'];
echo "✅ Got token: " . substr($token, 0, 20) . "...\n\n";

echo "2. Testing delete user endpoint...\n";

// Get a user to delete
$stmt = $pdo->prepare("SELECT id, name, email FROM users LIMIT 1");
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    echo "❌ No users found to test deletion\n";
    exit;
}

echo "Test user: ID {$user['id']}, {$user['name']} ({$user['email']})\n";

// Simulate DELETE request
$_SERVER['REQUEST_METHOD'] = 'DELETE';
$_SERVER['REQUEST_URI'] = '/api/users/delete';
$_SERVER['HTTP_AUTHORIZATION'] = "Bearer $token";
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Create temp file for delete input
$deleteData = json_encode(['id' => $user['id']]);
$tempDeleteFile = tempnam(sys_get_temp_dir(), 'delete_test');
file_put_contents($tempDeleteFile, $deleteData);

// Override php://input for delete
$tempDeleteStdin = fopen($tempDeleteFile, 'r');
$GLOBALS['php://input'] = $tempDeleteStdin;

// Try to delete
ob_start();
require_once 'api/index.php';
$deleteOutput = ob_get_clean();

fclose($tempDeleteStdin);
unlink($tempDeleteFile);

echo "Delete response: $deleteOutput\n";

$deleteResponse = json_decode($deleteOutput, true);
if ($deleteResponse && isset($deleteResponse['success']) && $deleteResponse['success']) {
    echo "✅ Delete API call successful\n";
} else {
    echo "❌ Delete API call failed\n";
    if ($deleteResponse && isset($deleteResponse['error'])) {
        echo "Error: {$deleteResponse['error']}\n";
    }
}

// Check if user was actually deleted
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$result = $stmt->fetch();

if ($result['count'] == 0) {
    echo "✅ User was successfully deleted from database\n";
} else {
    echo "❌ User still exists in database (delete failed)\n";
}

echo "\n🎯 Test completed!\n";
?>