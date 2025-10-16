/**
 * Configuration file for Elite Track system
 * Frontend on Vercel + PHP Backend on separate server
 */

// Auto-detect environment based on hostname
const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const isXampp = window.location.pathname.includes('/elitetrack2/');
const isVercel = window.location.hostname.includes('.vercel.app') || window.location.hostname.includes('yourdomain.com');

// Environment configuration
const CONFIG = {
    // Local XAMPP development
    local: {
        API_BASE_URL: 'http://localhost/elitetrack2/api',
        APP_BASE_URL: '/elitetrack2',
        ENVIRONMENT: 'development'
    },
    
    // Production: Frontend on Vercel, Backend on separate server
    production: {
        API_BASE_URL: 'https://track.digitalexpertstocknetwork.live/api', // 🔥 UPDATE THIS WITH YOUR PHP SERVER URL
        APP_BASE_URL: '',
        ENVIRONMENT: 'production'
    }
};

// Auto-select configuration based on environment
const CURRENT_CONFIG = (isLocalhost && isXampp) ? CONFIG.local : CONFIG.production;

// Export configuration
window.EliteTrackConfig = CURRENT_CONFIG;

console.log('Elite Track Environment:', CURRENT_CONFIG.ENVIRONMENT);
console.log('API Base URL:', CURRENT_CONFIG.API_BASE_URL);
console.log('App Base URL:', CURRENT_CONFIG.APP_BASE_URL);