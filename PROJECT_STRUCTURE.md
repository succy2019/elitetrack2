# Elite Track - Complete Project Structure

## Project Overview
- **Purpose**: Student tracking system with admin panel
- **Backend**: PHP 7.4+ with file-based JSON storage
- **Frontend**: HTML5/JavaScript/CSS
- **Authentication**: JWT tokens
- **Server**: Apache (XAMPP)

## Directory Structure
```
elitetrack2/
├── api/                              # PHP Backend API
│   ├── config/
│   │   ├── database.php             # SQLite PDO connection
│   │   └── simple_database.php      # File-based JSON storage
│   ├── controllers/
│   │   ├── AuthController.php       # Authentication endpoints
│   │   ├── SimpleAuthController.php # File-based auth
│   │   ├── UserController.php       # User CRUD operations
│   │   └── SimpleUserController.php # File-based user ops
│   ├── middleware/
│   │   └── AuthMiddleware.php       # JWT token validation
│   ├── models/
│   │   ├── Admin.php               # Admin model (SQLite)
│   │   ├── SimpleAdmin.php         # Admin model (JSON)
│   │   ├── User.php                # User model (SQLite)
│   │   └── SimpleUser.php          # User model (JSON)
│   ├── utils/
│   │   └── JWTUtil.php             # JWT token handling
│   ├── data/                       # JSON storage directory
│   │   ├── admins.json            # Admin data
│   │   └── users.json             # User data
│   ├── simple.php                 # Main API router
│   ├── init_simple.php            # Database initialization
│   └── test_api.php               # API testing script
├── index.html                     # Login page
├── dashboard.html                 # Admin dashboard
├── add-user.html                  # Add new user form
├── change-password.html           # Password change form
├── track.html                     # User tracking page
├── import.sql                     # Original database schema
├── README.md                      # Project documentation
├── XAMPP_SETUP.md                # XAMPP setup guide
└── migrate_to_xampp.bat          # Migration script
```

## API Endpoints

### Authentication
- `POST /api/simple.php/auth/login` - Admin login
- `POST /api/simple.php/auth/change-password` - Change admin password

### User Management
- `GET /api/simple.php/users` - List all users
- `POST /api/simple.php/users` - Create new user
- `GET /api/simple.php/users/{id}` - Get user by ID
- `PUT /api/simple.php/users/{id}` - Update user
- `DELETE /api/simple.php/users/{id}` - Delete user
- `GET /api/simple.php/users/track/{track_id}` - Get user by track ID
- `PUT /api/simple.php/users/track/{track_id}` - Update user progress

## Installation Steps

### Quick Setup (Using Migration Script)
1. **Run migration script**:
   ```batch
   migrate_to_xampp.bat
   ```

### Manual Setup
1. **Copy project to XAMPP**:
   ```
   Copy entire elitetrack2 folder to C:\xampp\htdocs\
   ```

2. **Start XAMPP services**:
   - Open XAMPP Control Panel
   - Start Apache

3. **Initialize database**:
   ```
   http://localhost/elitetrack2/api/init_simple.php
   ```

4. **Access application**:
   ```
   http://localhost/elitetrack2/
   ```

## Usage

### Admin Login
- **Email**: admin@elitetrack.com
- **Password**: admin123

### Frontend Pages
- **/** - Login page
- **/dashboard.html** - Admin dashboard
- **/add-user.html** - Add new user
- **/change-password.html** - Change admin password
- **/track.html?id={trackId}** - User tracking page

### User Tracking
- Each user gets a unique tracking ID
- Share tracking URL: `http://localhost/elitetrack2/track.html?id={trackId}`
- Users can update their own progress

## Features

### Admin Features
- Secure JWT-based authentication
- User management (CRUD operations)
- Progress tracking overview
- Password management

### User Features
- Self-service progress updates
- Unique tracking links
- No login required for tracking

### Technical Features
- File-based JSON storage (no database required)
- CORS enabled for API access
- Responsive design
- Error handling and validation

## Data Storage
All data is stored in JSON files in the `api/data/` directory:
- `admins.json` - Admin accounts
- `users.json` - User records and progress

## Security
- JWT tokens for admin authentication
- Password hashing (bcrypt equivalent)
- Input validation and sanitization
- CORS configuration

## Browser Compatibility
- Modern browsers (Chrome, Firefox, Safari, Edge)
- ES6+ JavaScript features
- Fetch API for HTTP requests

## Troubleshooting

### Common Issues
1. **404 errors**: Ensure Apache is running and files are in correct directory
2. **CORS errors**: API handles CORS automatically
3. **Permission errors**: Check file permissions in htdocs directory
4. **Data not saving**: Verify `api/data/` directory is writable

### Testing
- Use `api/test_api.php` for backend testing
- Check browser console for JavaScript errors
- Verify API responses in Network tab

## Development
For development/testing without XAMPP:
```bash
php -S localhost:8000
```
Then access: http://localhost:8000/

## Version History
- v1.0 - Initial PHP API implementation
- v1.1 - File-based storage fallback
- v1.2 - Frontend integration
- v1.3 - XAMPP deployment ready