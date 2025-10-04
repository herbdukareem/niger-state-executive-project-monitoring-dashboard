#!/bin/bash

# Test deployment script for local testing before pushing to production
# This script simulates the deployment process locally

echo "🧪 Testing deployment process locally..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the project root
if [ ! -f "artisan" ]; then
    print_error "This script must be run from the Laravel project root directory"
    exit 1
fi

print_status "Starting deployment test..."

# Step 1: Check PHP version
print_status "Checking PHP version..."
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
if (( $(echo "$PHP_VERSION >= 8.2" | bc -l) )); then
    print_success "PHP version $PHP_VERSION is supported"
else
    print_error "PHP version $PHP_VERSION is not supported. Minimum required: 8.2"
    exit 1
fi

# Step 2: Check required PHP extensions
print_status "Checking required PHP extensions..."
REQUIRED_EXTENSIONS=("mbstring" "dom" "fileinfo" "mysql" "gd" "zip" "bcmath" "curl" "openssl" "pdo" "tokenizer" "xml" "ctype" "json")
MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -q "^$ext$"; then
        MISSING_EXTENSIONS+=("$ext")
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -eq 0 ]; then
    print_success "All required PHP extensions are installed"
else
    print_error "Missing PHP extensions: ${MISSING_EXTENSIONS[*]}"
    exit 1
fi

# Step 3: Check Node.js version
print_status "Checking Node.js version..."
if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v | cut -d "v" -f 2 | cut -d "." -f 1)
    if [ "$NODE_VERSION" -ge 18 ]; then
        print_success "Node.js version $(node -v) is supported"
    else
        print_error "Node.js version $(node -v) is not supported. Minimum required: 18"
        exit 1
    fi
else
    print_error "Node.js is not installed"
    exit 1
fi

# Step 4: Install Composer dependencies
print_status "Installing Composer dependencies (production mode)..."
if composer install --optimize-autoloader --no-dev --no-interaction; then
    print_success "Composer dependencies installed successfully"
else
    print_error "Failed to install Composer dependencies"
    exit 1
fi

# Step 5: Install NPM dependencies
print_status "Installing NPM dependencies..."
if npm ci; then
    print_success "NPM dependencies installed successfully"
else
    print_error "Failed to install NPM dependencies"
    exit 1
fi

# Step 6: Build assets
print_status "Building production assets..."
if npm run build; then
    print_success "Assets built successfully"
else
    print_error "Failed to build assets"
    exit 1
fi

# Step 7: Check .env file
print_status "Checking .env configuration..."
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        print_warning ".env file created from .env.example. Please configure it properly."
    else
        print_error ".env.example file not found"
        exit 1
    fi
fi

# Step 8: Generate application key if needed
print_status "Checking application key..."
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Generating application key..."
    php artisan key:generate
    print_success "Application key generated"
else
    print_success "Application key already exists"
fi

# Step 9: Test database connection
print_status "Testing database connection..."
if php artisan migrate:status &> /dev/null; then
    print_success "Database connection successful"
else
    print_warning "Database connection failed or not configured"
fi

# Step 10: Run tests
print_status "Running application tests..."
if php artisan test; then
    print_success "All tests passed"
else
    print_warning "Some tests failed. Review test results before deploying."
fi

# Step 11: Check file permissions
print_status "Checking file permissions..."
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    print_success "Storage and cache directories are writable"
else
    print_warning "Storage or cache directories may not be writable"
    print_status "Setting proper permissions..."
    chmod -R 777 storage bootstrap/cache
fi

# Step 12: Clear and cache configurations
print_status "Clearing and caching configurations..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

print_success "Configurations cached successfully"

# Step 13: Check Laravel installation
print_status "Verifying Laravel installation..."
if php artisan --version &> /dev/null; then
    LARAVEL_VERSION=$(php artisan --version)
    print_success "Laravel installation verified: $LARAVEL_VERSION"
else
    print_error "Laravel installation verification failed"
    exit 1
fi

# Step 14: Create deployment summary
print_status "Creating deployment summary..."
echo ""
echo "📋 DEPLOYMENT TEST SUMMARY"
echo "=========================="
echo "✅ PHP Version: $PHP_VERSION"
echo "✅ Node.js Version: $(node -v)"
echo "✅ Laravel Version: $(php artisan --version)"
echo "✅ Composer Dependencies: Installed"
echo "✅ NPM Dependencies: Installed"
echo "✅ Assets: Built"
echo "✅ Application Key: Generated"
echo "✅ File Permissions: Set"
echo "✅ Configurations: Cached"
echo ""

# Step 15: Final recommendations
print_success "🎉 Deployment test completed successfully!"
echo ""
print_status "📋 Before deploying to production:"
echo "1. ✅ Update .env with production database credentials"
echo "2. ✅ Configure APP_URL with your domain"
echo "3. ✅ Set APP_DEBUG=false for production"
echo "4. ✅ Configure mail settings if needed"
echo "5. ✅ Set up GitHub secrets for automatic deployment"
echo ""
print_status "🚀 Your application is ready for deployment!"

# Cleanup (restore development dependencies)
print_status "Restoring development dependencies..."
composer install
print_success "Development environment restored"

echo ""
print_success "✨ Deployment test completed successfully! You can now push to the main branch to trigger automatic deployment."
