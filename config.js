/**
 * Configuration file for Elite Track system
 * Frontend on Vercel + PHP Backend on separate server
 */

// Auto-detect environment based on hostname
const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const isXampp = window.location.pathname.includes('/elitetrack2/');
const isVercel = window.location.hostname.includes('.vercel.app');
const isNetlify = window.location.hostname.includes('.netlify.app');
const isProductionDomain = window.location.hostname === 'track.digitalexpertstocknetwork.live';

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
        API_BASE_URL: 'https://track.digitalexpertstocknetwork.live/api',
        APP_BASE_URL: '',
        ENVIRONMENT: 'production'
    }
};

// Auto-select configuration based on environment
let CURRENT_CONFIG;
if (isLocalhost && isXampp) {
    CURRENT_CONFIG = CONFIG.local;
} else {
    CURRENT_CONFIG = CONFIG.production;
}

// Override for development testing - if you want to test production API from localhost
// Uncomment the line below to force production API:
// CURRENT_CONFIG = CONFIG.production;

// Export configuration
window.EliteTrackConfig = CURRENT_CONFIG;

console.log('Elite Track Environment:', CURRENT_CONFIG.ENVIRONMENT);
console.log('API Base URL:', CURRENT_CONFIG.API_BASE_URL);
console.log('App Base URL:', CURRENT_CONFIG.APP_BASE_URL);