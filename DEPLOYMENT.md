# 🚀 SmartWeb Hosting Deployment Guide

This guide will help you set up automatic deployment from GitHub to your SmartWeb hosting server using GitHub Actions.

## 📋 Prerequisites

Before setting up the deployment, ensure you have:

1. **SmartWeb Hosting Account** with cPanel access
2. **GitHub Repository** with your Laravel project
3. **FTP/SSH Access** to your hosting server
4. **Database** created in cPanel
5. **Domain** configured to point to your hosting

## 🔧 Setup Instructions

### Step 1: Configure GitHub Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions, and add these secrets:

#### 🌐 Application Configuration
```
APP_KEY=base64:your-generated-app-key-here
APP_URL=https://yourdomain.com
```

#### 🗄️ Database Configuration
```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

#### 📧 Mail Configuration (Optional)
```
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

#### 🔐 FTP/cPanel Configuration
```
CPANEL_FTP_HOST=ftp.yourdomain.com
CPANEL_FTP_USERNAME=your_cpanel_username
CPANEL_FTP_PASSWORD=your_cpanel_password
CPANEL_SERVER_DIR=/public_html/
```

#### 🖥️ SSH Configuration (Optional - if SSH is available)
```
SSH_ENABLED=true
CPANEL_SSH_HOST=yourdomain.com
CPANEL_SSH_USERNAME=your_cpanel_username
CPANEL_SSH_PASSWORD=your_cpanel_password
CPANEL_SSH_PORT=22
CPANEL_DEPLOYMENT_PATH=/home/username/public_html
```

### Step 2: Generate Laravel Application Key

Run this command locally to generate an application key:
```bash
php artisan key:generate --show
```

Copy the generated key (including `base64:`) and add it to your GitHub secrets as `APP_KEY`.

### Step 3: Database Setup in cPanel

1. **Login to cPanel**
2. **Go to MySQL Databases**
3. **Create a new database**
4. **Create a database user**
5. **Assign the user to the database with all privileges**
6. **Note down the database credentials**

### Step 4: Configure Domain

#### Option A: Main Domain
If deploying to your main domain, ensure your domain points to the `public` folder:
- In cPanel File Manager, you may need to move files from `public_html/public/` to `public_html/`

#### Option B: Subdomain
If using a subdomain:
1. Create subdomain in cPanel
2. Point it to the `public` folder of your application

### Step 5: File Structure on Server

Your files should be organized like this on the server:
```
/home/username/public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          # This should be your document root
│   ├── index.php
│   ├── .htaccess
│   └── build/       # Compiled assets
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── artisan
```

## 🔄 Deployment Workflow

The GitHub Action will automatically:

1. **Run Tests** - Execute PHPUnit tests to ensure code quality
2. **Build Assets** - Compile Vue.js components and CSS
3. **Install Dependencies** - Install production Composer packages
4. **Deploy Files** - Upload files to your server via FTP
5. **Run Migrations** - Execute database migrations
6. **Cache Configurations** - Optimize Laravel for production
7. **Set Permissions** - Configure proper file permissions

## 📁 Files Excluded from Deployment

The following files/folders are automatically excluded:
- `node_modules/`
- `.git/`
- `tests/`
- `.github/`
- `.env.example`
- `package.json`
- `composer.lock`
- Development configuration files

## 🛠️ Manual Deployment Steps (If Needed)

If you need to deploy manually or troubleshoot:

### 1. Upload Files via FTP
```bash
# Connect to FTP and upload all files except excluded ones
# Make sure to upload to the correct directory
```

### 2. Run Post-Deployment Script
```bash
# SSH into your server and run:
cd /path/to/your/application
chmod +x deploy/post-deployment.sh
./deploy/post-deployment.sh
```

### 3. Set Environment Variables
Create/update `.env` file with production values:
```env
APP_NAME="Niger State Project Monitoring"
APP_ENV=production
APP_KEY=base64:your-app-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 🔍 Troubleshooting

### Common Issues and Solutions

#### 1. **500 Internal Server Error**
- Check `storage/logs/laravel.log` for detailed errors
- Ensure `.env` file has correct database credentials
- Verify file permissions: `chmod -R 777 storage bootstrap/cache`

#### 2. **Database Connection Error**
- Verify database credentials in `.env`
- Ensure database exists and user has proper privileges
- Check if database server is accessible

#### 3. **Assets Not Loading**
- Run `npm run build` to compile assets
- Check if `public/build/` directory exists
- Verify `.htaccess` file in public directory

#### 4. **Permission Denied Errors**
```bash
# Set proper permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

#### 5. **FTP Upload Issues**
- Verify FTP credentials in GitHub secrets
- Check FTP server path (usually `/public_html/`)
- Ensure FTP user has write permissions

### 🔧 Manual Commands for Troubleshooting

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Generate application key
php artisan key:generate

# Create storage link
php artisan storage:link

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📊 Monitoring Deployment

### GitHub Actions Status
- Check the Actions tab in your GitHub repository
- Monitor deployment logs for any errors
- Verify successful completion of all steps

### Application Health Check
After deployment, verify:
- [ ] Website loads without errors
- [ ] Database connection works
- [ ] User authentication functions
- [ ] File uploads work (if applicable)
- [ ] All routes are accessible

## 🔒 Security Considerations

1. **Environment Variables** - Never commit `.env` to repository
2. **File Permissions** - Ensure proper permissions are set
3. **SSL Certificate** - Configure HTTPS for production
4. **Database Security** - Use strong database passwords
5. **Regular Updates** - Keep Laravel and dependencies updated

## 🎯 SmartWeb Specific Configuration

### cPanel File Manager Setup
1. **Login to cPanel**
2. **Open File Manager**
3. **Navigate to public_html**
4. **Set Document Root** to point to the `public` folder

### PHP Configuration
Ensure your hosting supports:
- PHP 8.2 or higher
- Required extensions: mbstring, dom, fileinfo, mysql, gd, zip, bcmath
- Memory limit: 256MB or higher

### Database Configuration
- Use MySQL 8.0 or MariaDB 10.3+
- Ensure proper character set (utf8mb4)
- Configure timezone settings

## 📞 Support

If you encounter issues:
1. Check the deployment logs in GitHub Actions
2. Review Laravel logs in `storage/logs/laravel.log`
3. Contact SmartWeb support for hosting-specific issues
4. Refer to Laravel documentation for framework issues

---

**Happy Deploying! 🚀**
