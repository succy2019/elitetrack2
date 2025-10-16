@echo off
echo Elite Track - XAMPP Migration Script
echo =====================================
echo.

REM Check if XAMPP is installed
if not exist "C:\xampp\htdocs" (
    echo ERROR: XAMPP not found at C:\xampp\
    echo Please install XAMPP first from: https://www.apachefriends.org/
    echo.
    pause
    exit /b 1
)

echo ✅ XAMPP found at C:\xampp\

REM Create project directory in htdocs
if not exist "C:\xampp\htdocs\elitetrack2" (
    mkdir "C:\xampp\htdocs\elitetrack2"
    echo ✅ Created project directory
) else (
    echo ⚠️  Project directory already exists
)

REM Copy all files
echo 📁 Copying project files...

REM Copy HTML files
copy "index.html" "C:\xampp\htdocs\elitetrack2\" >nul
copy "dashboard.html" "C:\xampp\htdocs\elitetrack2\" >nul
copy "add-user.html" "C:\xampp\htdocs\elitetrack2\" >nul
copy "change-password.html" "C:\xampp\htdocs\elitetrack2\" >nul
copy "track.html" "C:\xampp\htdocs\elitetrack2\" >nul
copy "README.md" "C:\xampp\htdocs\elitetrack2\" >nul
copy "XAMPP_SETUP.md" "C:\xampp\htdocs\elitetrack2\" >nul

REM Copy API directory
xcopy "api" "C:\xampp\htdocs\elitetrack2\api\" /E /I /Y >nul

echo ✅ Files copied successfully

REM Create data directory
if not exist "C:\xampp\htdocs\elitetrack2\api\data" (
    mkdir "C:\xampp\htdocs\elitetrack2\api\data"
    echo ✅ Created data directory
)

echo.
echo 🎯 Migration completed!
echo.
echo Next steps:
echo 1. Start XAMPP Control Panel
echo 2. Start Apache service
echo 3. Visit: http://localhost/elitetrack2/api/init_simple.php
echo 4. Visit: http://localhost/elitetrack2/
echo.
echo Default login:
echo Email: admin@elitetrack.com
echo Password: admin123
echo.
echo 📚 For detailed setup instructions, see:
echo C:\xampp\htdocs\elitetrack2\XAMPP_SETUP.md
echo.
pause