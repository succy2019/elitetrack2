<?php
echo "Testing API endpoint for track ID functionality...\n\n";

// Test the specific track ID that was mentioned in the terminal history
$trackId = 'TRK-T47WBM-LB5FJG';

// Also test the track IDs we found in the database
$testTrackIds = [
    'TRK-T47WBM-LB5FJG', // From terminal history
    'TRK-T47SSR-3ZDWFL', // Mike Johnson
    'TRK-T47SSR-BS8NEG'  // Sarah Wilson
];

foreach ($testTrackIds as $testTrackId) {
    echo "🔍 Testing track ID: $testTrackId\n";
    
    try {
        require_once 'api/controllers/UserController.php';
        
        // Create a new UserController instance
        $userController = new UserController();
        
        // Capture output
        ob_start();
        
        // Call the getUserByTrackId method
        $userController->getUserByTrackId($testTrackId);
        
        // Get the output
        $output = ob_get_clean();
        
        echo "📡 API Response: $output\n";
        
        // Try to parse as JSON
        $data = json_decode($output, true);
        if ($data) {
            if (isset($data['user'])) {
                echo "✅ User found: " . $data['user']['name'] . " (" . $data['user']['email'] . ")\n";
            } else if (isset($data['error'])) {
                echo "❌ Error: " . $data['error'] . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}
?>