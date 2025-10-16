<?php

require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * Authentication Controller
 * Handles admin login, logout, and password changes
 */
class AuthController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new Admin();
    }

    /**
     * Admin login endpoint
     * POST /api/auth/login
     */
    public function login() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['email']) || !isset($input['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Email and password are required']);
                return;
            }

            $email = trim($input['email']);
            $password = $input['password'];

            // Verify credentials
            $admin = $this->adminModel->verifyLogin($email, $password);
            
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid email or password']);
                return;
            }

            // Generate JWT token
            $payload = [
                'admin_id' => $admin['id'],
                'email' => $admin['email'],
                'role' => 'admin'
            ];

            $token = JWT::encode($payload);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'admin' => [
                    'id' => $admin['id'],
                    'email' => $admin['email']
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Login failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Change password endpoint
     * PUT /api/auth/change-password
     */
    public function changePassword() {
        try {
            // Verify authentication
            AuthMiddleware::verifyToken();
            $authAdmin = AuthMiddleware::getAuthenticatedAdmin();

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['currentPassword']) || !isset($input['newPassword'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Current password and new password are required']);
                return;
            }

            $currentPassword = $input['currentPassword'];
            $newPassword = $input['newPassword'];

            // Validate new password
            if (strlen($newPassword) < 6) {
                http_response_code(400);
                echo json_encode(['error' => 'New password must be at least 6 characters long']);
                return;
            }

            // Verify current password
            if (!$this->adminModel->verifyCurrentPassword($authAdmin['email'], $currentPassword)) {
                http_response_code(400);
                echo json_encode(['error' => 'Current password is incorrect']);
                return;
            }

            // Update password
            $success = $this->adminModel->updateAdminPassword($authAdmin['email'], $newPassword);
            
            if ($success) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Password changed successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update password']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Password change failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Verify token endpoint
     * GET /api/auth/verify
     */
    public function verifyToken() {
        try {
            AuthMiddleware::verifyToken();
            $authAdmin = AuthMiddleware::getAuthenticatedAdmin();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Token is valid',
                'admin' => [
                    'id' => $authAdmin['admin_id'],
                    'email' => $authAdmin['email']
                ]
            ]);

        } catch (Exception $e) {
            // AuthMiddleware already handles the error response
        }
    }

    /**
     * Logout endpoint (client-side token removal)
     * POST /api/auth/logout
     */
    public function logout() {
        try {
            // For JWT, logout is handled client-side by removing the token
            // Server-side logout would require token blacklisting
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Logout failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Get current admin profile
     * GET /api/auth/profile
     */
    public function getProfile() {
        try {
            AuthMiddleware::verifyToken();
            $authAdmin = AuthMiddleware::getAuthenticatedAdmin();

            $admin = $this->adminModel->getAdminById($authAdmin['admin_id']);
            
            if ($admin) {
                unset($admin['password']); // Remove password from response
                
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'admin' => $admin
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Admin not found']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get profile: ' . $e->getMessage()]);
        }
    }
}

?>