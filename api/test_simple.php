<?php

/**
 * Simple API Test Script - File-based storage
 */

class SimpleApiTester {
    private $baseUrl;
    private $token;

    public function __construct($baseUrl = null) {
        if ($baseUrl) {
            $this->baseUrl = rtrim($baseUrl, '/');
        } else {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $this->baseUrl = $protocol . '://' . $host . '/api/simple.php';
        }
    }

    private function makeRequest($method, $endpoint, $data = null, $headers = []) {
        $url = $this->baseUrl . $endpoint;
        
        $options = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => $data ? json_encode($data) : null,
                'ignore_errors' => true
            ]
        ];
        
        if ($data) {
            $options['http']['header'] .= "\r\nContent-Type: application/json";
        }
        
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        // Get response code from headers
        $httpCode = 200;
        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                    break;
                }
            }
        }
        
        return [
            'status_code' => $httpCode,
            'body' => json_decode($response, true),
            'raw_body' => $response
        ];
    }

    public function testApiInfo() {
        echo "Testing API Info...\n";
        $response = $this->makeRequest('GET', '');
        
        if ($response['status_code'] === 200) {
            echo "✅ API Info: PASSED\n";
            echo "   API Name: " . ($response['body']['name'] ?? 'Unknown') . "\n";
            echo "   Storage: " . ($response['body']['storage'] ?? 'Unknown') . "\n";
        } else {
            echo "❌ API Info: FAILED (Status: {$response['status_code']})\n";
            echo "   Response: " . ($response['raw_body'] ?? 'No response') . "\n";
        }
        echo "\n";
    }

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
            
            // Return first user for further testing
            if ($userCount > 0) {
                return $response['body']['users'][0];
            }
        } else {
            echo "❌ Get All Users: FAILED (Status: {$response['status_code']})\n";
            echo "   Response: " . ($response['raw_body'] ?? 'No response') . "\n";
        }
        echo "\n";
        return null;
    }

    public function testCreateUser() {
        echo "Testing Create User...\n";
        
        if (!$this->token) {
            echo "❌ Create User: SKIPPED (No token available)\n\n";
            return null;
        }
        
        $userData = [
            'email' => 'test.api@example.com',
            'name' => 'API Test User',
            'amount' => '$1,000.00',
            'status' => 'pending',
            'phone' => '+1 (555) 000-0000',
            'address' => '123 Test API St, Test City, TC 12345',
            'message' => 'Test user created by Simple API test script',
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
            echo "   Response: " . ($response['raw_body'] ?? 'No response') . "\n";
        }
        echo "\n";
    }

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
            echo "   Response: " . ($response['raw_body'] ?? 'No response') . "\n";
        }
        echo "\n";
    }

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
            echo "   Response: " . ($response['raw_body'] ?? 'No response') . "\n";
        }
        echo "\n";
    }

    public function runAllTests() {
        echo "🧪 Elite Track Simple API Test Suite\n";
        echo "===================================\n\n";
        
        echo "📍 Testing API at: {$this->baseUrl}\n\n";
        
        // Test API info
        $this->testApiInfo();
        
        // Test authentication
        $this->testLogin();
        
        // Test user operations
        $firstUser = $this->testGetAllUsers();
        $newUser = $this->testCreateUser();
        
        if ($newUser) {
            $this->testTrackUser($newUser['track_id']);
            $this->testUpdateProgress($newUser['id']);
        } else if ($firstUser) {
            $this->testTrackUser($firstUser['track_id']);
        }
        
        $this->testGetStats();
        
        echo "🏁 Simple API test suite completed!\n";
        echo "💡 If tests passed, your API is working with file-based storage.\n";
        echo "🌐 Your frontend can now connect to: {$this->baseUrl}\n\n";
        
        echo "📋 Frontend Integration:\n";
        echo "- Update your frontend API calls to use: {$this->baseUrl}\n";
        echo "- Default admin: admin@elitetrack.com / admin123\n";
        
        if ($newUser) {
            echo "- Test tracking: {$this->baseUrl}/users/track/{$newUser['track_id']}\n";
        }
    }
}

// Run tests if script is executed directly
if (php_sapi_name() === 'cli') {
    // Command line execution
    $baseUrl = isset($argv[1]) ? $argv[1] : null;
    $tester = new SimpleApiTester($baseUrl);
    $tester->runAllTests();
} else {
    // Web execution
    $baseUrl = isset($_GET['base_url']) ? $_GET['base_url'] : null;
    $tester = new SimpleApiTester($baseUrl);
    
    echo "<pre>";
    $tester->runAllTests();
    echo "</pre>";
}

?>