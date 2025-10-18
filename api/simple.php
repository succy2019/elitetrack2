<?php

require_once __DIR__ . '/controllers/SimpleAuthController.php';
require_once __DIR__ . '/controllers/SimpleUserController.php';
require_once __DIR__ . '/utils/CorsHandler.php';

/**
 * Simple API Router - File-based storage version
 */
class SimpleApiRouter {
    private $authController;
    private $userController;

    public function __construct() {
        $this->authController = new SimpleAuthController();
        $this->userController = new SimpleUserController();
    }

    public function route() {
        // Handle CORS and set headers
        CorsHandler::handlePreflight();
        CorsHandler::setCorsHeaders();
        CorsHandler::setSecurityHeaders();
        CorsHandler::setJsonHeader();

        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->getPath();

        try {
            // Authentication routes
            if (preg_match('#^/api/auth/login$#', $path) && $method === 'POST') {
                $this->authController->login();
                return;
            }

            if (preg_match('#^/api/auth/change-password$#', $path) && $method === 'PUT') {
                $this->authController->changePassword();
                return;
            }

            if (preg_match('#^/api/auth/verify$#', $path) && $method === 'GET') {
                $this->authController->verifyToken();
                return;
            }

            if (preg_match('#^/api/auth/logout$#', $path) && $method === 'POST') {
                $this->authController->logout();
                return;
            }

            if (preg_match('#^/api/auth/profile$#', $path) && $method === 'GET') {
                $this->authController->getProfile();
                return;
            }

            // User routes
            if (preg_match('#^/api/users/all$#', $path) && $method === 'GET') {
                $this->userController->getAllUsers();
                return;
            }

            if (preg_match('#^/api/users/new$#', $path) && $method === 'POST') {
                $this->userController->createUser();
                return;
            }

            if (preg_match('#^/api/users/update$#', $path) && $method === 'PUT') {
                $this->userController->updateUser();
                return;
            }

            if (preg_match('#^/api/users/progress$#', $path) && $method === 'PUT') {
                $this->userController->updateUserProgress();
                return;
            }

            if (preg_match('#^/api/users/delete$#', $path) && $method === 'DELETE') {
                $this->userController->deleteUser();
                return;
            }

            if (preg_match('#^/api/users/stats$#', $path) && $method === 'GET') {
                $this->userController->getUserStats();
                return;
            }

            if (preg_match('#^/api/users/search$#', $path) && $method === 'GET') {
                $this->userController->searchUsers();
                return;
            }

            // Track user by track ID (public endpoint)
            if (preg_match('#^/api/users/track/(.+)$#', $path, $matches) && $method === 'GET') {
                $trackId = $matches[1];
                $this->userController->getUserByTrackId($trackId);
                return;
            }

            // Get user by ID
            if (preg_match('#^/api/users/(\d+)$#', $path, $matches) && $method === 'GET') {
                $userId = $matches[1];
                $this->userController->getUserById($userId);
                return;
            }

            // Default route - API info
            if ($path === '/api' || $path === '/api/' || $path === '' || $path === '/') {
                $this->apiInfo();
                return;
            }

            // Route not found
            $this->notFound();

        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    private function getPath() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return rtrim($path, '/');
    }

    private function apiInfo() {
        http_response_code(200);
        echo json_encode([
            'name' => 'Elite Track API (Simple Version)',
            'version' => '1.0.0',
            'description' => 'PHP API for Elite Management Tracking System - File-based storage',
            'storage' => 'JSON files',
            'status' => 'Active',
            'endpoints' => [
                'Authentication' => [
                    'POST /api/auth/login' => 'Admin login',
                    'PUT /api/auth/change-password' => 'Change admin password',
                    'GET /api/auth/verify' => 'Verify token',
                    'POST /api/auth/logout' => 'Logout',
                    'GET /api/auth/profile' => 'Get admin profile'
                ],
                'Users' => [
                    'GET /api/users/all' => 'Get all users (protected)',
                    'POST /api/users/new' => 'Create new user (protected)',
                    'PUT /api/users/update' => 'Update user (protected)',
                    'PUT /api/users/progress' => 'Update user progress (protected)',
                    'DELETE /api/users/delete' => 'Delete user (protected)',
                    'GET /api/users/stats' => 'Get user statistics (protected)',
                    'GET /api/users/search' => 'Search users (protected)',
                    'GET /api/users/track/{trackId}' => 'Get user by track ID (public)',
                    'GET /api/users/{id}' => 'Get user by ID (protected)'
                ]
            ],
            'default_admin' => [
                'email' => 'admin@elitetrack.com',
                'password' => 'admin123'
            ],
            'note' => 'API is ready to use with your frontend!'
        ]);
    }

    private function notFound() {
        http_response_code(404);
        echo json_encode([
            'error' => 'Endpoint not found',
            'path' => $this->getPath(),
            'method' => $_SERVER['REQUEST_METHOD'],
            'available_endpoints' => 'Visit /api for endpoint documentation'
        ]);
    }

    private function handleError($exception) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Internal server error',
            'message' => $exception->getMessage()
        ]);
    }

    private function debugStatus() {
        try {
            $dataDir = __DIR__ . '/../data';
            $usersFile = $dataDir . '/users.json';
            $adminsFile = $dataDir . '/admins.json';
            
            $status = [
                'timestamp' => date('Y-m-d H:i:s'),
                'api_status' => 'working',
                'storage_type' => 'file-based',
                'data_directory' => [
                    'path' => $dataDir,
                    'exists' => is_dir($dataDir),
                    'writable' => is_writable($dataDir),
                    'absolute_path' => realpath($dataDir) ?: 'N/A'
                ],
                'users_file' => [
                    'path' => $usersFile,
                    'exists' => file_exists($usersFile),
                    'readable' => is_readable($usersFile),
                    'writable' => is_writable($usersFile),
                    'size' => file_exists($usersFile) ? filesize($usersFile) : 0,
                    'absolute_path' => realpath($usersFile) ?: 'N/A'
                ],
                'admins_file' => [
                    'path' => $adminsFile,
                    'exists' => file_exists($adminsFile),
                    'readable' => is_readable($adminsFile),
                    'writable' => is_writable($adminsFile),
                    'absolute_path' => realpath($adminsFile) ?: 'N/A'
                ],
                'server_info' => [
                    'php_version' => phpversion(),
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
                    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
                    'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'unknown',
                    'request_method' => $_SERVER['REQUEST_METHOD'],
                    'request_uri' => $_SERVER['REQUEST_URI'],
                    'http_origin' => $_SERVER['HTTP_ORIGIN'] ?? 'none'
                ],
                'routing_info' => [
                    'current_file' => __FILE__,
                    'api_base_path' => dirname(__FILE__),
                    'project_root' => dirname(dirname(__FILE__))
                ]
            ];
            
            // Try to read users count
            if (file_exists($usersFile) && is_readable($usersFile)) {
                $data = file_get_contents($usersFile);
                if ($data !== false) {
                    $users = json_decode($data, true);
                    $status['users_count'] = is_array($users) ? count($users) : 0;
                    $status['users_data_valid'] = is_array($users);
                    $status['users_sample'] = is_array($users) && count($users) > 0 ? $users[0] : null;
                    $status['raw_file_content'] = strlen($data) < 500 ? $data : substr($data, 0, 500) . '...';
                } else {
                    $status['users_read_error'] = 'Failed to read file content';
                }
            } else {
                $status['users_file_issue'] = 'File does not exist or is not readable';
            }
            
            // Test file creation
            $testFile = $dataDir . '/test_write.tmp';
            $status['write_test'] = [
                'test_file' => $testFile,
                'can_write' => is_writable($dataDir),
                'write_success' => file_put_contents($testFile, 'test') !== false
            ];
            if (file_exists($testFile)) {
                unlink($testFile); // Clean up test file
            }
            
            http_response_code(200);
            echo json_encode($status, JSON_PRETTY_PRINT);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'api_status' => 'error',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], JSON_PRETTY_PRINT);
        }
    }
}

// Initialize and run the router
$router = new SimpleApiRouter();
$router->route();

?>