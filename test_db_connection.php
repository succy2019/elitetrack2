<?php
echo "Testing database connection and user data...\n\n";

try {
    require_once 'api/config/database.php';
    
    echo "✅ Database connection: SUCCESS\n";
    
    // Check if users table exists and has data
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM users');
    $stmt->execute();
    $result = $stmt->fetch();
    echo "📊 Total users in database: " . $result['count'] . "\n\n";
    
    // Show sample users with track IDs
    $stmt = $pdo->prepare('SELECT id, name, email, track_id, status, progress_percentage FROM users LIMIT 10');
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    if ($users) {
        echo "📋 Sample users:\n";
        echo "ID\tName\t\tEmail\t\t\tTrack ID\t\tStatus\t\tProgress\n";
        echo "---\t----\t\t-----\t\t\t--------\t\t------\t\t--------\n";
        foreach ($users as $user) {
            echo sprintf(
                "%d\t%-15s\t%-25s\t%s\t%-10s\t%d%%\n",
                $user['id'],
                substr($user['name'], 0, 15),
                substr($user['email'], 0, 25),
                $user['track_id'],
                $user['status'],
                $user['progress_percentage']
            );
        }
    } else {
        echo "❌ No users found in database\n";
        echo "💡 You need to create some users first through the admin panel\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection FAILED: " . $e->getMessage() . "\n";
}
?>