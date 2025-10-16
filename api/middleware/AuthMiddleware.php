<?php

require_once __DIR__ . '/../utils/JWT.php';

/**
 * Authentication Middleware
 * Verifies JWT tokens for protected routes
 */
class AuthMiddleware {
    
    /**
     * Verify authentication token
     */
    public static function verifyToken() {
        try {
            $token = JWT::getBearerToken();
            
            if (!$token) {
                http_response_code(401);
                echo json_encode(['error' => 'Access token is required']);
                exit;
            }

            $payload = JWT::decode($token);
            
            // Store admin info in global variable for use in controllers
            $GLOBALS['auth_admin'] = $payload;
            
            return $payload;

        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Get authenticated admin data
     */
    public static function getAuthenticatedAdmin() {
        return isset($GLOBALS['auth_admin']) ? $GLOBALS['auth_admin'] : null;
    }

    /**
     * Check if request is authenticated
     */
    public static function isAuthenticated() {
        try {
            $token = JWT::getBearerToken();
            if (!$token) return false;
            
            JWT::decode($token);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

?>