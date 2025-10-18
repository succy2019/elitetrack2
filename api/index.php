<?php

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/utils/CorsHandler.php';

/**
 * API Router - Main entry point for all API requests
 * Handles routing to appropriate controllers based on URL patterns
 */
class ApiRouter {
    private $authController;
    private $userController;

    public function __construct() {
        $this->authController = new AuthController();
        $this->userController = new UserController();
    }

    /**
     * Route the request to appropriate controller
     */
    public function route() {
        // Handle CORS and set headers
        CorsHandler::handlePreflight();
        CorsHandler::setCorsHeaders();
        CorsHandler::setSecurityHeaders();
        CorsHandler::setJsonHeader();
        CorsHandler::setCacheControlHeaders();

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
            if ($path === '/api' || $path === '/api/') {
                $this->apiInfo();
                return;
            }

            // Route not found
            $this->notFound();

        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Get the request path
     */
    private function getPath() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rtrim($path, '/');
        
        // Remove the project folder from the path to match routing patterns
        $path = str_replace('/elitetrack2', '', $path);
        
        return $path;
    }

    /**
     * API information endpoint
     */
    private function apiInfo() {
        http_response_code(200);
        echo json_encode([
            'name' => 'Elite Track API',
            'version' => '1.0.0',
            'description' => 'PHP API for Elite Management Tracking System',
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
            ]
        ]);
    }

    /**
     * Handle 404 Not Found
     */
    private function notFound() {
        http_response_code(404);
        echo json_encode([
            'error' => 'Endpoint not found',
            'path' => $this->getPath(),
            'method' => $_SERVER['REQUEST_METHOD']
        ]);
    }

    /**
     * Handle errors
     */
    private function handleError($exception) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Internal server error',
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
    }
}

// Initialize and run the router
$router = new ApiRouter();
$router->route();

?>