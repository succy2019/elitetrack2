<?php

require_once __DIR__ . '/../models/SimpleUser.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * Simple User Controller - File-based storage
 */
class SimpleUserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new SimpleUser();
    }

    public function getAllUsers() {
        try {
            AuthMiddleware::verifyToken();

            $users = $this->userModel->getAllUsers();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'users' => $users
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get users: ' . $e->getMessage()]);
        }
    }

    public function createUser() {
        try {
            AuthMiddleware::verifyToken();

            $input = json_decode(file_get_contents('php://input'), true);
            
            $requiredFields = ['email', 'name', 'amount', 'status', 'phone', 'address', 'message'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty(trim($input[$field]))) {
                    http_response_code(400);
                    echo json_encode(['error' => ucfirst($field) . ' is required']);
                    return;
                }
            }

            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid email format']);
                return;
            }

            if ($this->userModel->getUserByEmail($input['email'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Email already exists']);
                return;
            }

            $validStatuses = ['pending', 'processing', 'active', 'completed', 'inactive'];
            if (!in_array($input['status'], $validStatuses)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                return;
            }

            $user = $this->userModel->createUser($input);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function getUserByTrackId($trackId) {
        try {
            if (empty($trackId)) {
                http_response_code(400);
                echo json_encode(['error' => 'Track ID is required']);
                return;
            }

            $user = $this->userModel->getUserByTrackId($trackId);

            if ($user) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'user' => $user
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get user: ' . $e->getMessage()]);
        }
    }

    public function updateUser() {
        try {
            AuthMiddleware::verifyToken();

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
                return;
            }

            $userId = $input['id'];

            if (!$this->userModel->getUserById($userId)) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }

            $requiredFields = ['email', 'name', 'amount', 'status', 'phone', 'address', 'message'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    http_response_code(400);
                    echo json_encode(['error' => ucfirst($field) . ' is required']);
                    return;
                }
            }

            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid email format']);
                return;
            }

            $existingUser = $this->userModel->getUserByEmail($input['email']);
            if ($existingUser && $existingUser['id'] != $userId) {
                http_response_code(400);
                echo json_encode(['error' => 'Email already exists for another user']);
                return;
            }

            $validStatuses = ['pending', 'processing', 'active', 'completed', 'inactive'];
            if (!in_array($input['status'], $validStatuses)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                return;
            }

            $user = $this->userModel->updateUserProfile($userId, $input);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function updateUserProgress() {
        try {
            AuthMiddleware::verifyToken();

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || !isset($input['progress_percentage'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID and progress percentage are required']);
                return;
            }

            $userId = $input['id'];
            $progressPercentage = intval($input['progress_percentage']);

            if ($progressPercentage < 0 || $progressPercentage > 100) {
                http_response_code(400);
                echo json_encode(['error' => 'Progress percentage must be between 0 and 100']);
                return;
            }

            if (!$this->userModel->getUserById($userId)) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }

            $user = $this->userModel->updateUserProgress($userId, $progressPercentage);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'User progress updated successfully',
                'user' => $user
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update user progress: ' . $e->getMessage()]);
        }
    }

    public function deleteUser() {
        try {
            AuthMiddleware::verifyToken();

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
                return;
            }

            $userId = $input['id'];

            if (!$this->userModel->getUserById($userId)) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }

            $success = $this->userModel->deleteUser($userId);

            if ($success) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete user']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }

    public function getUserStats() {
        try {
            AuthMiddleware::verifyToken();

            $stats = $this->userModel->getUserStats();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get user statistics: ' . $e->getMessage()]);
        }
    }

    public function searchUsers() {
        try {
            AuthMiddleware::verifyToken();

            $criteria = [];
            
            if (isset($_GET['email'])) {
                $criteria['email'] = $_GET['email'];
            }
            
            if (isset($_GET['name'])) {
                $criteria['name'] = $_GET['name'];
            }
            
            if (isset($_GET['status'])) {
                $criteria['status'] = $_GET['status'];
            }
            
            if (isset($_GET['track_id'])) {
                $criteria['track_id'] = $_GET['track_id'];
            }

            $users = $this->userModel->searchUsers($criteria);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'users' => $users
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to search users: ' . $e->getMessage()]);
        }
    }

    public function getUserById($id) {
        try {
            AuthMiddleware::verifyToken();

            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
                return;
            }

            $user = $this->userModel->getUserById($id);

            if ($user) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'user' => $user
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get user: ' . $e->getMessage()]);
        }
    }
}

?>