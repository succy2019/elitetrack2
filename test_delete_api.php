<?php
echo "🧪 Testing Delete User Functionality\n\n";

// Test the delete endpoint directly
echo "Testing DELETE /api/users/delete endpoint...\n\n";

// First, let's get a valid user ID to test with
require_once 'api/config/database.php';

try {
    // Get a user to test deletion
    $stmt = $pdo->prepare("SELECT id, name, email FROM users LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        echo "❌ No users found in database to test deletion\n";
        exit;
    }

    echo "📋 Test User: ID {$user['id']}, Name: {$user['name']}, Email: {$user['email']}\n\n";

    // Test the delete endpoint
    echo "🔍 Testing API endpoint directly...\n";

    // Simulate the request that the frontend makes
    $_SERVER['REQUEST_METHOD'] = 'DELETE';
    $_SERVER['REQUEST_URI'] = '/api/users/delete';
    $_SERVER['CONTENT_TYPE'] = 'application/json';

    // Create a temporary JSON file to simulate php://input
    $tempFile = tempnam(sys_get_temp_dir(), 'delete_test');
    $jsonData = json_encode(['id' => $user['id']]);
    file_put_contents($tempFile, $jsonData);

    // Redirect php://input to our temp file
    $originalInput = 'php://input';
    $GLOBALS['__test_input'] = $tempFile;

    // Override file_get_contents for php://input
    function file_get_contents_override($filename) {
        if ($filename === 'php://input' && isset($GLOBALS['__test_input'])) {
            return file_get_contents($GLOBALS['__test_input']);
        }
        return call_user_func_array('file_get_contents', func_get_args());
    }

    // Temporarily override file_get_contents
    $original_file_get_contents = 'file_get_contents';
    rename_function('file_get_contents', 'file_get_contents_original');
    rename_function('file_get_contents_override', 'file_get_contents');

    // Include the API router
    ob_start();
    require_once 'api/index.php';
    $output = ob_get_clean();

    // Restore original function
    rename_function('file_get_contents', 'file_get_contents_override');
    rename_function('file_get_contents_original', 'file_get_contents');

    // Clean up
    unlink($tempFile);
    unset($GLOBALS['__test_input']);

    echo "📡 API Response: $output\n";

    // Parse the response
    $response = json_decode($output, true);
    if ($response) {
        if (isset($response['success']) && $response['success']) {
            echo "✅ Delete API call successful\n";
        } else if (isset($response['error'])) {
            echo "❌ Delete API call failed: {$response['error']}\n";
        }
    } else {
        echo "❌ Invalid JSON response\n";
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

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}

echo "\n💡 If the API test fails, check:\n";
echo "   1. Authentication (token required)\n";
echo "   2. CORS headers\n";
echo "   3. Request method (DELETE)\n";
echo "   4. JSON payload format\n";
?>