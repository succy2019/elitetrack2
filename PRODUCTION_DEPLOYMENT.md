# Elite Track - Production Deployment Guide

## 🚀 Moving from XAMPP to Online Server

### Prerequisites
- Web hosting with PHP 7.4+ support
- MySQL database access
- Domain name or subdomain

### 📁 File Structure for Online Deployment

```
your-domain.com/
├── public_html/ (or htdocs/)
│   ├── index.html
│   ├── dashboard.html
│   ├── add-user.html
│   ├── change-password.html
│   ├── track.html
│   ├── config.js
│   └── api/
│       ├── .htaccess
│       ├── index.php
│       ├── init.php
│       ├── config/
│       ├── controllers/
│       ├── middleware/
│       ├── models/
│       └── utils/
```

## 🔧 Configuration Changes Needed

### 1. Database Configuration
Update `api/config/database.php`:

```php
// For shared hosting
private $host = 'localhost';           // Usually localhost
private $db_name = 'your_db_name';     // Your hosting provider's DB name
private $username = 'your_db_user';    // Your DB username
private $password = 'your_db_password'; // Your DB password

// For VPS/Dedicated servers, you might use:
// private $host = 'your-server-ip';
```

### 2. Frontend Configuration
The `config.js` file will automatically detect the environment, but you can manually override:

**For root domain deployment (example.com):**
```javascript
production: {
    API_BASE_URL: '/api',
    APP_BASE_URL: '',
    ENVIRONMENT: 'production'
}
```

**For subdirectory deployment (example.com/elitetrack):**
```javascript
production: {
    API_BASE_URL: '/elitetrack/api',
    APP_BASE_URL: '/elitetrack',
    ENVIRONMENT: 'production'
}
```

### 3. Update HTML Files to Use Config
Add this script tag to ALL HTML files (index.html, dashboard.html, etc.):

```html
<script src="config.js"></script>
```

Then update JavaScript API calls to use the config:

```javascript
// Instead of: fetch('api/auth/login')
fetch(`${window.EliteTrackConfig.API_BASE_URL}/auth/login`)

// Instead of: window.location.href = 'dashboard.html'
window.location.href = `${window.EliteTrackConfig.APP_BASE_URL}/dashboard.html`
```

## 📋 Step-by-Step Deployment Process

### Step 1: Prepare Files
1. Copy all files from `C:\xampp\htdocs\elitetrack2\` to your hosting
2. Update `api/config/database.php` with your hosting database credentials
3. Modify `config.js` if needed (usually auto-detected)

### Step 2: Database Setup
1. Create a MySQL database in your hosting control panel
2. Run the initialization:
   ```
   https://yourdomain.com/api/init.php
   ```
   OR manually import the SQL schema

### Step 3: Update Frontend (if needed)
If auto-detection doesn't work, manually update the config in `config.js`

### Step 4: Test Everything
1. Test login: `https://yourdomain.com/`
2. Test API: `https://yourdomain.com/api/`
3. Test tracking links
4. Test all admin functions

## 🌐 Common Hosting Scenarios

### Scenario 1: Root Domain (yourdomain.com)
- Upload files to `public_html/` or `htdocs/`
- No changes needed to config.js
- URLs will be: `yourdomain.com/`, `yourdomain.com/api/`

### Scenario 2: Subdirectory (yourdomain.com/elitetrack)
- Upload files to `public_html/elitetrack/`
- Update config.js production settings:
  ```javascript
  production: {
      API_BASE_URL: '/elitetrack/api',
      APP_BASE_URL: '/elitetrack'
  }
  ```

### Scenario 3: Subdomain (app.yourdomain.com)
- Upload files to subdomain root
- No changes needed to config.js
- URLs will be: `app.yourdomain.com/`, `app.yourdomain.com/api/`

## 🔒 Security Considerations for Production

### 1. Change Default Admin Password
```
https://yourdomain.com/change-password.html
```

### 2. Update Database Credentials
- Use strong, unique database passwords
- Consider using environment variables for sensitive data

### 3. Enable HTTPS
- Install SSL certificate
- Update any hardcoded HTTP links to HTTPS

### 4. File Permissions
- Set proper file permissions (644 for files, 755 for directories)
- Ensure `api/data/` directory is writable (if using file storage)

### 5. Hide Sensitive Files
- Remove or protect init.php after deployment
- Ensure .htaccess is working to protect sensitive directories

## 🔧 Quick Migration Script
Create this script to help with deployment:

```bash
#!/bin/bash
# deploy.sh - Quick deployment script

# 1. Upload files
# rsync -avz --exclude='.git' ./ user@yourserver:/path/to/public_html/

# 2. Set permissions
# find /path/to/public_html -type f -exec chmod 644 {} \;
# find /path/to/public_html -type d -exec chmod 755 {} \;

# 3. Initialize database (run once)
# curl https://yourdomain.com/api/init.php

echo "Deployment complete!"
echo "Don't forget to:"
echo "1. Update database credentials in api/config/database.php"
echo "2. Test the application"
echo "3. Change default admin password"
```

## ✅ Post-Deployment Checklist

- [ ] Database connection working
- [ ] API endpoints responding
- [ ] Login/logout working
- [ ] User management working
- [ ] Tracking links working
- [ ] All navigation links working
- [ ] Default admin password changed
- [ ] SSL certificate active (HTTPS)
- [ ] Remove/secure init.php file
- [ ] Test email notifications (if implemented)

## 🆘 Troubleshooting Common Issues

### Issue: API returns 404 errors
**Solution:** Check .htaccess file in api/ directory

### Issue: Database connection failed
**Solution:** Verify database credentials in `api/config/database.php`

### Issue: Tracking links broken
**Solution:** Update config.js with correct base URLs

### Issue: CORS errors
**Solution:** Update CORS settings in `api/utils/CorsHandler.php`

### Issue: File permissions
**Solution:** Ensure web server can read files (644) and execute directories (755)

---

## 📞 Need Help?
- Check server error logs
- Test API endpoints directly
- Verify database connectivity
- Check browser developer console for JavaScript errors