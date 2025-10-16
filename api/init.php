<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Admin.php';
require_once __DIR__ . '/models/User.php';

/**
 * Database Initialization Script
 * Run this script to set up the database with sample data
 */

echo "Elite Track API - Database Initialization\n";
echo "==========================================\n\n";

try {
    // Initialize database connection
    global $database, $pdo;
    
    echo "✅ Database connection established\n";
    echo "✅ Tables created successfully\n";
    
    // Create admin model
    $adminModel = new Admin();
    $userModel = new User();
    
    // Check if default admin exists
    $defaultAdmin = $adminModel->getAdminByEmail('admin@elitetrack.com');
    if ($defaultAdmin) {
        echo "✅ Default admin already exists\n";
    } else {
        echo "❌ Default admin not found, this should not happen\n";
    }
    
    // Create sample users if none exist
    $existingUsers = $userModel->getAllUsers();
    
    if (count($existingUsers) === 0) {
        echo "\n📝 Creating sample users...\n";
        
        $sampleUsers = [
            [
                'email' => 'john.doe@example.com',
                'name' => 'John Doe',
                'amount' => '$5,000.00',
                'status' => 'active',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Main St, New York, NY 10001',
                'message' => 'Payment processing for business loan application',
                'payment_to' => 'Merchant Commercial Bank',
                'account_number' => '0012239988',
                'estimated_processing_time' => '1-2 minutes',
                'money_due' => '$5,000.00',
                'progress_percentage' => 75
            ],
            [
                'email' => 'jane.smith@example.com',
                'name' => 'Jane Smith',
                'amount' => '$2,500.00',
                'status' => 'processing',
                'phone' => '+1 (555) 987-6543',
                'address' => '456 Oak Ave, Los Angeles, CA 90210',
                'message' => 'Investment portfolio setup payment',
                'payment_to' => 'Merchant Commercial Bank',
                'account_number' => '0012239988',
                'estimated_processing_time' => '3-5 minutes',
                'money_due' => '$2,500.00',
                'progress_percentage' => 45
            ],
            [
                'email' => 'mike.johnson@example.com',
                'name' => 'Mike Johnson',
                'amount' => '$8,750.00',
                'status' => 'pending',
                'phone' => '+1 (555) 456-7890',
                'address' => '789 Pine St, Chicago, IL 60601',
                'message' => 'Real estate transaction payment pending verification',
                'payment_to' => 'Merchant Commercial Bank',
                'account_number' => '0012239988',
                'estimated_processing_time' => '5-10 minutes',
                'money_due' => '$8,750.00',
                'progress_percentage' => 15
            ],
            [
                'email' => 'sarah.wilson@example.com',
                'name' => 'Sarah Wilson',
                'amount' => '$3,200.00',
                'status' => 'completed',
                'phone' => '+1 (555) 234-5678',
                'address' => '321 Elm St, Miami, FL 33101',
                'message' => 'Insurance claim payment completed successfully',
                'payment_to' => 'Merchant Commercial Bank',
                'account_number' => '0012239988',
                'estimated_processing_time' => '1-2 minutes',
                'money_due' => '$3,200.00',
                'progress_percentage' => 100
            ]
        ];
        
        foreach ($sampleUsers as $userData) {
            $user = $userModel->createUser($userData);
            echo "   ✅ Created user: {$user['name']} (Track ID: {$user['track_id']})\n";
        }
        
        echo "\n✅ Sample users created successfully\n";
    } else {
        echo "✅ Users already exist in database (" . count($existingUsers) . " users)\n";
    }
    
    // Display summary
    echo "\n📊 Database Summary:\n";
    echo "==================\n";
    
    $allUsers = $userModel->getAllUsers();
    $stats = $userModel->getUserStats();
    
    echo "Total Users: " . $stats['total'] . "\n";
    echo "Active Users: " . $stats['active'] . "\n";
    echo "Processing Users: " . $stats['processing'] . "\n";
    echo "Pending Users: " . $stats['pending'] . "\n";
    echo "Completed Users: " . $stats['completed'] . "\n";
    
    echo "\n🔐 Default Admin Credentials:\n";
    echo "Email: admin@elitetrack.com\n";
    echo "Password: admin123\n";
    
    echo "\n📋 Sample Track IDs for testing:\n";
    foreach ($allUsers as $user) {
        echo "- {$user['name']}: {$user['track_id']}\n";
    }
    
    echo "\n🚀 API is ready! Available at: http://localhost/api\n";
    echo "📚 API Documentation: http://localhost/api/\n";
    
} catch (Exception $e) {
    echo "❌ Error initializing database: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

?>