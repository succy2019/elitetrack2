# 🚨 "Failed to fetch" Error - Troubleshooting Guide

## What "Failed to fetch" means:
This error occurs when the browser cannot connect to the API server. It's usually one of these issues:

## 🔍 **Step 1: Use the Debug Tool**
1. Open `api-debug.html` in your browser
2. Click "Test Get Users" - if this works, the API is accessible
3. Click "Test Create User" - if this fails, we know it's a CORS or method issue
4. Click "Test CORS Preflight" - this checks CORS configuration

## 🌐 **Step 2: Check Your Environment**

### If you're on **localhost/XAMPP**:
- API URL should be: `http://localhost/elitetrack2/api`
- Make sure XAMPP is running
- Make sure the URL is correct in `config.js`

### If you're on **Vercel** or production:
- API URL should be: `https://track.digitalexpertstocknetwork.live/api`
- Check if the API server is actually running at that URL
- Try visiting the API URL directly in your browser

## 🔧 **Step 3: Common Fixes**

### Fix 1: Update your Vercel domain in CORS
If your Vercel app has a different URL, add it to `api/utils/CorsHandler.php`:

```php
'https://YOUR-VERCEL-APP.vercel.app',  // Add your actual Vercel URL
```

### Fix 2: Check API Server Status
Visit your API URL directly: `https://track.digitalexpertstocknetwork.live/api`
- Should show API information
- If it shows 404 or doesn't load, the API server is down

### Fix 3: Test with Force Production Config
In `config.js`, uncomment this line to force production API:
```javascript
CURRENT_CONFIG = CONFIG.production;
```

### Fix 4: Check Network Tab
1. Open browser DevTools (F12)
2. Go to Network tab
3. Try creating a user
4. Look for the failed request - check:
   - Status code
   - Response body
   - Request headers

## 🐛 **Step 4: Specific Error Types**

### Error: "Failed to fetch"
- **Cause**: Network/CORS issue
- **Fix**: Check CORS configuration, API server status

### Error: "TypeError: Failed to fetch"
- **Cause**: CORS blocking the request
- **Fix**: Add your domain to CORS allowlist

### Error: Network timeout
- **Cause**: API server not responding
- **Fix**: Check if API server is running

### Error: 404 Not Found
- **Cause**: Wrong API URL
- **Fix**: Check `config.js` API_BASE_URL

## 📱 **Step 5: Quick Tests**

### Test 1: Direct API Call
```bash
curl https://track.digitalexpertstocknetwork.live/api/users/all
```

### Test 2: Browser Direct Visit
Visit: `https://track.digitalexpertstocknetwork.live/api`

### Test 3: CORS Test
```javascript
fetch('https://track.digitalexpertstocknetwork.live/api/users/all')
  .then(r => r.json())
  .then(console.log)
  .catch(console.error);
```

## 🚀 **Step 6: Emergency Workaround**

If all else fails, temporarily use a CORS proxy:
```javascript
// In config.js, change production API URL to:
API_BASE_URL: 'https://cors-anywhere.herokuapp.com/https://track.digitalexpertstocknetwork.live/api'
```
⚠️ **Note**: This is only for testing - don't use in production!

---

## 🎯 **Most Likely Fixes:**

1. **Add your Vercel URL to CORS** (90% of cases)
2. **Check if API server is running** (8% of cases)  
3. **Wrong config.js settings** (2% of cases)

Run the debug tool first - it will tell you exactly what's wrong!