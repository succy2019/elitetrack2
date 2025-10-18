<?php

/**
 * Database Configuration and Connection Class
 * Based on the schema from import.sql
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'mspmetrj_track';
    private $username = 'mspmetrj_track';
    private $password = 'mspmetrj_track';
    private $db_type = 'mysql';
    // private $host = 'localhost';
    // private $db_name = 'elitetrack';
    // private $username = 'root';
    // private $password = '';
    // private $db_type = 'mysql';


    public function __construct() {
        $this->connect();
        $this->initializeTables();
    }

    /**
     * Create database connection using PDO
     */
    private function connect() {
        try {
            // MySQL connection
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            // If database doesn't exist, create it
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                $this->createDatabase();
                // Try connecting again
                $this->pdo = new PDO($dsn, $this->username, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Create database if it doesn't exist
     */
    private function createDatabase() {
        try {
            $dsn = "mysql:host={$this->host};charset=utf8mb4";
            $pdo = new PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch(PDOException $e) {
            throw new Exception("Failed to create database: " . $e->getMessage());
        }
    }

    /**
     * Initialize database tables based on import.sql schema
     */
    private function initializeTables() {
        try {
            // Create users table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    amount VARCHAR(100) NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    phone VARCHAR(50) NOT NULL,
                    address TEXT NOT NULL,
                    message TEXT NOT NULL,
                    track_id VARCHAR(100) UNIQUE NOT NULL,
                    payment_to VARCHAR(255) NOT NULL DEFAULT 'Merchant Commercial Bank',
                    account_number VARCHAR(50) NOT NULL DEFAULT '0012239988',
                    estimated_processing_time VARCHAR(50) NOT NULL DEFAULT '1-2 minutes',
                    money_due VARCHAR(100) NOT NULL,
                    progress_percentage INT NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");

            // Create admins table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS admins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");

            // Create default admin if not exists
            $this->createDefaultAdmin();

        } catch(PDOException $e) {
            throw new Exception("Failed to initialize tables: " . $e->getMessage());
        }
    }

    /**
     * Create default admin user
     */
    private function createDefaultAdmin() {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM admins WHERE email = ?");
            $stmt->execute(['admin@elitetrack.com']);
            
            if ($stmt->fetchColumn() == 0) {
                $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
                $stmt->execute(['admin@elitetrack.com', $defaultPassword]);
            }
        } catch(PDOException $e) {
            // Silently fail if admin already exists
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Generate unique track ID similar to the TypeScript version
     */
    public function generateTrackId() {
        $timestamp = base_convert(time(), 10, 36);
        $randomStr = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        return 'TRK-' . strtoupper($timestamp) . '-' . $randomStr;
    }

    /**
     * Close database connection
     */
    public function close() {
        $this->pdo = null;
    }
}

// Create global database instance
$database = new Database();
$pdo = $database->getConnection();

?>