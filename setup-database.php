<?php

/**
 * Database Setup Script for Niger State Executive Project Monitoring Dashboard
 * 
 * This script helps set up the database by running migrations and seeders.
 * Run this script from the project root directory.
 */

echo "Niger State Executive Project Monitoring Dashboard - Database Setup\n";
echo "==================================================================\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "Error: This script must be run from the Laravel project root directory.\n";
    echo "Please navigate to the project directory and run: php setup-database.php\n";
    exit(1);
}

// Function to run artisan commands
function runArtisanCommand($command, $description) {
    echo "Running: {$description}\n";
    echo "Command: php artisan {$command}\n";
    
    $output = [];
    $returnCode = 0;
    
    exec("php artisan {$command} 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Success: {$description}\n";
        foreach ($output as $line) {
            echo "   {$line}\n";
        }
    } else {
        echo "❌ Failed: {$description}\n";
        foreach ($output as $line) {
            echo "   {$line}\n";
        }
        return false;
    }
    
    echo "\n";
    return true;
}

// Step 1: Clear config cache
echo "Step 1: Clearing configuration cache...\n";
runArtisanCommand("config:clear", "Clear configuration cache");

// Step 2: Check migration status and run migrations
echo "Step 2: Checking and running database migrations...\n";

// First, let's check if we need to reset migrations due to dependency issues
echo "Checking migration status...\n";
$output = [];
exec("php artisan migrate:status 2>&1", $output, $returnCode);

$needsReset = false;
foreach ($output as $line) {
    if (strpos($line, 'wards') !== false && strpos($line, 'Pending') !== false) {
        $needsReset = true;
        break;
    }
}

if ($needsReset) {
    echo "Detected migration dependency issues. Resetting migrations...\n";
    runArtisanCommand("migrate:reset", "Reset migrations");
}

if (!runArtisanCommand("migrate", "Run database migrations")) {
    echo "Migration failed. Trying to fix dependency issues...\n";

    // Try to run specific migrations in order
    echo "Running LGA migration first...\n";
    runArtisanCommand("migrate --path=database/migrations/2025_09_26_231900_create_lgas_table.php", "Create LGAs table");

    echo "Running Wards migration...\n";
    runArtisanCommand("migrate --path=database/migrations/2025_09_26_231910_create_wards_table.php", "Create Wards table");

    echo "Running remaining migrations...\n";
    if (!runArtisanCommand("migrate", "Run remaining migrations")) {
        echo "Migration failed. Please check your database configuration in .env file.\n";
        echo "Make sure your database exists and connection details are correct.\n";
        exit(1);
    }
}

// Step 3: Run seeders
echo "Step 3: Running database seeders...\n";
runArtisanCommand("db:seed", "Seed database with initial data");

// Step 4: Create storage link
echo "Step 4: Creating storage link for file uploads...\n";
runArtisanCommand("storage:link", "Create storage symbolic link");

// Step 5: Clear all caches
echo "Step 5: Clearing application caches...\n";
runArtisanCommand("cache:clear", "Clear application cache");
runArtisanCommand("route:clear", "Clear route cache");
runArtisanCommand("view:clear", "Clear view cache");

echo "🎉 Database setup completed successfully!\n\n";

echo "Next steps:\n";
echo "1. Start your development server: php artisan serve\n";
echo "2. Visit your application in the browser\n";
echo "3. The dashboard should now display with real data from Niger State LGAs and Wards\n\n";

echo "API Endpoints available:\n";
echo "- GET /api/projects - List all projects\n";
echo "- GET /api/lgas - List all Local Government Areas\n";
echo "- GET /api/wards - List all Wards\n";
echo "- GET /api/dashboard/stats - Dashboard statistics\n";
echo "- POST /api/projects/{id}/updates - Create project update\n";
echo "- POST /api/projects/{id}/attachments - Upload project files\n\n";

echo "If you encounter any issues:\n";
echo "1. Check your .env file for correct database configuration\n";
echo "2. Ensure your database server is running\n";
echo "3. Make sure you have proper file permissions for storage directory\n";
echo "4. Run 'composer install' if you haven't already\n";
echo "5. Run 'npm install && npm run build' for frontend assets\n\n";

echo "Happy coding! 🚀\n";
