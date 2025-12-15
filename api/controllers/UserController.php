<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * User Controller
 * Handles all user-related API endpoints
 */
class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Get all users
     * GET /api/users/all
     */
    public function getAllUsers() {
        try {
            // Note: This endpoint is now public (no authentication required)

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

    /**
     * Create new user
     * POST /api/users/new
     */
    public function createUser() {
        try {
            // Note: This endpoint is now public (no authentication required)

            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields (email and phone are now optional)
            $requiredFields = ['name', 'amount', 'status', 'address', 'message'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty(trim($input[$field]))) {
                    http_response_code(400);
                    echo json_encode(['error' => ucfirst($field) . ' is required']);
                    return;
                }
            }

            // Validate email format if provided
            if (isset($input['email']) && !empty($input['email'])) {
                if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid email format']);
                    return;
                }

                // Check if email already exists
                if ($this->userModel->getUserByEmail($input['email'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email already exists']);
                    return;
                }
            }

            // Validate status
            $validStatuses = ['pending', 'processing', 'active', 'completed', 'inactive'];
            if (!in_array($input['status'], $validStatuses)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                return;
            }

            // Create user
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

    /**
     * Get user by track ID (public endpoint for tracking)
     * GET /api/users/track/{trackId}
     */
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

    /**
     * Update user
     * PUT /api/users/update
     */
    public function updateUser() {
        try {
            // Note: This endpoint is now public (no authentication required)

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
                return;
            }

            $userId = $input['id'];

            // Check if user exists
            if (!$this->userModel->getUserById($userId)) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }

            // Validate required fields (email and phone are now optional)
            $requiredFields = ['name', 'amount', 'status', 'address', 'message'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    http_response_code(400);
                    echo json_encode(['error' => ucfirst($field) . ' is required']);
                    return;
                }
            }

            // Validate email format if provided
            if (isset($input['email']) && !empty($input['email'])) {
                if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid email format']);
                    return;
                }

                // Check if email exists for another user
                $existingUser = $this->userModel->getUserByEmail($input['email']);
                if ($existingUser && $existingUser['id'] != $userId) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email already exists for another user']);
                    return;
                }
            }

            // Validate status
            $validStatuses = ['pending', 'processing', 'active', 'completed', 'inactive'];
            if (!in_array($input['status'], $validStatuses)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                return;
            }

            // Update user
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

    /**
     * Update user progress
     * PUT /api/users/progress
     */
    public function updateUserProgress() {
        try {
            // Note: This endpoint is now public (no authentication required)

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || !isset($input['progress_percentage'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID and progress percentage are required']);
                return;
            }

            $userId = $input['id'];
            $progressPercentage = intval($input['progress_percentage']);

            // Validate progress percentage
            if ($progressPercentage < 0 || $progressPercentage > 100) {
                http_response_code(400);
                echo json_encode(['error' => 'Progress percentage must be between 0 and 100']);
                return;
            }

            // Check if user exists
            if (!$this->userModel->getUserById($userId)) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }

            // Update progress
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

    /**
     * Delete user
     * DELETE /api/users/delete
     */
    public function deleteUser() {
        try {
            // Note: This endpoint is now public (no authentication required)

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['id']) || empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
                return;
            }

            $userId = $input['id'];

            // Check if user exists
            if (!$this->userModel->getUserById($userId)) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                return;
            }

            // Delete user
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

    /**
     * Get user statistics
     * GET /api/users/stats
     */
    public function getUserStats() {
        try {
            // Note: This endpoint is now public (no authentication required)

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

    /**
     * Search users
     * GET /api/users/search
     */
    public function searchUsers() {
        try {
            // Note: This endpoint is now public (no authentication required)

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

    /**
     * Get user by ID
     * GET /api/users/{id}
     */
    public function getUserById($id) {
        try {
            // Note: This endpoint is now public (no authentication required)

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