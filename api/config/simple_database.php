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
        
        // Ensure data directory exists and is writable
        if (!is_dir($this->dataDir)) {
            if (!mkdir($this->dataDir, 0755, true)) {
                error_log("SimpleDatabase: Failed to create data directory: " . $this->dataDir);
                throw new Exception("Failed to create data directory: " . $this->dataDir);
            }
            error_log("SimpleDatabase: Created data directory: " . $this->dataDir);
        }
        
        // Check if directory is writable
        if (!is_writable($this->dataDir)) {
            error_log("SimpleDatabase: Data directory is not writable: " . $this->dataDir);
            throw new Exception("Data directory is not writable: " . $this->dataDir);
        }
        
        $this->initializeData();
        error_log("SimpleDatabase: Initialized successfully");
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
        if (!file_exists($this->usersFile)) {
            error_log("SimpleDatabase::getUsers - Users file does not exist: " . $this->usersFile);
            return [];
        }
        
        $data = file_get_contents($this->usersFile);
        if ($data === false) {
            error_log("SimpleDatabase::getUsers - Failed to read users file: " . $this->usersFile);
            return [];
        }
        
        $users = json_decode($data, true);
        if ($users === null) {
            error_log("SimpleDatabase::getUsers - Failed to decode JSON from users file: " . $this->usersFile);
            error_log("SimpleDatabase::getUsers - JSON error: " . json_last_error_msg());
            return [];
        }
        
        error_log("SimpleDatabase::getUsers - Successfully loaded " . count($users) . " users");
        return $users;
    }

    public function saveUsers($users) {
        $result = file_put_contents($this->usersFile, json_encode($users, JSON_PRETTY_PRINT));
        if ($result === false) {
            throw new Exception("Failed to write users file: " . $this->usersFile);
        }
        return $result;
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