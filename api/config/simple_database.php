<?php

/**
 * Simple File-Based Database Configuration
 * Alternative to SQLite when PDO SQLite driver is not available
 */

class SimpleDatabase {
    private $dataDir;
    private $usersFile;
    private $adminsFile;

    public function __construct() {
        $this->dataDir = __DIR__ . '/../data';
        $this->usersFile = $this->dataDir . '/users.json';
        $this->adminsFile = $this->dataDir . '/admins.json';
        
        // Create data directory if it doesn't exist
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
        
        $this->initializeData();
    }

    private function initializeData() {
        // Initialize users file
        if (!file_exists($this->usersFile)) {
            file_put_contents($this->usersFile, json_encode([]));
        }
        
        // Initialize admins file with default admin
        if (!file_exists($this->adminsFile)) {
            $defaultAdmins = [
                [
                    'id' => 1,
                    'email' => 'admin@elitetrack.com',
                    'password' => password_hash('admin123', PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            file_put_contents($this->adminsFile, json_encode($defaultAdmins));
        }
    }

    public function getUsers() {
        $data = file_get_contents($this->usersFile);
        return json_decode($data, true) ?: [];
    }

    public function saveUsers($users) {
        return file_put_contents($this->usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function getAdmins() {
        $data = file_get_contents($this->adminsFile);
        return json_decode($data, true) ?: [];
    }

    public function saveAdmins($admins) {
        return file_put_contents($this->adminsFile, json_encode($admins, JSON_PRETTY_PRINT));
    }

    public function generateTrackId() {
        $timestamp = base_convert(time(), 10, 36);
        $randomStr = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        return 'TRK-' . strtoupper($timestamp) . '-' . $randomStr;
    }

    public function getNextId($data) {
        if (empty($data)) return 1;
        $maxId = max(array_column($data, 'id'));
        return $maxId + 1;
    }
}

// Create global database instance
$simpleDb = new SimpleDatabase();

?>