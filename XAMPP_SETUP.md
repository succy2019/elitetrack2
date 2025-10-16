# Elite Track - XAMPP Setup Guide

## 🚀 Setting up Elite Track with XAMPP

Moving your project to XAMPP will provide a complete web server environment that's perfect for this PHP API backend.

### 📋 Prerequisites

1. **Download and Install XAMPP**:
   - Download from: https://www.apachefriends.org/
   - Install with Apache and PHP enabled
   - MySQL is optional (we're using file-based storage)

### 📁 Project Setup in XAMPP

1. **Copy Project to htdocs**:
   ```
   Copy entire elitetrack2 folder to:
   C:\xampp\htdocs\elitetrack2\
   ```

2. **Final Directory Structure**:
   ```
   C:\xampp\htdocs\elitetrack2\
   ├── index.html                    # Login page
   ├── dashboard.html               # Admin dashboard
   ├── add-user.html               # Add new user
   ├── change-password.html        # Change admin password
   ├── track.html                  # User tracking page
   └── api/
       ├── simple.php              # Main API router
       ├── init_simple.php         # Database initialization
       ├── test_simple.php         # API testing
       ├── config/
       │   └── simple_database.php # File-based database
       ├── models/
       │   ├── SimpleUser.php      # User model
       │   └── SimpleAdmin.php     # Admin model
       ├── controllers/
       │   ├── SimpleAuthController.php
       │   └── SimpleUserController.php
       ├── middleware/
       │   └── AuthMiddleware.php  # JWT middleware
       ├── utils/
       │   ├── JWT.php            # JWT utility
       │   └── CorsHandler.php    # CORS handling
       └── data/                  # JSON storage (auto-created)
           ├── users.json
           └── admins.json
   ```

### 🔧 XAMPP Configuration

1. **Start XAMPP Services**:
   - Open XAMPP Control Panel
   - Start **Apache** (required)
   - MySQL is not needed for this project

2. **Verify PHP is Working**:
   - Open browser: `http://localhost/`
   - Should see XAMPP dashboard

### 🚀 Initialize Your Project

1. **Initialize the Database**:
   ```
   http://localhost/elitetrack2/api/init_simple.php
   ```

2. **Test the API**:
   ```
   http://localhost/elitetrack2/api/test_simple.php
   ```

3. **View API Documentation**:
   ```
   http://localhost/elitetrack2/api/simple.php
   ```

### 🌐 Access Your Application

- **Frontend (Login)**: `http://localhost/elitetrack2/`
- **Dashboard**: `http://localhost/elitetrack2/dashboard.html`
- **API**: `http://localhost/elitetrack2/api/simple.php`
- **Tracking**: `http://localhost/elitetrack2/track.html/{TRACK_ID}`

### 🔑 Default Login Credentials

- **Email**: `admin@elitetrack.com`
- **Password**: `admin123`

### 🧪 Testing the Complete Setup

1. **Test API Directly**:
   - Visit: `http://localhost/elitetrack2/api/simple.php`
   - Should show API documentation

2. **Test Frontend Login**:
   - Visit: `http://localhost/elitetrack2/`
   - Login with default credentials
   - Should redirect to dashboard

3. **Test User Management**:
   - Create a new user
   - View users in dashboard
   - Test tracking with generated Track ID

### 🔧 Troubleshooting

**If Apache won't start**:
- Check if port 80 is free
- Try changing Apache port in XAMPP config

**If API returns 404**:
- Ensure mod_rewrite is enabled in Apache
- Check .htaccess file is present in api folder

**If CORS errors occur**:
- Check browser console
- Verify CorsHandler.php allows your origin

### ⚡ Performance Tips

1. **Enable PHP OPcache** (in php.ini):
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   ```

2. **Adjust PHP Settings** (if needed):
   ```ini
   max_execution_time=300
   memory_limit=256M
   post_max_size=50M
   upload_max_filesize=50M
   ```

### 🔒 Security Notes

For development in XAMPP:
- CORS is set to allow localhost origins
- JWT secret should be changed for production
- Default admin password should be changed

### 🎯 Benefits of Using XAMPP

1. **Complete Environment**: Apache + PHP + proper URL handling
2. **Easy Management**: Start/stop services with GUI
3. **Real Server Environment**: Tests work as they would in production
4. **No Configuration**: Works out of the box
5. **Cross-Platform**: Same setup works on Windows/Mac/Linux

---

## 🚀 Quick Start Commands

After copying to XAMPP:

1. Start XAMPP Apache
2. Visit: `http://localhost/elitetrack2/api/init_simple.php`
3. Visit: `http://localhost/elitetrack2/`
4. Login and enjoy your working application!

Your Elite Track system will be fully functional with XAMPP! 🎉