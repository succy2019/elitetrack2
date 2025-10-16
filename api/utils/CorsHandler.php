<?php

/**
 * CORS and Security Headers Utility
 */
class CorsHandler {
    
    /**
     * Set CORS headers
     */
    public static function setCorsHeaders() {
        // Allow requests from specific origins (update for production)
        $allowedOrigins = [
            // Local development
            'http://localhost:3000',
            'http://localhost:8000',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:8000',
            'http://localhost',
            
            // Frontend domains (where users visit your app)
            'https://your-app.vercel.app',           // 🔥 UPDATE: Your Vercel frontend URL
            'https://your-custom-domain.com',       // 🔥 UPDATE: Custom domain pointing to Vercel frontend
            
            // Example:
            // 'https://elite-track.vercel.app',     // Vercel auto-generated URL
            // 'https://elitetrack.com',             // Your custom domain
            
            // Vercel preview deployments (auto-generated)
            // 'https://elite-track-git-main-yourusername.vercel.app'
        ];

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . $origin);
        } else {
            header("Access-Control-Allow-Origin: *"); // For development only
        }

        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400"); // Cache preflight for 24 hours
    }

    /**
     * Set security headers
     */
    public static function setSecurityHeaders() {
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Content-Security-Policy: default-src 'self'");
    }

    /**
     * Handle preflight OPTIONS request
     */
    public static function handlePreflight() {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            self::setCorsHeaders();
            http_response_code(204);
            exit();
        }
    }

    /**
     * Set JSON content type
     */
    public static function setJsonHeader() {
        header("Content-Type: application/json; charset=UTF-8");
    }
}

?>