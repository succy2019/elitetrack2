<?php

require_once __DIR__ . '/../config/database.php';

/**
 * User Model - Handles all user-related database operations
 * Based on the schema and functions from import.sql
 */
class User {
    private $pdo;
    private $database;

    public function __construct() {
        global $database, $pdo;
        $this->database = $database;
        $this->pdo = $pdo;
    }

    /**
     * Create a new user (equivalent to createUser from import.sql)
     */
    public function createUser($userData) {
        try {
            $trackId = $this->database->generateTrackId();
            
            $sql = "INSERT INTO users (
                        email, name, amount, status, phone, address, message, 
                        track_id, payment_to, account_number, estimated_processing_time, 
                        money_due, progress_percentage, sender_bank
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $userData['email'] ?? null,
                $userData['name'],
                $userData['amount'],
                $userData['status'],
                $userData['phone'] ?? null,
                $userData['address'],
                $userData['message'],
                $trackId,
                $userData['payment_to'] ?? 'Merchant Commercial Bank',
                $userData['account_number'] ?? '0012239988',
                $userData['estimated_processing_time'] ?? '1-2 minutes',
                $userData['money_due'] ?? $userData['amount'],
                $userData['progress_percentage'] ?? 0,
                $userData['sender_bank'] ?? null
            ]);

            $userId = $this->pdo->lastInsertId();
            return $this->getUserById($userId);

        } catch(PDOException $e) {
            throw new Exception("Failed to create user: " . $e->getMessage());
        }
    }

    /**
     * Get user by ID (equivalent to getUserById from import.sql)
     */
    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Failed to get user by ID: " . $e->getMessage());
        }
    }

    /**
     * Get user by email (equivalent to getUserByEmail from import.sql)
     */
    public function getUserByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Failed to get user by email: " . $e->getMessage());
        }
    }

    /**
     * Get user by track ID (equivalent to getUserByTrackId from import.sql)
     */
    public function getUserByTrackId($trackId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE track_id = ?");
            $stmt->execute([$trackId]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Failed to get user by track ID: " . $e->getMessage());
        }
    }

    /**
     * Get all users (equivalent to getAllUsers from import.sql)
     */
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Failed to get all users: " . $e->getMessage());
        }
    }

    /**
     * Update user profile (equivalent to updateUserProfile from import.sql)
     */
    public function updateUserProfile($userId, $userData) {
        try {
            $sql = "UPDATE users SET 
                        name = ?,
                        email = ?,
                        amount = ?,
                        status = ?,
                        message = ?,
                        address = ?,
                        phone = ?,
                        payment_to = ?,
                        account_number = ?,
                        estimated_processing_time = ?,
                        money_due = ?,
                        progress_percentage = ?,
                        sender_bank = ?,
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $userData['name'],
                $userData['email'],
                $userData['amount'],
                $userData['status'],
                $userData['message'],
                $userData['address'],
                $userData['phone'],
                $userData['payment_to'] ?? 'Merchant Commercial Bank',
                $userData['account_number'] ?? '0012239988',
                $userData['estimated_processing_time'] ?? '1-2 minutes',
                $userData['money_due'] ?? $userData['amount'],
                $userData['progress_percentage'] ?? 0,
                $userData['sender_bank'] ?? null,
                $userId
            ]);

            return $this->getUserById($userId);

        } catch(PDOException $e) {
            throw new Exception("Failed to update user profile: " . $e->getMessage());
        }
    }

    /**
     * Update user progress (equivalent to updateUserProgress from import.sql)
     */
    public function updateUserProgress($userId, $progressPercentage) {
        try {
            $sql = "UPDATE users SET 
                        progress_percentage = ?,
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$progressPercentage, $userId]);

            return $this->getUserById($userId);

        } catch(PDOException $e) {
            throw new Exception("Failed to update user progress: " . $e->getMessage());
        }
    }

    /**
     * Delete user (equivalent to deleteUser from import.sql)
     */
    public function deleteUser($userId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            throw new Exception("Failed to delete user: " . $e->getMessage());
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats() {
        try {
            $stats = [];
            
            // Total users
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM users");
            $stmt->execute();
            $stats['total'] = $stmt->fetch()['total'];
            
            // Active users
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as active FROM users WHERE status = 'active'");
            $stmt->execute();
            $stats['active'] = $stmt->fetch()['active'];
            
            // Pending users
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as pending FROM users WHERE status = 'pending'");
            $stmt->execute();
            $stats['pending'] = $stmt->fetch()['pending'];
            
            // Processing users
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as processing FROM users WHERE status = 'processing'");
            $stmt->execute();
            $stats['processing'] = $stmt->fetch()['processing'];
            
            // Completed users
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as completed FROM users WHERE status = 'completed'");
            $stmt->execute();
            $stats['completed'] = $stmt->fetch()['completed'];

            return $stats;

        } catch(PDOException $e) {
            throw new Exception("Failed to get user statistics: " . $e->getMessage());
        }
    }

    /**
     * Search users by various criteria
     */
    public function searchUsers($criteria) {
        try {
            $sql = "SELECT * FROM users WHERE 1=1";
            $params = [];

            if (!empty($criteria['email'])) {
                $sql .= " AND email LIKE ?";
                $params[] = '%' . $criteria['email'] . '%';
            }

            if (!empty($criteria['name'])) {
                $sql .= " AND name LIKE ?";
                $params[] = '%' . $criteria['name'] . '%';
            }

            if (!empty($criteria['status'])) {
                $sql .= " AND status = ?";
                $params[] = $criteria['status'];
            }

            if (!empty($criteria['track_id'])) {
                $sql .= " AND track_id LIKE ?";
                $params[] = '%' . $criteria['track_id'] . '%';
            }

            $sql .= " ORDER BY created_at DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch(PDOException $e) {
            throw new Exception("Failed to search users: " . $e->getMessage());
        }
    }
}

?>