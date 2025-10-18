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
            'http://localhost:80',
            'http://localhost:8080',
            'http://127.0.0.1',
            'http://127.0.0.1:80',
            'http://127.0.0.1:8080',
            'http://localhost/elitetrack2',  // ✅ Added for XAMPP localhost
            'http://127.0.0.1/elitetrack2',  // ✅ Added for XAMPP localhost
            
            // Frontend domains (where users visit your app)
            'https://transtrack-three.vercel.app',  // ✅ Your actual Vercel app
            'https://track.digitalexpertstocknetwork.live',  // ✅ Added production backend domain
            'https://your-custom-domain.com',       // 🔥 UPDATE: Custom domain if you have one
            
            // Add other preview deployments if needed
            'https://transtrack-git-main-succy2019.vercel.app', // Git branch URL pattern
            
            // Add wildcard support for Vercel preview deployments
            // Note: You'll need to add specific preview URLs as needed
        ];

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        // Log the origin for debugging
        error_log("CORS Origin Request: " . $origin);
        
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . $origin);
            error_log("CORS: Allowed specific origin - " . $origin);
        } else {
            // Check if it's a Vercel preview deployment
            if (strpos($origin, 'vercel.app') !== false || strpos($origin, 'netlify.app') !== false) {
                header("Access-Control-Allow-Origin: " . $origin);
                error_log("CORS: Allowed deployment platform - " . $origin);
            } else {
                header("Access-Control-Allow-Origin: *"); // For development only
                error_log("CORS: Using wildcard for - " . $origin);
            }
        }

        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control, Pragma, Expires, X-Request-ID");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400"); // Cache preflight for 24 hours
        header("Access-Control-Expose-Headers: Content-Type, Cache-Control, Pragma, Expires");
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

    /**
     * Set cache control headers to prevent caching of API responses
     */
    public static function setCacheControlHeaders() {
        header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("ETag: " . md5(microtime() . rand()));
    }
}

?>