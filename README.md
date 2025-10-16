# Elite Track API - PHP Backend

A complete PHP REST API backend for the Elite Management Tracking system, built to replace the existing Node.js/TypeScript backend while maintaining full compatibility with the existing frontend.

## 🚀 Features

- **Complete REST API** with all endpoints from the original system
- **JWT Authentication** for secure admin access
- **SQLite Database** with automatic initialization
- **CORS Support** for frontend integration
- **User Management** with full CRUD operations
- **Progress Tracking** with real-time updates
- **Public Tracking** endpoints for user tracking
- **Comprehensive Error Handling** and validation
- **Security Headers** and best practices

## 📁 Project Structure

```
api/
├── config/
│   └── database.php          # Database configuration and connection
├── controllers/
│   ├── AuthController.php    # Authentication endpoints
│   └── UserController.php    # User management endpoints
├── middleware/
│   └── AuthMiddleware.php    # JWT token verification
├── models/
│   ├── Admin.php            # Admin model with database operations
│   └── User.php             # User model with database operations
├── utils/
│   ├── JWT.php              # JWT token generation and verification
│   └── CorsHandler.php      # CORS and security headers
├── data/                    # SQLite database storage (auto-created)
├── .htaccess               # URL rewriting and security
├── index.php               # Main API router
├── init.php                # Database initialization script
└── test.php                # API testing script
```

## 🛠️ Installation & Setup

### Prerequisites

- PHP 7.4 or higher
- Apache/Nginx web server with mod_rewrite enabled
- SQLite support (usually included with PHP)

### Quick Setup

1. **Copy the API files** to your web server directory:
   ```bash
   # Copy the entire api folder to your web root or subdirectory
   cp -r api/ /your/web/root/api/
   ```

2. **Set proper permissions**:
   ```bash
   chmod 755 api/
   chmod 666 api/data/  # Will be created automatically
   ```

3. **Initialize the database**:
   ```bash
   # Via command line
   php api/init.php
   
   # Or via web browser
   http://localhost/api/init.php
   ```

4. **Test the API**:
   ```bash
   # Via command line
   php api/test.php
   
   # Or via web browser
   http://localhost/api/test.php
   ```

## 🔧 Configuration

### Database Configuration

The API uses SQLite by default. To modify database settings, edit `config/database.php`:

```php
private $db_name = 'elitetrack.db'; // Database file name
```

### JWT Secret

Change the JWT secret in `utils/JWT.php` for production:

```php
private static $secret = 'your-secure-secret-key-here';
```

### CORS Origins

Update allowed origins in `utils/CorsHandler.php`:

```php
$allowedOrigins = [
    'https://your-domain.com',
    'https://app.your-domain.com'
];
```

## 📚 API Endpoints

### Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/login` | Admin login | No |
| PUT | `/api/auth/change-password` | Change admin password | Yes |
| GET | `/api/auth/verify` | Verify JWT token | Yes |
| POST | `/api/auth/logout` | Logout (client-side) | No |
| GET | `/api/auth/profile` | Get admin profile | Yes |

### User Management Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/users/all` | Get all users | Yes |
| POST | `/api/users/new` | Create new user | Yes |
| PUT | `/api/users/update` | Update user | Yes |
| PUT | `/api/users/progress` | Update user progress | Yes |
| DELETE | `/api/users/delete` | Delete user | Yes |
| GET | `/api/users/stats` | Get user statistics | Yes |
| GET | `/api/users/search` | Search users | Yes |
| GET | `/api/users/track/{trackId}` | Get user by track ID | No |
| GET | `/api/users/{id}` | Get user by ID | Yes |

## 🔑 Authentication

### Login Request

```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@elitetrack.com",
    "password": "admin123"
  }'
```

### Response

```json
{
  "success": true,
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "admin": {
    "id": 1,
    "email": "admin@elitetrack.com"
  }
}
```

### Using JWT Token

Include the token in the `Authorization` header for protected endpoints:

```bash
curl -X GET http://localhost/api/users/all \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## 👤 User Operations

### Create User

```bash
curl -X POST http://localhost/api/users/new \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "email": "user@example.com",
    "name": "John Doe",
    "amount": "$5,000.00",
    "status": "pending",
    "phone": "+1 (555) 123-4567",
    "address": "123 Main St, City, State 12345",
    "message": "Payment processing for loan application"
  }'
```

### Track User (Public Endpoint)

```bash
curl -X GET http://localhost/api/users/track/TRK-ABC123-DEF456
```

### Update User Progress

```bash
curl -X PUT http://localhost/api/users/progress \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "id": 1,
    "progress_percentage": 75
  }'
```

## 🗄️ Database Schema

### Users Table

```sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    amount TEXT NOT NULL,
    status TEXT NOT NULL,
    phone TEXT NOT NULL,
    address TEXT NOT NULL,
    message TEXT NOT NULL,
    track_id TEXT UNIQUE NOT NULL,
    payment_to TEXT NOT NULL DEFAULT 'Merchant Commercial Bank',
    account_number TEXT NOT NULL DEFAULT '0012239988',
    estimated_processing_time TEXT NOT NULL DEFAULT '1-2 minutes',
    money_due TEXT NOT NULL,
    progress_percentage INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Admins Table

```sql
CREATE TABLE admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## 🧪 Testing

### Run All Tests

```bash
# Command line
php api/test.php

# With custom base URL
php api/test.php http://your-domain.com/api

# Web browser
http://localhost/api/test.php
```

### Manual Testing

1. **Test API Info**: `GET http://localhost/api/`
2. **Test Login**: Use the credentials above
3. **Test User Creation**: Create a user via the admin panel
4. **Test Public Tracking**: Use the generated track ID

## 🔒 Security Features

- **JWT Authentication** with expiration
- **Password Hashing** using PHP's password_hash()
- **SQL Injection Protection** via prepared statements
- **CORS Headers** for cross-origin requests
- **Security Headers** (XSS, CSRF protection)
- **Input Validation** and sanitization
- **Error Handling** without exposing sensitive data

## 🌐 Frontend Integration

The API is fully compatible with the existing HTML frontend. Update the API endpoints in your frontend JavaScript:

### In your frontend files:

```javascript
// Update API base URL
const API_BASE_URL = 'http://localhost/api';

// Login function (already compatible)
const response = await fetch(`${API_BASE_URL}/auth/login`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, password }),
});

// Get users (already compatible)
const response = await fetch(`${API_BASE_URL}/users/all`, {
    headers: {
        'Authorization': `Bearer ${token}`
    }
});
```

## 🐛 Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check PHP error logs
   - Verify file permissions
   - Ensure SQLite extension is enabled

2. **CORS Errors**
   - Update allowed origins in `CorsHandler.php`
   - Check that `.htaccess` is working

3. **Database Connection Failed**
   - Verify write permissions on `data/` directory
   - Check SQLite support: `php -m | grep sqlite`

4. **JWT Token Issues**
   - Verify token format
   - Check token expiration
   - Ensure consistent secret key

### Debug Mode

To enable detailed error reporting, add to the top of `index.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📈 Production Deployment

### Security Checklist

- [ ] Change JWT secret key
- [ ] Update CORS allowed origins
- [ ] Set proper file permissions
- [ ] Enable HTTPS
- [ ] Disable PHP error display
- [ ] Regular database backups
- [ ] Update default admin credentials

### Performance Optimization

- [ ] Enable PHP OPcache
- [ ] Use proper HTTP caching headers
- [ ] Implement rate limiting
- [ ] Monitor database size
- [ ] Use CDN for static assets

## 🤝 Default Credentials

**Admin Login:**
- Email: `admin@elitetrack.com`
- Password: `admin123`

> ⚠️ **Important**: Change these credentials in production!

## 📄 License

This project matches the functionality of the original Elite Track system and maintains compatibility with the existing frontend application.

---

**Ready to use!** The API is now fully functional and ready to replace your existing backend while maintaining complete compatibility with your HTML frontend.