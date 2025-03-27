<?php
/**
 * ReachOut World Media Network - Configuration File
 * Contains configuration settings for the website
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'reachout_media');
define('DB_USER', 'root');         // Change this to your database username
define('DB_PASS', '');             // Change this to your database password

// Site configuration
define('SITE_NAME', 'ReachOut World Media Network');
define('SITE_URL', 'http://localhost/rowmn'); // Change to your actual domain

// File paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// Time zone setting
date_default_timezone_set('UTC');

// Error reporting - set to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security settings
define('HASH_COST', 10); // For password hashing