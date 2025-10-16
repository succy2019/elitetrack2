<?php

require_once __DIR__ . '/../config/simple_database.php';

/**
 * Simple User Model - File-based storage
 */
class SimpleUser {
    private $db;

    public function __construct() {
        global $simpleDb;
        $this->db = $simpleDb;
    }

    public function createUser($userData) {
        $users = $this->db->getUsers();
        
        $newUser = [
            'id' => $this->db->getNextId($users),
            'email' => $userData['email'],
            'name' => $userData['name'],
            'amount' => $userData['amount'],
            'status' => $userData['status'],
            'phone' => $userData['phone'],
            'address' => $userData['address'],
            'message' => $userData['message'],
            'track_id' => $this->db->generateTrackId(),
            'payment_to' => $userData['payment_to'] ?? 'Merchant Commercial Bank',
            'account_number' => $userData['account_number'] ?? '0012239988',
            'estimated_processing_time' => $userData['estimated_processing_time'] ?? '1-2 minutes',
            'money_due' => $userData['money_due'] ?? $userData['amount'],
            'progress_percentage' => intval($userData['progress_percentage'] ?? 0),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $users[] = $newUser;
        $this->db->saveUsers($users);
        
        return $newUser;
    }

    public function getAllUsers() {
        $users = $this->db->getUsers();
        // Sort by created_at descending
        usort($users, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        return $users;
    }

    public function getUserById($id) {
        $users = $this->db->getUsers();
        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }

    public function getUserByEmail($email) {
        $users = $this->db->getUsers();
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }

    public function getUserByTrackId($trackId) {
        $users = $this->db->getUsers();
        foreach ($users as $user) {
            if ($user['track_id'] === $trackId) {
                return $user;
            }
        }
        return null;
    }

    public function updateUserProfile($userId, $userData) {
        $users = $this->db->getUsers();
        
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['id'] == $userId) {
                $users[$i]['name'] = $userData['name'];
                $users[$i]['email'] = $userData['email'];
                $users[$i]['amount'] = $userData['amount'];
                $users[$i]['status'] = $userData['status'];
                $users[$i]['message'] = $userData['message'];
                $users[$i]['address'] = $userData['address'];
                $users[$i]['phone'] = $userData['phone'];
                $users[$i]['payment_to'] = $userData['payment_to'] ?? 'Merchant Commercial Bank';
                $users[$i]['account_number'] = $userData['account_number'] ?? '0012239988';
                $users[$i]['estimated_processing_time'] = $userData['estimated_processing_time'] ?? '1-2 minutes';
                $users[$i]['money_due'] = $userData['money_due'] ?? $userData['amount'];
                $users[$i]['progress_percentage'] = intval($userData['progress_percentage'] ?? 0);
                $users[$i]['updated_at'] = date('Y-m-d H:i:s');
                
                $this->db->saveUsers($users);
                return $users[$i];
            }
        }
        return null;
    }

    public function updateUserProgress($userId, $progressPercentage) {
        $users = $this->db->getUsers();
        
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['id'] == $userId) {
                $users[$i]['progress_percentage'] = intval($progressPercentage);
                $users[$i]['updated_at'] = date('Y-m-d H:i:s');
                
                $this->db->saveUsers($users);
                return $users[$i];
            }
        }
        return null;
    }

    public function deleteUser($userId) {
        $users = $this->db->getUsers();
        
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['id'] == $userId) {
                array_splice($users, $i, 1);
                $this->db->saveUsers($users);
                return true;
            }
        }
        return false;
    }

    public function getUserStats() {
        $users = $this->db->getUsers();
        $stats = [
            'total' => count($users),
            'active' => 0,
            'pending' => 0,
            'processing' => 0,
            'completed' => 0
        ];
        
        foreach ($users as $user) {
            if (isset($stats[$user['status']])) {
                $stats[$user['status']]++;
            }
        }
        
        return $stats;
    }

    public function searchUsers($criteria) {
        $users = $this->db->getUsers();
        $filtered = [];
        
        foreach ($users as $user) {
            $match = true;
            
            if (!empty($criteria['email']) && stripos($user['email'], $criteria['email']) === false) {
                $match = false;
            }
            
            if (!empty($criteria['name']) && stripos($user['name'], $criteria['name']) === false) {
                $match = false;
            }
            
            if (!empty($criteria['status']) && $user['status'] !== $criteria['status']) {
                $match = false;
            }
            
            if (!empty($criteria['track_id']) && stripos($user['track_id'], $criteria['track_id']) === false) {
                $match = false;
            }
            
            if ($match) {
                $filtered[] = $user;
            }
        }
        
        return $filtered;
    }
}

?>