<?php

/**
 * Quick Database Fix Script
 * 
 * This script fixes the specific issue with the wards table migration
 */

echo "Niger State Dashboard - Database Fix Script\n";
echo "==========================================\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "Error: This script must be run from the Laravel project root directory.\n";
    exit(1);
}

// Function to run artisan commands
function runCommand($command, $description) {
    echo "Running: {$description}\n";
    echo "Command: {$command}\n";
    
    $output = [];
    $returnCode = 0;
    
    exec("{$command} 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Success: {$description}\n";
    } else {
        echo "❌ Failed: {$description}\n";
        foreach ($output as $line) {
            echo "   {$line}\n";
        }
        return false;
    }
    
    foreach ($output as $line) {
        echo "   {$line}\n";
    }
    echo "\n";
    return true;
}

echo "Step 1: Resetting migrations to fix dependency issues...\n";
runCommand("php artisan migrate:reset", "Reset all migrations");

echo "Step 2: Running migrations in correct order...\n";
runCommand("php artisan migrate", "Run all migrations");

echo "Step 3: Seeding database with Niger State data...\n";
runCommand("php artisan db:seed", "Seed database");

echo "🎉 Database fix completed!\n\n";

echo "Now try accessing your API endpoints:\n";
echo "- http://your-domain/api/lgas\n";
echo "- http://your-domain/api/wards\n";
echo "- http://your-domain/api/projects\n\n";

echo "If you still have issues, check:\n";
echo "1. Database connection in .env file\n";
echo "2. Make sure MySQL/database server is running\n";
echo "3. Database user has proper permissions\n\n";
