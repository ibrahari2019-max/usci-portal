<?php
/**
 * Database configuration and PDO connection
 * Hardened with error handling, prepared statements, and session/CSRF bootstrap
 */

declare(strict_types=1);

// Start a secure session
session_start();

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database credentials – replace with real values or use environment variables
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'ucsi';
$DB_USER = getenv('DB_USER') ?: 'ucsi_user';
$DB_PASS = getenv('DB_PASS') ?: 'secure_password';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // In production you would log this error instead of displaying it
    http_response_code(500);
    exit('Database connection failed');
}

// Helper function to verify CSRF token
function verify_csrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
?>