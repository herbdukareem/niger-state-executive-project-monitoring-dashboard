# 🚀 GitHub Actions Deployment Setup for SmartWeb Hosting

## 📁 Files Created

I've created a complete GitHub Actions deployment system for your Niger State Project Monitoring Dashboard:

### 🔧 GitHub Actions Workflows
- `.github/workflows/deploy.yml` - Complete deployment with testing
- `.github/workflows/deploy-cpanel.yml` - Optimized for cPanel hosting

### 📜 Deployment Scripts
- `deploy/post-deployment.sh` - Server-side post-deployment script
- `deploy/test-deployment.sh` - Local deployment testing script

### 📖 Documentation
- `DEPLOYMENT.md` - Comprehensive deployment guide
- `GITHUB_ACTIONS_SETUP.md` - This setup guide

## 🔑 Required GitHub Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions, and add these secrets:

### Application Configuration
```
APP_KEY=base64:your-generated-app-key-here
APP_URL=https://yourdomain.com
```

### Database Configuration
```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### SmartWeb cPanel FTP Configuration
```
CPANEL_FTP_HOST=ftp.yourdomain.com
CPANEL_FTP_USERNAME=your_cpanel_username
CPANEL_FTP_PASSWORD=your_cpanel_password
CPANEL_SERVER_DIR=/public_html/
```

### SSH Configuration (Optional - if available)
```
SSH_ENABLED=true
CPANEL_SSH_HOST=yourdomain.com
CPANEL_SSH_USERNAME=your_cpanel_username
CPANEL_SSH_PASSWORD=your_cpanel_password
CPANEL_SSH_PORT=22
CPANEL_DEPLOYMENT_PATH=/home/username/public_html
```

### Mail Configuration (Optional)
```
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

## 🎯 How to Generate APP_KEY

Run this command locally to generate your application key:
```bash
php artisan key:generate --show
```

Copy the output (including `base64:`) and add it to your GitHub secrets.

## 🔄 Deployment Process

When you push to the `main` branch, GitHub Actions will automatically:

1. **🧪 Run Tests** - Execute PHPUnit tests
2. **📦 Install Dependencies** - Composer and NPM packages
3. **🏗️ Build Assets** - Compile Vue.js and CSS
4. **📤 Deploy Files** - Upload to SmartWeb via FTP
5. **🗄️ Run Migrations** - Update database schema
6. **⚡ Cache Configs** - Optimize for production
7. **🔒 Set Permissions** - Configure file permissions

## 🛠️ SmartWeb cPanel Setup

### 1. Database Setup
1. Login to cPanel
2. Go to "MySQL Databases"
3. Create a new database
4. Create a database user
5. Assign user to database with all privileges

### 2. Domain Configuration
- Ensure your domain points to the `public` folder
- You may need to move files from `public_html/public/` to `public_html/`

### 3. PHP Configuration
Verify your hosting supports:
- PHP 8.2+
- Required extensions: mbstring, dom, fileinfo, mysql, gd, zip, bcmath
- Memory limit: 256MB+

## 🧪 Testing Before Deployment

Run the local test script to verify everything works:

### On Linux/Mac:
```bash
chmod +x deploy/test-deployment.sh
./deploy/test-deployment.sh
```

### On Windows:
```bash
bash deploy/test-deployment.sh
```

## 📋 Pre-Deployment Checklist

Before pushing to main branch:

- [ ] All GitHub secrets are configured
- [ ] Database is created in cPanel
- [ ] Domain is configured properly
- [ ] Local tests pass
- [ ] .env.example is updated with required variables
- [ ] All features are tested locally

## 🚀 First Deployment Steps

1. **Configure GitHub Secrets** (see list above)
2. **Test Locally** using the test script
3. **Push to Main Branch**:
   ```bash
   git add .
   git commit -m "feat: setup GitHub Actions deployment"
   git push origin main
   ```
4. **Monitor Deployment** in GitHub Actions tab
5. **Verify Application** is working on your domain

## 🔍 Monitoring Deployment

### GitHub Actions
- Go to your repository → Actions tab
- Click on the latest workflow run
- Monitor each step for success/failure
- Check logs for any errors

### Application Health Check
After deployment, verify:
- [ ] Website loads without errors
- [ ] Database connection works
- [ ] User login/registration works
- [ ] Dashboard displays data correctly
- [ ] File uploads work (if applicable)

## 🛠️ Troubleshooting

### Common Issues:

#### 1. FTP Connection Failed
- Verify FTP credentials in GitHub secrets
- Check FTP host format (usually `ftp.yourdomain.com`)
- Ensure FTP user has write permissions

#### 2. Database Connection Error
- Verify database credentials in GitHub secrets
- Ensure database exists in cPanel
- Check database user privileges

#### 3. 500 Internal Server Error
- Check `storage/logs/laravel.log` on server
- Verify file permissions are set correctly
- Ensure .env file has correct values

#### 4. Assets Not Loading
- Verify build process completed successfully
- Check if `public/build/` directory exists
- Ensure .htaccess file is present in public folder

### Manual Fix Commands:
If you have SSH access, run these commands on the server:
```bash
cd /path/to/your/application

# Set permissions
chmod -R 777 storage bootstrap/cache

# Clear caches
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate --force

# Cache for production
php artisan config:cache
php artisan route:cache
```

## 📞 Support Resources

- **GitHub Actions Logs** - Check the Actions tab for detailed logs
- **Laravel Logs** - Check `storage/logs/laravel.log` on server
- **SmartWeb Support** - Contact for hosting-specific issues
- **Laravel Documentation** - For framework-related questions

## 🎉 Success!

Once everything is set up, you'll have:
- ✅ Automatic deployment on every push to main
- ✅ Automated testing before deployment
- ✅ Production-optimized builds
- ✅ Database migrations
- ✅ Proper file permissions
- ✅ Cached configurations

Your Niger State Project Monitoring Dashboard will automatically deploy to SmartWeb hosting whenever you push changes to the main branch!

---

**Happy Deploying! 🚀**
