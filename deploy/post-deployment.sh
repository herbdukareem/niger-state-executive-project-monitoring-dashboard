#!/bin/bash

# Post-deployment script for SmartWeb cPanel hosting
# This script should be run after files are uploaded to the server

echo "🚀 Starting post-deployment setup..."

# Get the deployment directory (usually public_html or a subdirectory)
DEPLOYMENT_DIR=${1:-"/home/username/public_html"}

cd "$DEPLOYMENT_DIR"

echo "📁 Current directory: $(pwd)"

# Set proper file permissions
echo "🔒 Setting file permissions..."
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Set writable permissions for Laravel directories
echo "📝 Setting writable permissions for Laravel directories..."
chmod -R 777 storage
chmod -R 777 bootstrap/cache

# Check if .env file exists
if [ ! -f .env ]; then
    echo "⚠️  .env file not found. Creating from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✅ .env file created from .env.example"
    else
        echo "❌ .env.example not found. Please create .env manually."
        exit 1
    fi
fi

# Generate application key if not set
echo "🔑 Checking application key..."
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
    echo "✅ Application key generated"
else
    echo "✅ Application key already exists"
fi

# Clear all caches
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed essential data (roles and permissions)
echo "🌱 Seeding essential data..."
php artisan db:seed --class=RolePermissionSeeder --force

# Cache configurations for production
echo "⚡ Caching configurations for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create symbolic link for storage (if not exists)
echo "🔗 Creating storage symbolic link..."
if [ ! -L public/storage ]; then
    php artisan storage:link
    echo "✅ Storage symbolic link created"
else
    echo "✅ Storage symbolic link already exists"
fi

# Set up log rotation (optional)
echo "📋 Setting up log rotation..."
if [ ! -f storage/logs/laravel.log ]; then
    touch storage/logs/laravel.log
    chmod 666 storage/logs/laravel.log
fi

# Create .htaccess for security (if not exists)
echo "🛡️  Setting up security configurations..."
if [ ! -f .htaccess ]; then
    cat > .htaccess << 'EOF'
# Redirect to public folder
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
</IfModule>

# Hide sensitive files
<Files .env>
    Order allow,deny
    Deny from all
</Files>

<Files composer.json>
    Order allow,deny
    Deny from all
</Files>

<Files composer.lock>
    Order allow,deny
    Deny from all
</Files>

<Files package.json>
    Order allow,deny
    Deny from all
</Files>
EOF
    echo "✅ Root .htaccess created"
fi

# Ensure public/.htaccess exists
if [ ! -f public/.htaccess ]; then
    cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Disable server signature
ServerSignature Off

# Prevent access to sensitive files
<Files .env>
    Order allow,deny
    Deny from all
</Files>
EOF
    echo "✅ Public .htaccess created"
fi

# Check PHP version
echo "🐘 PHP Version: $(php -v | head -n 1)"

# Check Laravel installation
echo "🎯 Laravel Version: $(php artisan --version)"

# Final status check
echo "🔍 Running final status check..."
if php artisan route:list > /dev/null 2>&1; then
    echo "✅ Laravel application is properly configured"
else
    echo "⚠️  Laravel application may have configuration issues"
fi

echo ""
echo "🎉 Post-deployment setup completed!"
echo "🌐 Your application should now be accessible via your domain"
echo ""
echo "📋 Next steps:"
echo "1. Update your .env file with production database credentials"
echo "2. Configure your domain to point to the public folder"
echo "3. Test the application functionality"
echo "4. Set up SSL certificate if not already configured"
echo ""
echo "🔧 Troubleshooting:"
echo "- Check storage/logs/laravel.log for any errors"
echo "- Ensure database credentials are correct in .env"
echo "- Verify file permissions are set correctly"
echo ""
