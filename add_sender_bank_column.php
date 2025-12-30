<?php
// Add sender_bank column to local database
require_once 'api/config/database.php';

try {
    $sql = "ALTER TABLE users ADD COLUMN sender_bank VARCHAR(255) NULL AFTER progress_percentage";
    $pdo->exec($sql);
    echo "✅ SUCCESS: sender_bank column added to users table!<br>";
    echo "You can now delete this file.";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️ Column 'sender_bank' already exists in the table.<br>";
        echo "No changes needed. You can delete this file.";
    } else {
        echo "❌ ERROR: " . $e->getMessage();
    }
}
?>
