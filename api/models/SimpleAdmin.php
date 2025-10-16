<?php

require_once __DIR__ . '/../config/simple_database.php';

/**
 * Simple Admin Model - File-based storage
 */
class SimpleAdmin {
    private $db;

    public function __construct() {
        global $simpleDb;
        $this->db = $simpleDb;
    }

    public function getAdminByEmail($email) {
        $admins = $this->db->getAdmins();
        foreach ($admins as $admin) {
            if ($admin['email'] === $email) {
                return $admin;
            }
        }
        return null;
    }

    public function getAdminById($id) {
        $admins = $this->db->getAdmins();
        foreach ($admins as $admin) {
            if ($admin['id'] == $id) {
                return $admin;
            }
        }
        return null;
    }

    public function verifyLogin($email, $password) {
        $admin = $this->getAdminByEmail($email);
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Remove password from returned data
            unset($admin['password']);
            return $admin;
        }
        
        return false;
    }

    public function verifyCurrentPassword($email, $currentPassword) {
        $admin = $this->getAdminByEmail($email);
        
        if ($admin && password_verify($currentPassword, $admin['password'])) {
            return true;
        }
        
        return false;
    }

    public function updateAdminPassword($email, $newPassword) {
        $admins = $this->db->getAdmins();
        
        for ($i = 0; $i < count($admins); $i++) {
            if ($admins[$i]['email'] === $email) {
                $admins[$i]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                $admins[$i]['updated_at'] = date('Y-m-d H:i:s');
                
                $this->db->saveAdmins($admins);
                return true;
            }
        }
        return false;
    }

    public function createAdmin($adminData) {
        $admins = $this->db->getAdmins();
        
        $newAdmin = [
            'id' => $this->db->getNextId($admins),
            'email' => $adminData['email'],
            'password' => password_hash($adminData['password'], PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $admins[] = $newAdmin;
        $this->db->saveAdmins($admins);
        
        // Remove password from returned data
        unset($newAdmin['password']);
        return $newAdmin;
    }

    public function getAllAdmins() {
        $admins = $this->db->getAdmins();
        // Remove passwords from returned data
        foreach ($admins as &$admin) {
            unset($admin['password']);
        }
        return $admins;
    }

    public function deleteAdmin($adminId) {
        $admins = $this->db->getAdmins();
        
        // Prevent deletion if it's the last admin
        if (count($admins) <= 1) {
            throw new Exception("Cannot delete the last admin account");
        }
        
        for ($i = 0; $i < count($admins); $i++) {
            if ($admins[$i]['id'] == $adminId) {
                array_splice($admins, $i, 1);
                $this->db->saveAdmins($admins);
                return true;
            }
        }
        return false;
    }

    public function updateAdminProfile($adminId, $email) {
        $admins = $this->db->getAdmins();
        
        for ($i = 0; $i < count($admins); $i++) {
            if ($admins[$i]['id'] == $adminId) {
                $admins[$i]['email'] = $email;
                $admins[$i]['updated_at'] = date('Y-m-d H:i:s');
                
                $this->db->saveAdmins($admins);
                
                // Remove password from returned data
                $result = $admins[$i];
                unset($result['password']);
                return $result;
            }
        }
        return null;
    }
}

?>