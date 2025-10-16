<?php

/**
 * API Test Script
 * Tests all API endpoints to verify functionality
 */

class ApiTester {
    private $baseUrl;
    private $token;

    public function __construct($baseUrl = 'http://localhost/api') {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Make HTTP request
     */
    private function makeRequest($method, $endpoint, $data = null, $headers = []) {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'status_code' => $httpCode,
            'body' => json_decode($response, true),
            'raw_body' => $response
        ];
    }

    /**
     * Test API info endpoint
     */
    public function testApiInfo() {
        echo "Testing API Info...\n";
        $response = $this->makeRequest('GET', '/');
        
        if ($response['status_code'] === 200) {
            echo "✅ API Info: PASSED\n";
            echo "   API Name: " . ($response['body']['name'] ?? 'Unknown') . "\n";
        } else {
            echo "❌ API Info: FAILED (Status: {$response['status_code']})\n";
        }
        echo "\n";
    }

    /**
     * Test admin login
     */
    public function testLogin() {
        echo "Testing Admin Login...\n";
        
        $loginData = [
            'email' => 'admin@elitetrack.com',
            'password' => 'admin123'
        ];
        
        $response = $this->makeRequest('POST', '/auth/login', $loginData);
        
        if ($response['status_code'] === 200 && isset($response['body']['token'])) {
            echo "✅ Login: PASSED\n";
            $this->token = $response['body']['token'];
            echo "   Token received and saved\n";
        } else {
            echo "❌ Login: FAILED (Status: {$response['status_code']})\n";
            echo "   Response: " . ($response['raw_body'] ?? 'No response') . "\n";
        }
        echo "\n";
    }

    /**
     * Test token verification
     */
    public function testTokenVerification() {
        echo "Testing Token Verification...\n";
        
        if (!$this->token) {
            echo "❌ Token Verification: SKIPPED (No token available)\n\n";
            return;
        }
        
        $headers = ['Authorization: Bearer ' . $this->token];
        $response = $this->makeRequest('GET', '/auth/verify', null, $headers);
        
        if ($response['status_code'] === 200) {
            echo "✅ Token Verification: PASSED\n";
        } else {
            echo "❌ Token Verification: FAILED (Status: {$response['status_code']})\n";
        }
        echo "\n";
    }

    /**
     * Test get all users
     */
    public function testGetAllUsers() {
        echo "Testing Get All Users...\n";
        
        if (!$this->token) {
            echo "❌ Get All Users: SKIPPED (No token available)\n\n";
            return;
        }
        
        $headers = ['Authorization: Bearer ' . $this->token];
        $response = $this->makeRequest('GET', '/users/all', null, $headers);
        
        if ($response['status_code'] === 200) {
            $userCount = count($response['body']['users'] ?? []);
            echo "✅ Get All Users: PASSED\n";
            echo "   Found {$userCount} users\n";
        } else {
            echo "❌ Get All Users: FAILED (Status: {$response['status_code']})\n";
        }
        echo "\n";
    }

    /**
     * Test create user
     */
    public function testCreateUser() {
        echo "Testing Create User...\n";
        
        if (!$this->token) {
            echo "❌ Create User: SKIPPED (No token available)\n\n";
            return;
        }
        
        $userData = [
            'email' => 'test.user@example.com',
            'name' => 'Test User',
            'amount' => '$1,000.00',
            'status' => 'pending',
            'phone' => '+1 (555) 000-0000',
            'address' => '123 Test St, Test City, TC 12345',
            'message' => 'Test user created by API test script',
            'progress_percentage' => 0
        ];
        
        $headers = ['Authorization: Bearer ' . $this->token];
        $response = $this->makeRequest('POST', '/users/new', $userData, $headers);
        
        if ($response['status_code'] === 201) {
            echo "✅ Create User: PASSED\n";
            $trackId = $response['body']['user']['track_id'] ?? 'Unknown';
            echo "   Track ID: {$trackId}\n";
            return $response['body']['user'];
        } else {
            echo "❌ Create User: FAILED (Status: {$response['status_code']})\n";
            echo "   Response: " . ($response['raw_body'] ?? 'No response') . "\n";
        }
        echo "\n";
        return null;
    }

    /**
     * Test track user by track ID (public endpoint)
     */
    public function testTrackUser($trackId) {
        echo "Testing Track User (Public Endpoint)...\n";
        
        if (!$trackId) {
            echo "❌ Track User: SKIPPED (No track ID available)\n\n";
            return;
        }
        
        $response = $this->makeRequest('GET', "/users/track/{$trackId}");
        
        if ($response['status_code'] === 200) {
            echo "✅ Track User: PASSED\n";
            $userName = $response['body']['user']['name'] ?? 'Unknown';
            echo "   User: {$userName}\n";
        } else {
            echo "❌ Track User: FAILED (Status: {$response['status_code']})\n";
        }
        echo "\n";
    }

    /**
     * Test update user progress
     */
    public function testUpdateProgress($userId) {
        echo "Testing Update User Progress...\n";
        
        if (!$this->token || !$userId) {
            echo "❌ Update Progress: SKIPPED (No token or user ID available)\n\n";
            return;
        }
        
        $progressData = [
            'id' => $userId,
            'progress_percentage' => 50
        ];
        
        $headers = ['Authorization: Bearer ' . $this->token];
        $response = $this->makeRequest('PUT', '/users/progress', $progressData, $headers);
        
        if ($response['status_code'] === 200) {
            echo "✅ Update Progress: PASSED\n";
        } else {
            echo "❌ Update Progress: FAILED (Status: {$response['status_code']})\n";
        }
        echo "\n";
    }

    /**
     * Test get user statistics
     */
    public function testGetStats() {
        echo "Testing Get User Statistics...\n";
        
        if (!$this->token) {
            echo "❌ Get Stats: SKIPPED (No token available)\n\n";
            return;
        }
        
        $headers = ['Authorization: Bearer ' . $this->token];
        $response = $this->makeRequest('GET', '/users/stats', null, $headers);
        
        if ($response['status_code'] === 200) {
            echo "✅ Get Stats: PASSED\n";
            $stats = $response['body']['stats'] ?? [];
            echo "   Total: " . ($stats['total'] ?? 0) . "\n";
            echo "   Active: " . ($stats['active'] ?? 0) . "\n";
            echo "   Pending: " . ($stats['pending'] ?? 0) . "\n";
        } else {
            echo "❌ Get Stats: FAILED (Status: {$response['status_code']})\n";
        }
        echo "\n";
    }

    /**
     * Run all tests
     */
    public function runAllTests() {
        echo "🧪 Elite Track API Test Suite\n";
        echo "============================\n\n";
        
        // Test API info
        $this->testApiInfo();
        
        // Test authentication
        $this->testLogin();
        $this->testTokenVerification();
        
        // Test user operations
        $this->testGetAllUsers();
        $newUser = $this->testCreateUser();
        
        if ($newUser) {
            $this->testTrackUser($newUser['track_id']);
            $this->testUpdateProgress($newUser['id']);
        }
        
        $this->testGetStats();
        
        echo "🏁 Test suite completed!\n";
        echo "💡 If all tests passed, your API is working correctly.\n";
        echo "🌐 You can now use the API with your frontend application.\n\n";
        
        echo "📋 Quick Test URLs:\n";
        echo "- API Info: {$this->baseUrl}/\n";
        echo "- Login: POST {$this->baseUrl}/auth/login\n";
        echo "- Get Users: GET {$this->baseUrl}/users/all (requires auth)\n";
        
        if ($newUser) {
            echo "- Track User: GET {$this->baseUrl}/users/track/{$newUser['track_id']} (public)\n";
        }
    }
}

// Run tests if script is executed directly
if (php_sapi_name() === 'cli') {
    // Command line execution
    $baseUrl = isset($argv[1]) ? $argv[1] : 'http://localhost/api';
    $tester = new ApiTester($baseUrl);
    $tester->runAllTests();
} else {
    // Web execution
    $baseUrl = isset($_GET['base_url']) ? $_GET['base_url'] : 'http://localhost/api';
    $tester = new ApiTester($baseUrl);
    
    echo "<pre>";
    $tester->runAllTests();
    echo "</pre>";
}

?>