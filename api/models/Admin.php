<?php

require_once __DIR__ . '/../config/database.php';

/**
 * Admin Model - Handles admin authentication and operations
 * Based on the schema and functions from import.sql
 */
class Admin {
    private $pdo;
    private $database;

    public function __construct() {
        global $database, $pdo;
        $this->database = $database;
        $this->pdo = $pdo;
    }

    /**
     * Get admin by email (equivalent to getAdminByEmail from import.sql)
     */
    public function getAdminByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Failed to get admin by email: " . $e->getMessage());
        }
    }

    /**
     * Create new admin (equivalent to createAdmin from import.sql)
     */
    public function createAdmin($adminData) {
        try {
            $sql = "INSERT INTO admins (email, password) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $adminData['email'],
                password_hash($adminData['password'], PASSWORD_DEFAULT)
            ]);

            $adminId = $this->pdo->lastInsertId();
            return $this->getAdminById($adminId);

        } catch(PDOException $e) {
            throw new Exception("Failed to create admin: " . $e->getMessage());
        }
    }

    /**
     * Get admin by ID
     */
    public function getAdminById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Failed to get admin by ID: " . $e->getMessage());
        }
    }

    /**
     * Update admin password (equivalent to updateAdminPassword from import.sql)
     */
    public function updateAdminPassword($email, $newPassword) {
        try {
            $sql = "UPDATE admins SET 
                        password = ?, 
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE email = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                password_hash($newPassword, PASSWORD_DEFAULT),
                $email
            ]);

            return $stmt->rowCount() > 0;

        } catch(PDOException $e) {
            throw new Exception("Failed to update admin password: " . $e->getMessage());
        }
    }

    /**
     * Verify admin login credentials
     */
    public function verifyLogin($email, $password) {
        try {
            $admin = $this->getAdminByEmail($email);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Remove password from returned data
                unset($admin['password']);
                return $admin;
            }
            
            return false;

        } catch(Exception $e) {
            throw new Exception("Failed to verify login: " . $e->getMessage());
        }
    }

    /**
     * Verify current password for password change
     */
    public function verifyCurrentPassword($email, $currentPassword) {
        try {
            $admin = $this->getAdminByEmail($email);
            
            if ($admin && password_verify($currentPassword, $admin['password'])) {
                return true;
            }
            
            return false;

        } catch(Exception $e) {
            throw new Exception("Failed to verify current password: " . $e->getMessage());
        }
    }

    /**
     * Get all admins (for administrative purposes)
     */
    public function getAllAdmins() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, email, created_at, updated_at FROM admins ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Failed to get all admins: " . $e->getMessage());
        }
    }

    /**
     * Delete admin
     */
    public function deleteAdmin($adminId) {
        try {
            // Prevent deletion if it's the last admin
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM admins");
            $stmt->execute();
            $adminCount = $stmt->fetch()['count'];
            
            if ($adminCount <= 1) {
                throw new Exception("Cannot delete the last admin account");
            }

            $stmt = $this->pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$adminId]);
            return $stmt->rowCount() > 0;

        } catch(PDOException $e) {
            throw new Exception("Failed to delete admin: " . $e->getMessage());
        }
    }

    /**
     * Update admin profile
     */
    public function updateAdminProfile($adminId, $email) {
        try {
            $sql = "UPDATE admins SET 
                        email = ?, 
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $adminId]);

            return $this->getAdminById($adminId);

        } catch(PDOException $e) {
            throw new Exception("Failed to update admin profile: " . $e->getMessage());
        }
    }
}

?>