<?php
echo "🔍 API Route Debugging\n\n";

// Simulate different request scenarios
function testPath($requestUri, $description) {
    echo "Testing: $description\n";
    echo "Request URI: $requestUri\n";
    
    $path = parse_url($requestUri, PHP_URL_PATH);
    $path = rtrim($path, '/');
    echo "Parsed path: $path\n";
    
    // Remove the project folder from the path to match routing patterns
    $path = str_replace('/elitetrack2', '', $path);
    echo "Final path: $path\n";
    
    // Test the track ID route pattern
    $pattern = '#^/api/users/track/(.+)$#';
    if (preg_match($pattern, $path, $matches)) {
        echo "✅ MATCHES track ID pattern\n";
        echo "Track ID extracted: " . $matches[1] . "\n";
    } else {
        echo "❌ Does NOT match track ID pattern\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test various scenarios
testPath('/elitetrack2/api/users/track/TRK-T47SSR-3ZDWFL', 'XAMPP localhost with project folder');
testPath('/api/users/track/TRK-T47SSR-3ZDWFL', 'Direct API call without project folder');
testPath('/elitetrack2/api', 'XAMPP API root');
testPath('/api', 'Direct API root');

// Test with actual server variables if available
if (isset($_SERVER['REQUEST_URI'])) {
    echo "📍 Current actual request:\n";
    testPath($_SERVER['REQUEST_URI'], 'Current actual request');
}

echo "💡 If testing from browser, visit:\n";
echo "   http://localhost/elitetrack2/test_routing.php\n";
echo "   http://localhost/elitetrack2/api/users/track/TRK-T47SSR-3ZDWFL\n";
?>