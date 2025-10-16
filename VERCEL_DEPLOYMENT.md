# Elite Track - Vercel + PHP Server Split Deployment

## 🏗️ Architecture: Frontend (Vercel) + Backend (PHP Server)

This setup separates your static frontend from the PHP backend for optimal performance and scalability.

```
┌─────────────────┐    HTTPS API calls    ┌──────────────────┐
│   Vercel        │  ──────────────────►  │  PHP Server      │
│   (Frontend)    │                       │  (Backend API)   │
│                 │                       │                  │
│ • HTML/CSS/JS   │                       │ • PHP API        │
│ • Static files  │                       │ • MySQL Database │
│ • Fast CDN      │                       │ • File storage   │
└─────────────────┘                       └──────────────────┘
```

## 📁 File Structure Split

### Frontend Files (Deploy to Vercel):
```
frontend/
├── index.html
├── dashboard.html
├── add-user.html
├── change-password.html
├── track.html
├── config.js
└── vercel.json (optional)
```

### Backend Files (Deploy to PHP Server):
```
backend/
└── api/
    ├── .htaccess
    ├── index.php
    ├── init.php
    ├── config/
    ├── controllers/
    ├── middleware/
    ├── models/
    └── utils/
```

## 🚀 Step-by-Step Deployment

### Step 1: Deploy Backend (PHP Server)

1. **Choose PHP hosting**:
   - **Shared Hosting**: Bluehost, HostGator, SiteGround (~$3-10/month)
   - **VPS**: DigitalOcean, Linode, Vultr (~$5-20/month)
   - **Platform**: Railway, Heroku PHP

2. **Upload API files only**:
   - Upload the entire `api/` folder to your server
   - Your backend URL will be: `https://your-server.com/api/`

3. **Update database config** in `api/config/database.php`:
   ```php
   private $host = 'localhost';
   private $db_name = 'your_hosting_database_name';
   private $username = 'your_db_username';
   private $password = 'your_db_password';
   ```

4. **Initialize database**:
   ```
   https://your-server.com/api/init.php
   ```

5. **Test API**:
   ```
   https://your-server.com/api/
   ```

### Step 2: Deploy Frontend (Vercel)

1. **Update config.js** with your backend URL:
   ```javascript
   production: {
       API_BASE_URL: 'https://your-server.com/api', // 🔥 Your PHP server URL
       APP_BASE_URL: '',
       ENVIRONMENT: 'production'
   }
   ```

2. **Install Vercel CLI**:
   ```bash
   npm i -g vercel
   ```

3. **Deploy to Vercel**:
   ```bash
   # Login to Vercel
   vercel login
   
   # Deploy from your frontend folder
   vercel --prod
   ```

4. **Your frontend will be live at**:
   ```
   https://your-app.vercel.app
   ```

### Step 3: Configure CORS

Update `api/utils/CorsHandler.php` on your PHP server:

```php
$allowedOrigins = [
    // Local development
    'http://localhost',
    
    // Your Vercel URLs (🔥 UPDATE THESE)
    'https://your-app.vercel.app',
    'https://your-custom-domain.com',
];
```

## 🛠️ Complete Example Setup

### Example: Backend on Bluehost, Frontend on Vercel

1. **Backend**: `https://mysite.com/api/`
2. **Frontend**: `https://elite-track.vercel.app`
3. **Config**:
   ```javascript
   // config.js
   production: {
       API_BASE_URL: 'https://mysite.com/api',
       APP_BASE_URL: '',
       ENVIRONMENT: 'production'
   }
   ```

## 📋 Deployment Checklist

### ✅ Backend (PHP Server):
- [ ] Upload `api/` folder to server
- [ ] Create MySQL database via hosting panel
- [ ] Update database credentials in `database.php`
- [ ] Add Vercel domain to CORS origins
- [ ] Visit `/api/init.php` to initialize
- [ ] Test API at `/api/` endpoint
- [ ] Enable SSL certificate

### ✅ Frontend (Vercel):
- [ ] Update `config.js` with backend API URL
- [ ] Remove all PHP files (keep only HTML/CSS/JS)
- [ ] Connect GitHub/GitLab repo to Vercel
- [ ] Deploy to Vercel
- [ ] Test login and all functionality
- [ ] Setup custom domain (optional)

### ✅ Integration Testing:
- [ ] Login works from Vercel frontend
- [ ] Dashboard loads users from PHP backend
- [ ] User creation/editing works
- [ ] Tracking links work
- [ ] No CORS errors in browser console

## 🚨 Troubleshooting

### CORS Error:
```
Access to fetch at 'https://api.com' from origin 'https://app.vercel.app' blocked by CORS
```
**Fix**: Add your Vercel URL to `$allowedOrigins` in `CorsHandler.php`

### 404 API Error:
**Fix**: Ensure `.htaccess` file is in the `api/` directory on your server

### Database Connection Error:
**Fix**: Double-check database credentials in `config/database.php`

## 💰 Cost Breakdown

### Budget Option (~$5/month):
- Frontend: Vercel (Free)
- Backend: Shared hosting ($3-5/month)

### Professional Setup (~$30/month):
- Frontend: Vercel Pro ($20/month)
- Backend: VPS ($10-15/month)

## 🎯 Benefits of This Architecture

1. **Performance**: Static frontend served from CDN
2. **Scalability**: Backend and frontend scale independently  
3. **Development**: Easy to develop and deploy separately
4. **Cost-effective**: Use the best hosting for each part
5. **Modern**: Follows current web development best practices

## 🚀 You're Ready!

1. Deploy your PHP backend to any hosting service
2. Deploy your frontend to Vercel  
3. Update the config with your backend URL
4. Test everything works together

Your modern split-architecture Elite Track system is ready! 🎉